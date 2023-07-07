<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model\ResourceModel\Taxes;

use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractQuickbooksEntityCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksOnline\Model\ResourceModel\Category
 */
class Collection extends AbstractQuickbooksEntityCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = TaxesInterface::ENTITY_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\Taxes', 'Magenest\QuickBooksDesktop\Model\ResourceModel\Taxes');
    }
}
