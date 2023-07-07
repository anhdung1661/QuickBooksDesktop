<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 12/04/2020 17:01
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ProcessXmlResponse;

/**
 * Class ProcessItemInventoryResponse
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory
 */
class ProcessItemInventoryResponse extends ProcessXmlResponse implements ItemInventoryAddRes
{

    /**
     * @return string
     */
    public function getDetailName()
    {
        return self::DETAIL_NAME;
    }

    /**
     * @return array
     */
    public function processResponseFromQB()
    {
        if ($this->_itemsData == null) {
            throw new \RuntimeException(__('Not found Item data'));
        }
        return ProcessArray::getColValueFromThreeDimensional($this->_itemsData, [
            ItemInterface::ITEM_NAME => ItemInventoryAddRes::XML_ITEM_INVENTORY_NAME,
            ItemInterface::LIST_ID => ItemInventoryAddRes::XML_ITEM_INVENTORY_LIST_ID,
            ItemInterface::EDIT_SEQUENCE => ItemInventoryAddRes::XML_ITEM_INVENTORY_EDIT_SEQUENCE,
            ItemInterface::NOTE => ItemInventoryAddRes::XML_ITEM_INVENTORY_FULL_NAME,
        ], [
            ItemInterface::ITEM_TYPE => ItemInterface::ITEM_TYPE_ITEM_INVENTORY
        ]);
    }
}