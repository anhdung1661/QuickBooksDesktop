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
 * @time: 25/09/2020 13:09
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;

/**
 * Class SalesOrder
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class SalesOrder extends AbstractQuickbooksEntity
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SalesOrderInterface::TABLE_NAME, SalesOrderInterface::ENTITY_ID);
    }

    /**
     * @param $salesOrdersData
     * @return $this
     */
    public function saveSalesOrders($salesOrdersData)
    {
        $this->saveQuickbooksEntity(SalesOrderInterface::TABLE_NAME, $salesOrdersData);

        return $this;
    }
}