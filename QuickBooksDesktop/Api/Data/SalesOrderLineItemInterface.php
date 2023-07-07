<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 * @time: 25/09/2020 13:21
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Store Line Item Information of Sales Order
 *
 * Interface SalesOrderLineItemInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface SalesOrderLineItemInterface
{
    const TABLE_NAME = 'magenest_qbd__sales_order_line_items';

    const ENTITY_ID = 'id';

    const COMPANY_ID = 'company_id';

    const ORDER_TXN_ID = 'order_txn_id';

    const TXN_LINE_ID = 'txn_line_id';

    const ITEM_LIST_ID = 'item_list_id';

    const ITEM_SKU = 'sku';
}