<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 * @time: 25/09/2020 17:16
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd;

/**
 * Interface InvoiceAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd
 */
interface InvoiceAddReq
{
    const XML_SALES_INVOICE_ADD = 'InvoiceAdd';

    const XML_INVOICE_LINE_ADD = 'InvoiceLineAdd';

    const XML_SALES_ORDER_LIST_ID = 'TxnID';

    const XML_LINE_ITEM_TXN_ID = 'TxnLineID';
}