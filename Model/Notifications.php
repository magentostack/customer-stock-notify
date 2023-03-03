<?php

namespace WMZ\OutOfStockNotifications\Model;

use Magento\Framework\Model\AbstractModel;
use WMZ\OutOfStockNotifications\Api\Data\OutOfStockNotificationsDataInterface;

class Notifications extends AbstractModel implements OutOfStockNotificationsDataInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Notifications::class);
    }

    /**
     * @param int $entityId
     */
    public function setEntityId($entityId): void
    {
        $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param string $email
     */
    public function setCustomerEmail(string $email): void
    {
        $this->setData(self::CUSTOMER_EMAIL, $email);
    }

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void
    {
        $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @param string $subscribedAt
     */
    public function setSubscribedAt(string $subscribedAt): void
    {
        $this->setData(self::SUBSCRIBED_AT, $subscribedAt);
    }

    /**
     * @return string|null
     */
    public function getSubscribedAt(): ?string
    {
        return $this->getData(self::SUBSCRIBED_AT);
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->getData(self::STATUS);
    }
}
