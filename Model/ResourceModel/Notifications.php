<?php

namespace WMZ\OutOfStockNotifications\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Notifications extends AbstractDb
{
    private const TABLE_NAME = 'WMZ_outofstock_notification_requests';

    private const PRIMARY_KEY = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }
}
