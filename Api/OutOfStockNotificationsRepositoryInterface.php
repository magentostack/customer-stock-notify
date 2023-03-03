<?php

namespace WMZ\OutOfStockNotifications\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use WMZ\OutOfStockNotifications\Api\Data\OutOfStockNotificationsDataInterface;

interface OutOfStockNotificationsRepositoryInterface
{
    /**
     * @param OutOfStockNotificationsDataInterface $notification
     * @return OutOfStockNotificationsDataInterface
     * @throws CouldNotSaveException
     */
    public function save(OutOfStockNotificationsDataInterface $notification);

    /**
     * @param int $outOfStockNotificationId
     * @return OutOfStockNotificationsDataInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $outOfStockNotificationId);

    /**
     * @param OutOfStockNotificationsDataInterface $outOfStockNotification
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(OutOfStockNotificationsDataInterface $outOfStockNotification);

    /**
     * @param int $outOfStockNotificationId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $outOfStockNotificationId);
}
