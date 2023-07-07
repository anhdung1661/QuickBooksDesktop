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

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrderLineItem;

use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrderLineItem
 */
class Collection extends AbstractCollection
{
    const TABLE_JOIN_NAME = 'sales_order_info';

    /**
     * @inheritDoc
     */
    protected $_idFieldName = SalesOrderLineItemInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\SalesOrderLineItem', 'Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrderLineItem');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getMainTable())->joinLeft(
            [self::TABLE_JOIN_NAME => $this->getTable(SalesOrderInterface::TABLE_NAME)],
            sprintf(
                '%s.%s = %s.%s',
                $this->getMainTable(),
                SalesOrderLineItemInterface::ORDER_TXN_ID,
                self::TABLE_JOIN_NAME,
                SalesOrderInterface::LIST_ID
            ), [
                SalesOrderInterface::MAGENTO_ID
            ]
        )->where(self::TABLE_JOIN_NAME . '.' .SalesOrderInterface::COMPANY_ID . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }

    /**
     * @param $orderId
     * @return Collection
     */
    public function filterByOrderId($orderId)
    {
        return $this->addFieldToFilter(SalesOrderInterface::MAGENTO_ID, ['eq' => $orderId]);
    }
}