<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 14/10/2020 16:39
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerMod;

use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerInformation;

/**
 * Interface CustomerModReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerMod
 */
interface CustomerModReq extends CustomerInformation
{
    const XML_CUSTOMER_MOD = 'CustomerMod';

    const XML_CUSTOMER_LIST_ID = 'ListID';

    const XML_CUSTOMER_EDIT_SEQUENCE = ['tag_name' => 'EditSequence', 'value_length' => 16];
}