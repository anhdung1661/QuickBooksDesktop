<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory;

/**
 * Interface ItemInventoryAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory
 */
interface ItemInventoryAddReq
{
    /**#@+
     * Constants defined by QBWC
     */
    const XML_ITEM_INVENTORY_ADD = 'ItemInventoryAdd';

    const XML_ITEM_INVENTORY_NAME = ['tag_name' => 'Name', 'value_length' => 31];

    const XML_ITEM_INVENTORY_MANUFACTURER_PART_NUMBER = ['tag_name' => 'ManufacturerPartNumber', 'value_length' => 31];

    const XML_ITEM_INVENTORY_SALES_TAX_CODE_LIST_ID = ['SalesTaxCodeRef', 'ListID'];

    const XML_ITEM_INVENTORY_SALES_TAX_CODE_FULL_NAME = ['tag_name' => ['SalesTaxCodeRef', 'FullName'], 'value_length' => 3];

    const XML_ITEM_INVENTORY_SALES_DESC = ['tag_name' => 'SalesDesc', 'value_length' => 4095];

    const XML_ITEM_INVENTORY_SALES_PRICE = 'SalesPrice';

    const XML_ITEM_INVENTORY_INCOME_ACCOUNT_LIST_ID = ['IncomeAccountRef', 'ListID'];

    const XML_ITEM_INVENTORY_INCOME_ACCOUNT_FULL_NAME = ['tag_name' => ['IncomeAccountRef', 'FullName'], 'value_length' => 159];

    const XML_ITEM_INVENTORY_PURCHASE_DESC = ['tag_name' => 'PurchaseDesc', 'value_length' => 4095];

    const XML_ITEM_INVENTORY_PURCHASE_COST = 'PurchaseCost';

    const XML_ITEM_INVENTORY_PURCHASE_TAX_CODE_LIST_ID = ['PurchaseTaxCodeRef', 'ListID'];

    const XML_ITEM_INVENTORY_PURCHASE_TAX_CODE_FULL_NAME = ['tag_name' => ['PurchaseTaxCodeRef', 'FullName'], 'value_length' => 3];

    const XML_ITEM_INVENTORY_COGS_ACCOUNT_LIST_ID = ['COGSAccountRef', 'ListID'];

    const XML_ITEM_INVENTORY_COGS_ACCOUNT_FULL_NAME = ['tag_name' => ['COGSAccountRef', 'FullName'], 'value_length' => 159];

    const XML_ITEM_INVENTORY_PREF_VENDOR_LIST_ID = ['PrefVendorRef', 'ListID'];

    const XML_ITEM_INVENTORY_PREF_VENDOR_FULL_NAME = ['tag_name' => ['PrefVendorRef', 'FullName'], 'value_length' => 41];

    const XML_ITEM_INVENTORY_ASSET_ACCOUNT_LIST_ID = ['AssetAccountRef', 'ListID'];

    const XML_ITEM_INVENTORY_ASSET_ACCOUNT_FULL_NAME = ['tag_name' => ['AssetAccountRef', 'FullName'], 'value_length' => 159];

    const XML_ITEM_INVENTORY_QUANTITY_ON_HAND = 'QuantityOnHand';
}