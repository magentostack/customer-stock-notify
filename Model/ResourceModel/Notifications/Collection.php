<?php

namespace WMZ\OutOfStockNotifications\Model\ResourceModel\Notifications;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WMZ\OutOfStockNotifications\Model\Notifications as NotificationsModel;
use WMZ\OutOfStockNotifications\Model\ResourceModel\Notifications as NotificationsResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            NotificationsModel::class,
            NotificationsResourceModel::class
        );
    }
}
