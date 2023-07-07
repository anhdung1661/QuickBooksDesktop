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
 * @time: 25/09/2020 13:10
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrder;

use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractQuickbooksEntityCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrder
 */
class Collection extends AbstractQuickbooksEntityCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = SalesOrderInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\SalesOrder', 'Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrder');
    }

    /**
     * @inheritDoc
     */
    protected function getCompanyIdFieldName()
    {
        return SalesOrderInterface::COMPANY_ID;
    }
}