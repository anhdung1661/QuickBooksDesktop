<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 15/04/2020 15:05
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\Item;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\Item
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = ItemInterface::ENTITY_ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\Item', 'Magenest\QuickBooksDesktop\Model\ResourceModel\Item');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])->where(ItemInterface::COMPANY_ID . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }
}