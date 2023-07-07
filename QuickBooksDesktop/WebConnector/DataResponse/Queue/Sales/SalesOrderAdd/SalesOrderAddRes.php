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
 * Interface SalesOrderAddRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd
 */
interface SalesOrderAddRes
{
    const XML_SALES_ORDER_TXN_ID = 'TxnID';

    const XML_SALES_ORDER_EDIT_SEQUENCE = 'EditSequence';

    const XML_SALES_ORDER_LINE_RET = 'SalesOrderLineRet';

    const XML_SALES_ORDER_TXN_LINE_ID = 'TxnLineID';
}