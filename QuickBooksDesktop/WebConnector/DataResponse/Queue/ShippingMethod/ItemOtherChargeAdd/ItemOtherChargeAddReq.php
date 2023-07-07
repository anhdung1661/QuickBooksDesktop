<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ItemOtherChargeAdd;

/**
 * Interface ItemOtherChargeAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ItemOtherChargeAdd
 */
interface ItemOtherChargeAddReq
{
    /**#@+
     * Constants defined by QBWC
     */

    const XML_ITEM_OTHER_CHARGE_ADD = 'ItemOtherChargeAdd';

    const XML_ITEM_OTHER_CHARGE_NAME = ['tag_name' => 'Name', 'value_length' => 31];

    const SALES_TAX_CODE_REF_LIST_ID = ['SalesTaxCodeRef', 'ListID'];

    const SALES_TAX_CODE_REF_FULL_NAME = ['tag_name' => ['SalesTaxCodeRef', 'FullName'], 'value_length' => 3];

    const SALES_OR_PURCHASE_ACCOUNT_REF_LIST_ID = ['SalesOrPurchase', 'AccountRef', 'ListID'];

    const SALES_OR_PURCHASE_ACCOUNT_REF_FULL_NAME = ['tag_name' => ['SalesOrPurchase', 'AccountRef', 'FullName'], 'value_length' => 159];
}