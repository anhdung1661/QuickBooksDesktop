<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 11:06
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\ShippingMethod;

use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\ShippingMethod
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = ShippingMethodInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ShippingMethod', 'Magenest\QuickBooksDesktop\Model\ResourceModel\ShippingMethod');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])->where(ShippingMethodInterface::COMPANY_ID . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }
}