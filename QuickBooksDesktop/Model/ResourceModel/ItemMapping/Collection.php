<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 15/04/2020 15:46
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\ItemMapping;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractMappingCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\ItemMapping
 */
class Collection extends AbstractMappingCollection
{
    const TABLE_JOIN_NAME = 'itemQb';

    /**
     * @inheritDoc
     */
    protected $_idFieldName = ItemMappingInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ItemMapping', 'Magenest\QuickBooksDesktop\Model\ResourceModel\ItemMapping');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getMainTable())->joinLeft(
            [self::TABLE_JOIN_NAME => $this->getTable(ItemInterface::TABLE_NAME)],
            sprintf(
                '%s.%s = %s.%s',
                $this->getMainTable(),
                ItemMappingInterface::QB_ITEM_ID,
                self::TABLE_JOIN_NAME,
                ItemInterface::ENTITY_ID
            )
        )->where(self::TABLE_JOIN_NAME . '.' . ItemInterface::COMPANY_ID . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }

    /**
     * @param $field
     * @param null $condition
     * @return Collection
     */
    public function filterByQuickbooksItemInfo($field, $condition = null)
    {
        $itemColumns = [ItemInterface::ITEM_NAME, ItemInterface::ITEM_TYPE, ItemInterface::LIST_ID, ItemInterface::EDIT_SEQUENCE, ItemInterface::NOTE, ItemInterface::ENTITY_ID];

        if (is_array($field)) {
            foreach ($field as $key => $value) {
                if (in_array($value, $itemColumns)) {
                    $field[$key] = self::TABLE_JOIN_NAME . '.' . $value;
                }
            }

        } else {
            if (in_array($field, $itemColumns)) {
                $field = self::TABLE_JOIN_NAME . '.' . $field;
            }
        }

        return $this->addFieldToFilter($field, $condition);
    }
}