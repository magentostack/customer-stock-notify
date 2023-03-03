<?php

namespace WMZ\OutOfStockNotifications\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupsCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroups implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var GroupsCollectionFactory
     */
    private $groupsFactory;

    public function __construct(
        GroupsCollectionFactory $groupsFactory
    ) {
        $this->groupsFactory = $groupsFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $customerGroups = $this->groupsFactory->create();
            $this->options = $customerGroups->load()->toOptionArray();
        }
        return $this->options;
    }
}
