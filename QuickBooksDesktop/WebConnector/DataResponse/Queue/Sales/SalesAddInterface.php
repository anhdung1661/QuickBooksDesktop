<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 * @time: 12/05/2020 18:25
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales;

/**
 * Interface SalesAddInterface
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales
 */
interface SalesAddInterface
{
    const CUSTOMER_REF_LIST_ID = ['tag_name' => ['CustomerRef', 'ListID'], 'value_length' => 41];

    const CUSTOMER_REF_FULL_NAME = ['tag_name' => ['CustomerRef', 'FullName'], 'value_length' => 209];

    const XML_TXN_DATE = ['tag_name' => 'TxnDate', 'value_length' => null];

    const XML_REF_NUMBER = ['tag_name' => 'RefNumber', 'value_length' => 11];

    const XML_SHIP_METHOD_REF = ['tag_name' => ['ShipMethodRef', 'FullName'], 'value_length' => 15];


    /**
     * Transaction line items
     */
    const XML_SALES_ORDER_LINE_ITEM_REF = ['tag_name' => ['ItemRef', 'FullName'], 'value_length' => null];

    const XML_SALES_ORDER_LINE_ITEM_QTY = ['tag_name' => 'Quantity', 'value_length' => null];

    const XML_SALES_ORDER_LINE_ITEM_RATE = ['tag_name' => 'Rate', 'value_length' => null];

    const XML_SALES_ORDER_LINE_ITEM_DESC = ['tag_name' => 'Desc', 'value_length' => null];

    const XML_ITEM_SALES_TAX_REF = ['tag_name' => ['ItemSalesTaxRef', 'FullName'], 'value_length' => 31];

    const XML_LINE_ITEM_TAX = ['tag_name' => ['SalesTaxCodeRef', 'FullName'], 'value_length' => 3];

    const XML_SALES_ORDER_LINE_AMOUNT = 'Amount';
    /**
     * End transaction line items
     */
}