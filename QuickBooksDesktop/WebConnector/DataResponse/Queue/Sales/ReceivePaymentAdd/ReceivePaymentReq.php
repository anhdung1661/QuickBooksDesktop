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
 * @time: 29/09/2020 17:06
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd;

/**
 * Interface ReceivePaymentReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd
 */
interface ReceivePaymentReq
{
    const XML_SALES_RECEIVE_PAYMENT_ADD = 'ReceivePaymentAdd';

    const XML_SALES_RECEIVE_PAYMENT_TOTAL_AMOUNT = 'TotalAmount';

    const XML_SALES_RECEIVE_PAYMENT_PAYMENT_METHOD_REF = ['tag_name' => ['PaymentMethodRef', 'FullName'], 'value_length' => 31];

    const XML_SALES_RECEIVE_PAYMENT_DEPOSIT_ACCOUNT = ['tag_name' => ['DepositToAccountRef', 'FullName'], 'value_length' => 0];

    const XML_SALES_RECEIVE_PAYMENT_APPLIED_TO_TXN_ADD = 'AppliedToTxnAdd';

    const XML_SALES_RECEIVE_PAYMENT_AMOUNT = 'PaymentAmount';

    const XML_RECEIVE_PAYMENT_DELETE = 'TxnDelRq';

    const XML_TXN_DEL_TYPE = ['tag_name' => 'TxnDelType', 'value_length' => null];

    const XML_TXN_ID = ['tag_name' => 'TxnID', 'value_length' => null];
}