<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 20/10/2020 09:53
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ProcessXmlResponse;

/**
 * Class ProcessItemNonInventoryResponse
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory
 */
class ProcessItemNonInventoryResponse extends ProcessXmlResponse implements ItemNonInventoryAddRes
{
    /**
     * @inheritDoc
     */
    public function processResponseFromQB()
    {
        if ($this->_itemsData == null) {
            throw new \RuntimeException(__('Not found Item data'));
        }
        return ProcessArray::getColValueFromThreeDimensional($this->_itemsData, [
            ItemInterface::ITEM_NAME => self::XML_ITEM_NON_INVENTORY_NAME,
            ItemInterface::LIST_ID => self::XML_ITEM_NON_INVENTORY_LIST_ID,
            ItemInterface::EDIT_SEQUENCE => self::XML_ITEM_NON_INVENTORY_EDIT_SEQUENCE,
            ItemInterface::NOTE => self::XML_ITEM_NON_INVENTORY_FULL_NAME,
        ], [
            ItemInterface::ITEM_TYPE => ItemInterface::ITEM_TYPE_ITEM_NONE_INVENTORY
        ]);
    }
}