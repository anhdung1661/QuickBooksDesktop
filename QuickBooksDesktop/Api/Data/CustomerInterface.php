<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 08:27
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Table structure
 *
 * Interface CustomerInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface CustomerInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__customer';

    const ENTITY_ID = 'id';

    const CUSTOMER_NAME = 'customer_name';

    const EMAIL = 'email';

    const NOTE = 'note';
}