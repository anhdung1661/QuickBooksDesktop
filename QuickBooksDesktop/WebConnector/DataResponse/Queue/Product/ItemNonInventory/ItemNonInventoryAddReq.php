<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 20/10/2020 09:18
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory;

/**
 * Interface ItemNonInventoryAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory
 */
interface ItemNonInventoryAddReq
{
    const XML_ITEM_NON_INVENTORY_ADD = 'ItemNonInventoryAdd';

    const XML_ITEM_NON_INVENTORY_NAME = ['tag_name' => 'Name', 'value_length' => 31];

    const XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE = 'SalesOrPurchase';

    const XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_DESC = ['tag_name' => 'Desc', 'value_length' => 4095];

    const XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_PRICE = ['tag_name' => 'Price', 'value_length' => null];

    const XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_ACCOUNT_REF = ['tag_name' => ['AccountRef', 'FullName'], 'value_length' => 159];
}