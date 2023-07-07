<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 * @time: 29/09/2020 17:06
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd;

/**
 * Interface ReceivePaymentRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd
 */
interface ReceivePaymentRes
{
    const XML_RECEIVE_PAYMENT_TXN_ID = 'TxnID';

    const XML_RECEIVE_PAYMENT_EDIT_SEQUENCE = 'EditSequence';
}