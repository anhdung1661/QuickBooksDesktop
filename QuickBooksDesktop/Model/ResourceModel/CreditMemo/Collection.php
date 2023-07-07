<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 30/09/2020 15:48
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\CreditMemo;

use Magenest\QuickBooksDesktop\Api\Data\CreditMemoInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractQuickbooksEntityCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\CreditMemo
 */
class Collection extends AbstractQuickbooksEntityCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = CreditMemoInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\CreditMemo', 'Magenest\QuickBooksDesktop\Model\ResourceModel\CreditMemo');
    }

    /**
     * @inheritDoc
     */
    protected function getCompanyIdFieldName()
    {
        return CreditMemoInterface::COMPANY_ID;
    }
}