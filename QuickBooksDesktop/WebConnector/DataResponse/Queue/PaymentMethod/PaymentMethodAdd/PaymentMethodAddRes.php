<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 14:54
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd;

/**
 * Interface PaymentMethodAddRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd
 */
interface PaymentMethodAddRes
{
    /**#@+
     * Constants defined by QBWC
     */
    const DETAIL_NAME = 'PaymentMethodRet';

    const XML_PAYMENT_METHOD_ADD_LIST_ID = 'ListID';

    const XML_PAYMENT_METHOD_ADD_EDIT_SEQUENCE = 'EditSequence';

    const XML_PAYMENT_METHOD_ADD_NAME = 'Name';
}