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
 * @time: 25/09/2020 13:08
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SalesOrder
 * @package Magenest\QuickBooksDesktop\Model
 */
class SalesOrder extends AbstractQuickbooksEntity
{
    /**
     * @inheritDoc
     */
    protected $_eventPrefix = SalesOrderInterface::TABLE_NAME;

    /**
     * @var array
     */
    protected $_salesOrderData = [];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrder');
    }

    /**
     * @param $salesOrderData
     * @return $this
     */
    public function setSalesOrderData($salesOrderData)
    {
        $this->_salesOrderData = ProcessArray::insertColumnToThreeDimensional($salesOrderData, [
            SalesOrderInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if (empty($this->_salesOrderData)) {
            return parent::save();
        }
        $this->getResource()->saveSalesOrders($this->_salesOrderData);

        return $this;
    }
}