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
 * @time: 29/09/2020 16:33
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice;

use Magenest\QuickBooksDesktop\Api\Data\InvoiceInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractQuickbooksEntityCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice
 */
class Collection extends AbstractQuickbooksEntityCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = InvoiceInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\Invoice', 'Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice');
    }

    /**
     * @inheritDoc
     */
    protected function getCompanyIdFieldName()
    {
        return InvoiceInterface::COMPANY_ID;
    }
}