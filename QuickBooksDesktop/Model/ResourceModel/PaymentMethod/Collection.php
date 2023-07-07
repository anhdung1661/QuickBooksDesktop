<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 15:57
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\PaymentMethod;

use Magenest\QuickBooksDesktop\Api\Data\PaymentMethodInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractQuickbooksEntityCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\PaymentMethod
 */
class Collection extends AbstractQuickbooksEntityCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = PaymentMethodInterface::ENTITY_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\PaymentMethod', 'Magenest\QuickBooksDesktop\Model\ResourceModel\PaymentMethod');
    }
}