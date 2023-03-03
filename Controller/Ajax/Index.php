<?php

namespace WMZ\OutOfStockNotifications\Controller\Ajax;

use Exception;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\ScopeInterface;
use WMZ\OutOfStockNotifications\Api\Data\OutOfStockNotificationsDataInterfaceFactory;
use WMZ\OutOfStockNotifications\Api\OutOfStockNotificationsRepositoryInterface;
use WMZ\OutOfStockNotifications\Model\ResourceModel\Notifications\CollectionFactory as NotificationsCollection;

class Index implements ActionInterface
{
    /**
     * Success Message Configuration Path
     */
    const XML_PATH_SUCCESS_MSG = 'notification_options/configuration/success_msg';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @var NotificationsCollection
     */
    private $notificationsCollectionFactory;

    /**
     * @var OutOfStockNotificationsDataInterfaceFactory
     */
    private $outOfStockNotificationsData;

    /**
     * @var OutOfStockNotificationsRepositoryInterface
     */
    private $outOfStockNotificationsRepository;

    /**
     * Index constructor.
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param NotificationsCollection $notificationsCollectionFactory
     * @param OutOfStockNotificationsDataInterfaceFactory $outOfStockNotificationsData
     * @param OutOfStockNotificationsRepositoryInterface $outOfStockNotificationsRepository
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfigInterface,
        NotificationsCollection $notificationsCollectionFactory,
        OutOfStockNotificationsDataInterfaceFactory $outOfStockNotificationsData,
        OutOfStockNotificationsRepositoryInterface $outOfStockNotificationsRepository
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->notificationsCollectionFactory = $notificationsCollectionFactory;
        $this->outOfStockNotificationsData = $outOfStockNotificationsData;
        $this->outOfStockNotificationsRepository = $outOfStockNotificationsRepository;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->request->isAjax()) {
            try {
                $productId = $this->request->getParam('product_id');
                $customerEmail = $this->request->getParam('email');
                $storeScope = ScopeInterface::SCOPE_STORE;
                $successMsg = $this->scopeConfigInterface
                    ->getValue(self::XML_PATH_SUCCESS_MSG, $storeScope);

                $customerEmailsCollection = $this->notificationsCollectionFactory->create()
                    ->addFieldToFilter('customer_email', $customerEmail)
                    ->addFieldToFilter('product_id', $productId);

                if (count($customerEmailsCollection) > 0) {
                    return $result->setData(
                        ['success' => false, 'msg' => 'You are already subscribed to be notified.']
                    );
                }

                $outOfStock = $this->outOfStockNotificationsData->create();
                $outOfStock->setCustomerEmail($customerEmail);
                $outOfStock->setProductId($productId);
                $outOfStock->setStatus(1);
                $outOfStock->setSubscribedAt(date('Y-m-d H:i:s'));

                $this->outOfStockNotificationsRepository->save($outOfStock);

            } catch (Exception $e) {
                return $result->setData(['success' => false, 'msg' => 'Error : ' . $e->getMessage()]);
            }
        }
        return $result->setData(['success' => true, 'msg' => $successMsg]);
    }
}
