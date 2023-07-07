<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 * @time: 28/04/2020 09:35
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd;

/**
 * Interface SalesOrderAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd
 */
interface SalesOrderAddReq
{
    const XML_SALES_ORDER_ADD = 'SalesOrderAdd';

    const XML_SALES_ORDER_PO_NUMBER = ['tag_name' => 'PONumber', 'value_length' => 25];

    const XML_SALES_ORDER_TAX_CODE_FULL_NAME = ['tag_name' => ['ItemSalesTaxRef', 'FullName'], 'value_length' => 31];

    const XML_SALES_ORDER_LINE_ADD = 'SalesOrderLineAdd';
}