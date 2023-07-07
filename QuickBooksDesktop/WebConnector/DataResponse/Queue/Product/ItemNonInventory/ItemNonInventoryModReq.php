<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 20/10/2020 09:45
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory;

/**
 * Class ItemNonInventoryModReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory
 */
interface ItemNonInventoryModReq
{
    const XML_ITEM_NON_INVENTORY_MOD = 'ItemNonInventoryMod';

    const XML_ITEM_NON_INVENTORY_MOD_LIST_ID = 'ListID';

    const XML_ITEM_NON_INVENTORY_MOD_EDIT_SEQUENCE = 'EditSequence';

    const XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_MOD = 'SalesOrPurchaseMod';
}