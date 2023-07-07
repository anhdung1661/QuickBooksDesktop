<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 15/04/2020 14:59
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Item
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class Item extends AbstractQuickbooksEntity
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ItemInterface::TABLE_NAME, ItemInterface::ENTITY_ID);
    }

    /**
     * @param $itemsData
     * @return $this
     * @throws LocalizedException
     */
    public function saveItems($itemsData)
    {
        $this->saveQuickbooksEntity($this->getMainTable(), $itemsData);

        return $this;
    }
}