<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 09:17
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class CustomerMapping
 * @package Magenest\QuickBooksDesktop\Model
 */
class CustomerMapping extends AbstractModel
{
    /**
     * @inheritDoc
     */
    protected $_eventPrefix = CustomerMappingInterface::TABLE_NAME;

    /**
     * @var CustomerCollection
     */
    protected $_customerCollection;

    /**
     * @var array
     */
    private $customersMappingData;

    /**
     * CustomerMapping constructor.
     * @param Collection $customerCollection
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        CustomerCollection $customerCollection,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_customerCollection = $customerCollection;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping');
    }

    /**
     * @param array $mappingData
     * @return $this
     */
    public function setCustomerMapping($customerMappingData)
    {
        if (!empty($customerMappingData)) {
            $listCustomerId = array_column($customerMappingData, CustomerInterface::LIST_ID);
            $listCustomer = $this->_customerCollection->create()
                ->addFieldToFilter(CustomerInterface::LIST_ID, ['in' => $listCustomerId])
                ->addFieldToSelect([CustomerInterface::ENTITY_ID, CustomerInterface::LIST_ID])
                ->getData();

            $this->customersMappingData = ProcessArray::getColValueFromThreeDimensional(
                ProcessArray::mergeArrayThreeD($listCustomer, $customerMappingData, CustomerInterface::LIST_ID),
                [
                    CustomerMappingInterface::QB_ID => CustomerInterface::ENTITY_ID,
                    CustomerMappingInterface::M2_ENTITY_ID => CustomerMappingInterface::M2_ENTITY_ID,
                    CustomerMappingInterface::M2_ENTITY_TYPE => CustomerMappingInterface::M2_ENTITY_TYPE
                ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function saveMapping()
    {
        if ($this->customersMappingData != null) {
            $this->getResource()->saveMultiRows($this->customersMappingData);
        }
        return $this;
    }
}