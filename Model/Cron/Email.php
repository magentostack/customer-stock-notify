<?php

namespace WMZ\OutOfStockNotifications\Model\Cron;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ProductImageLoader;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use WMZ\OutOfStockNotifications\Api\OutOfStockNotificationsRepositoryInterface;
use WMZ\OutOfStockNotifications\Helper\Data;
use WMZ\OutOfStockNotifications\Model\ResourceModel\Notifications\CollectionFactory as NotifsCollection;

class Email
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var NotifsCollection
     */
    private $notifsCollection;

    /**
     * @var ProductImageLoader
     */
    private $productImageLoader;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OutOfStockNotificationsRepositoryInterface
     */
    private $outOfStockNotificationsRepository;

    /**
     * Email constructor.
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param NotifsCollection $notifsCollection
     * @param ProductImageLoader $productImageLoader
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param OutOfStockNotificationsRepositoryInterface $outOfStockNotificationsRepository
     */
    public function __construct(
        Data $helperData,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        NotifsCollection $notifsCollection,
        ProductImageLoader $productImageLoader,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        OutOfStockNotificationsRepositoryInterface $outOfStockNotificationsRepository
    ) {
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->productImageLoader = $productImageLoader;
        $this->notifsCollection = $notifsCollection;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->outOfStockNotificationsRepository = $outOfStockNotificationsRepository;
    }

    /**
     * @return void
     */
    public function sendEmails()
    {
        $col = $this->notifsCollection->create()
            ->addFieldToFilter('status', 1);

        if (count($col) > 0 && $this->helperData->getNotificationsEnable() == true) {
            foreach ($col as $row) {
                $sent = $this->sendActualEmail($row->getData());
                if ($sent) {
                    $this->outOfStockNotificationsRepository->deleteById(
                        (int)$row->getData('entity_id')
                    );
                }
            }
        }
    }

    /**
     * @param $emailData
     * @return bool
     */
    private function sendActualEmail($emailData)
    {
        try {
            $productId = $emailData['product_id'];
            $product = $this->productRepository->getById((int)$productId);
            if ($product->isAvailable() === false) {
                return false;
            }

            $productName = $product->getName();
            $productSku = $product->getSku();
            $productImageAttr = $product->getCustomAttribute('image');
            $productUrl = $product->getProductUrl();

            /** @var Product $product */
            $productImage = $this->productImageLoader
                ->init($product, 'image')
                ->setImageFile($productImageAttr->getValue());
            $productImgUrl = str_replace('/pub', '', $productImage->getUrl());

            $customerEmail = $emailData['customer_email'];

            try {
                $customerObj = $this->customerRepository->get(
                    $customerEmail,
                    $this->storeManager->getDefaultStoreView()->getWebsiteId()
                );
                $customerName = $customerObj->getFirstName();
            } catch (NoSuchEntityException $exception) {
                $customerName = 'Guest';
            }

            $storeName = $this->storeManager->getStore()->getName();
            $storeEmail = $this->scopeConfig->getValue(
                'trans_email/ident_support/email',
                ScopeInterface::SCOPE_STORE
            );

            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
            ];

            $templateVars = [
                'store' => $this->storeManager->getStore(),
                'storename' => $this->storeManager->getStore()->getName(),
                'product_sku' => $productSku,
                'product_name' => $productName,
                'product_url' => $productUrl,
                'product_img_url' => $productImgUrl,
                'customer_name' => $customerName,
                'message' => 'You subscribed to the below product\'s in-stock notification.
             This is to notify you that the product is back in stock now.',
            ];
            $from = ['email' => $storeEmail, 'name' => $storeName];
            $this->inlineTranslation->suspend();
            $to = $customerEmail;

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('oos_notification_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFromByScope($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();

            $this->inlineTranslation->resume();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
