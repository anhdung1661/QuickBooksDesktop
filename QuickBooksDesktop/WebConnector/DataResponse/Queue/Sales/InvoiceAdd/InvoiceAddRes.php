<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 25/09/2020 17:16
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd;

/**
 * Interface InvoiceAddRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd
 */
interface InvoiceAddRes
{
    const XML_INVOICE_TXN_ID = 'TxnID';

    const XML_INVOICE_EDIT_SEQUENCE = 'EditSequence';
}