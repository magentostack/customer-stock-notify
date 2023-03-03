<?php

namespace WMZ\OutOfStockNotifications\Model\Repository;

use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use WMZ\OutOfStockNotifications\Api\Data\OutOfStockNotificationsDataInterface;
use WMZ\OutOfStockNotifications\Api\Data\OutOfStockNotificationsDataInterfaceFactory;
use WMZ\OutOfStockNotifications\Api\OutOfStockNotificationsRepositoryInterface;
use WMZ\OutOfStockNotifications\Model\ResourceModel\Notifications as NotificationResource;

class OutOfStockNotifications implements OutOfStockNotificationsRepositoryInterface
{
    /**
     * @var OutOfStockNotificationsDataInterfaceFactory
     */
    private $notificationFactory;

    /**
     * @var NotificationResource
     */
    private $notificationResource;

    /**
     * OutOfStockNotifications constructor.
     * @param NotificationResource $notificationsResouce
     * @param OutOfStockNotificationsDataInterfaceFactory $notificationFactory
     */
    public function __construct(
        NotificationResource $notificationsResouce,
        OutOfStockNotificationsDataInterfaceFactory $notificationFactory
    ) {
        $this->notificationResource = $notificationsResouce;
        $this->notificationFactory = $notificationFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(OutOfStockNotificationsDataInterface $notification)
    {
        try {
            $this->notificationResource->save($notification);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Unable to save new reason. Error: %1', $e->getMessage()));
        }
        return $notification;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $outOfStockNotificationId)
    {
        $notification = $this->notificationFactory->create();
        $this->notificationResource->load($notification, $outOfStockNotificationId);
        if (!$notification->getEntityId()) {
            throw new NoSuchEntityException(__('Message with specified ID "%1" not found.', $outOfStockNotificationId));
        }

        return $notification;
    }

    /**
     * @inheritDoc
     */
    public function delete(OutOfStockNotificationsDataInterface $outOfStockNotification)
    {
        try {
            $this->notificationResource->delete($outOfStockNotification);
        } catch (Exception $e) {
            if ($outOfStockNotification->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove notification with ID %1. Error: %2',
                        [$outOfStockNotification->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove notification. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $outOfStockNotificationId)
    {
        $outOfStockNotification = $this->getById($outOfStockNotificationId);
        $this->delete($outOfStockNotification);
        return true;
    }
}
