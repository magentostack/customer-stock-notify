<?php

namespace WMZ\OutOfStockNotifications\Api\Data;

interface OutOfStockNotificationsDataInterface
{
    const ENTITY_ID = 'entity_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const PRODUCT_ID = 'product_id';
    const SUBSCRIBED_AT = 'subscribed_at';
    const STATUS = 'status';

    /**
     * @param int $entityId
     */
    public function setEntityId(int $entityId): void;

    /**
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * @param string $email
     */
    public function setCustomerEmail(string $email): void;

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void;

    /**
     * @return int|null
     */
    public function getProductId(): ?int;

    /**
     * @param string $subscribedAt
     */
    public function setSubscribedAt(string $subscribedAt): void;

    /**
     * @return string|null
     */
    public function getSubscribedAt(): ?string;

    /**
     * @param int $status
     */
    public function setStatus(int $status): void;

    /**
     * @return int|null
     */
    public function getStatus(): ?int;
}
