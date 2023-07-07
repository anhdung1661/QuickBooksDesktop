<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 15/10/2020 13:28
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory;

/**
 * Interface ItemInventoryModReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory
 */
interface ItemInventoryModReq
{
    const XML_ITEM_INVENTORY_MOD = 'ItemInventoryMod';

    const XML_ITEM_INVENTORY_MOD_LIST_ID = 'ListID';

    const XML_ITEM_INVENTORY_MOD_EDIT_SEQUENCE = 'EditSequence';
}