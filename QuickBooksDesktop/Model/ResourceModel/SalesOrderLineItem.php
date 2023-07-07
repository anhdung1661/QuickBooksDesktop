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

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class SalesOrderLineItem
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class SalesOrderLineItem extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SalesOrderLineItemInterface::TABLE_NAME, SalesOrderLineItemInterface::ENTITY_ID);
    }

    /**
     * @param $salesOrderLineItemsData
     * @return int
     */
    public function saveSalesOrderLineItems($salesOrderLineItemsData)
    {
        $row = reset($salesOrderLineItemsData);
        if (!is_array($row)) {
            return $this->getConnection()->insert($this->getTable(SalesOrderLineItemInterface::TABLE_NAME), $salesOrderLineItemsData);

        }
        unset($row);

        return $this->getConnection()->insertOnDuplicate(
            $this->getTable(SalesOrderLineItemInterface::TABLE_NAME),
            $salesOrderLineItemsData,
            [SalesOrderLineItemInterface::ITEM_LIST_ID, SalesOrderLineItemInterface::TXN_LINE_ID, SalesOrderLineItemInterface::ITEM_SKU]
        );
    }
}