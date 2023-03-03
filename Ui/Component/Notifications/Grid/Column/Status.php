<?php

namespace WMZ\OutOfStockNotifications\Ui\Component\Notifications\Grid\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['status'])) {
                    $status = $item['status'] == 2 ? 'Notification Sent' : 'Notification not sent';
                    $item['status'] = __($status);
                }
            }
        }

        return $dataSource;
    }
}
