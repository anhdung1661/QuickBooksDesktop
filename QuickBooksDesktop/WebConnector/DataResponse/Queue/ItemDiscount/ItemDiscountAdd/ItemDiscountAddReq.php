<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd;

/**
 * Interface ItemDiscountAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd
 */
interface ItemDiscountAddReq
{
    const XML_ITEM_DISCOUNT_ADD = 'ItemDiscountAdd';

    const XML_ITEM_DISCOUNT_NAME = ['tag_name' => 'Name', 'value_length' => 31];

    /**
     * Fixed value for Name of Item Discount
     */
    const XML_ITEM_DISCOUNT_SALES_TAX_CODE_REF_FULL_NAME = ['tag_name' => ['SalesTaxCodeRef', 'FullName'], 'value_length' => 3];

    const XML_ITEM_DISCOUNT_SALES_TAX_CODE_REF_LIST_ID = ['tag_name' => ['SalesTaxCodeRef', 'ListID'], 'value_length' => 0];

    const XML_ITEM_DISCOUNT_INCOME_ACCOUNT_FULL_NAME = ['tag_name' => ['AccountRef', 'FullName'], 'value_length' => 159];

    const XML_ITEM_DISCOUNT_INCOME_ACCOUNT_LIST_ID = ['tag_name' => ['AccountRef', 'ListID'], 'value_length' => 0];
}