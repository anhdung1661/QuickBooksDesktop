<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 10:35
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd;

use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerInformation;

/**
 * Interface CustomerAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd
 */
interface CustomerAddReq extends CustomerInformation
{
    const XML_CUSTOMER_ADD = 'CustomerAdd';
}