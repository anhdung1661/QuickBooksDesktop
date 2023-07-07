<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 20/10/2020 09:52
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory;

/**
 * Interface ItemNonInventoryAddRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory
 */
interface ItemNonInventoryAddRes
{
    const DETAIL_NAME = 'ItemNonInventoryRet';

    const XML_ITEM_NON_INVENTORY_LIST_ID = 'ListID';

    const XML_ITEM_NON_INVENTORY_EDIT_SEQUENCE = 'EditSequence';

    const XML_ITEM_NON_INVENTORY_NAME = 'Name';

    const XML_ITEM_NON_INVENTORY_FULL_NAME = 'FullName';

    const XML_ITEM_NON_INVENTORY_TXN_NUMBER = ['Detail', 'TxnNumber'];
}