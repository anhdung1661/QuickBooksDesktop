<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 15/10/2020 14:37
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory;

/**
 * Update Quantity on Hand when update Product
 *
 * Interface InventoryAdjustmentAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory
 */
interface InventoryAdjustmentAddReq
{
    const XML_INVENTORY_ADJUSTMENT_ADD = 'InventoryAdjustmentAdd';

    const XML_INVENTORY_ADJUSTMENT_LINE_ADD = 'InventoryAdjustmentLineAdd';

    const XML_INVENTORY_ADJUSTMENT_ACCOUNT_REF = ['tag_name' => ['AccountRef', 'FullName'], 'value_length' => null];

    const XML_INVENTORY_ADJUSTMENT_ITEM_REF = ['tag_name' => ['ItemRef', 'FullName'], 'value_length' => null];

    const XML_INVENTORY_ADJUSTMENT_QTY_ADJUSTMENT = ['tag_name' => ['QuantityAdjustment', 'NewQuantity'], 'value_length' => null];
}