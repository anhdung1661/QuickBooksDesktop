<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 25/09/2020 13:46
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class SalesOrderLineItem
 * @package Magenest\QuickBooksDesktop\Model
 */
class SalesOrderLineItem extends AbstractModel
{
    /**
     * @inherit
     */
    protected $_eventPrefix = SalesOrderLineItemInterface::TABLE_NAME;

    /**
     * @var array
     */
    private $salesOrderLineItem = [];

    /**
     * @var ResourceModel\Company\CollectionFactory
     */
    protected $_companyCollection;

    /**
     * SalesOrderLineItem constructor.
     * @param ResourceModel\Company\CollectionFactory $companyCollection
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory $companyCollection,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_companyCollection = $companyCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrderLineItem');
    }

    /**
     * @param $salesOrderLineItemData
     * @return $this
     */
    public function setSalesOrderLineItem($salesOrderLineItemData)
    {
        if (!empty($salesOrderLineItemData)) {
            $this->salesOrderLineItem = ProcessArray::insertColumnToThreeDimensional($salesOrderLineItemData, [
                SalesOrderLineItemInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
            ]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if (empty($this->salesOrderLineItem)) {
            return parent::save();
        }
        $this->getResource()->saveSalesOrderLineItems($this->salesOrderLineItem);

        return $this;
    }
}