<?php

namespace WMZ\OutOfStockNotifications\Block;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use WMZ\OutOfStockNotifications\Helper\Data as HelperData;

class Notifier extends Template
{
    /**
     * @var CustomerSessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * Notifier constructor.
     * @param Context $context
     * @param CustomerSessionFactory $customerSessionFactory
     * @param Registry $registry
     * @param FormKey $formKey
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSessionFactory $customerSessionFactory,
        Registry $registry,
        FormKey $formKey,
        HelperData $helperData,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->formKey = $formKey;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return string
     */
    public function getNotificationMessage(): string
    {
        return $this->helperData->getNotificationMessage();
    }

    /**
     * @return bool
     */
    public function displayInputField(): bool
    {
        /** @var Product $product */
        $product = $this->getCurrentProduct();

        if ($product->isAvailable() === false) {
            $moduleNotificationsEnabled = $this->helperData->getNotificationsEnable();

            $customerGroupId = 0;

            $customerSession = $this->customerSessionFactory->create();
            if ($customerSession->isLoggedIn()) {
                $customerGroupId = $customerSession->getCustomer()->getGroupId();
            }

            if ($this->helperData->getCustomerNotification() === null) {
                return false;
            }

            $moduleEnabledCustomerGroups = $this->helperData->getCustomerNotification();
            $customerGroupEnabled = in_array($customerGroupId, explode(',', $moduleEnabledCustomerGroups));

            if ($moduleNotificationsEnabled == 1 && $customerGroupEnabled == 1) {
                return true;
            }
        }

        return false;
    }
}
