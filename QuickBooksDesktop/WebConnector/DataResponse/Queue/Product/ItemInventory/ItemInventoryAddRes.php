<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 07/04/2020 01:58
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory;

/**
 * Interface ItemInventoryAddRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory
 */
interface ItemInventoryAddRes
{
    const DETAIL_NAME = 'ItemInventoryRet';

    const XML_ITEM_INVENTORY_LIST_ID = 'ListID';

    const XML_ITEM_INVENTORY_EDIT_SEQUENCE = 'EditSequence';

    const XML_ITEM_INVENTORY_NAME = 'Name';

    const XML_ITEM_INVENTORY_FULL_NAME = 'FullName';

    const XML_ITEM_INVENTORY_TXN_NUMBER = ['Detail', 'TxnNumber'];
}