<?php

namespace WMZ\OutOfStockNotifications\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data
{
    private const XML_PATH_ENABLED_NOTIF = 'notification_options/configuration/enabled';
    private const XML_PATH_ENABLED_GROUPS = 'notification_options/configuration/customer_groups';
    private const XML_PATH_NOTIF_MSG = 'notification_options/configuration/notification_msg';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * Data constructor.
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * @return mixed
     */
    public function getNotificationsEnable()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfigInterface->getValue(self::XML_PATH_ENABLED_NOTIF, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getNotificationMessage()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfigInterface->getValue(self::XML_PATH_NOTIF_MSG, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getCustomerNotification()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfigInterface->getValue(self::XML_PATH_ENABLED_GROUPS, $storeScope);
    }
}
