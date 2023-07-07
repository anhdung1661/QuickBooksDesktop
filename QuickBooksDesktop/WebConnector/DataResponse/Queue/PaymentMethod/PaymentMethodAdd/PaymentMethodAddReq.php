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
 * Interface PaymentMethodAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd
 */
interface PaymentMethodAddReq
{
    const XML_PAYMENT_METHOD_ADD = 'PaymentMethodAdd';

    const XML_PAYMENT_METHOD_NAME = ['tag_name' => 'Name', 'value_length' => 31];

    const XML_PAYMENT_METHOD_TYPE = 'PaymentMethodType';

    const XML_PAYMENT_METHOD_TYPE_ECHECK = 'ECheck';
}