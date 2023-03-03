<?php

namespace WMZ\OutOfStockNotifications\Ui\Component\Notifications\Grid\Column;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductName extends Column
{
    /**
     * Product edit page initial URL
     */
    const EDIT_URL = 'catalog/product/edit/id/';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var mixed|string
     */
    private $editUrl;

    /**
     * ProductName constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Product $product
     * @param ProductRepositoryInterface $productRepository
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ProductCollectionFactory $productCollectionFactory,
        Product $product,
        ProductRepositoryInterface $productRepository,
        array $components = [],
        array $data = [],
        $editUrl = self::EDIT_URL
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->product = $product;
        $this->productRepository = $productRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');

                if (isset($item['product_id'])) {
                    $productCollection = $this->productCollectionFactory->create()
                        ->addAttributeToFilter('entity_id', $item['product_id'])
                        ->load();

                    $data = $productCollection->getData();

                    if (!empty($data)) {
                        $productId = $data[0]['entity_id'];
                        $productLoaded = $this->productRepository->getById((int)$productId);

                        $item[$name]['edit'] = [
                            'href' => $this->urlBuilder->getUrl(
                                $this->editUrl,
                                ['id' => $item['product_id']]
                            ),
                            'label' => $productLoaded->getName(),
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
