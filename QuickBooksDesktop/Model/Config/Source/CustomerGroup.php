<?php

namespace Magenest\QuickBooksDesktop\Model\Config\Source;

/**
 * Class CustomerGroup
 * @package Magenest\QuickBooksDesktop\Model\Config\Source
 */
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroupCollection;

    /**
     * CustomerGroup constructor.
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}