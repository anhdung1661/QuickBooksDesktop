<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 * @time: 25/09/2020 13:16
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Interface SalesOrderInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface SalesOrderInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__sales_order';

    const ENTITY_ID = 'id';

    const MAGENTO_ID = 'magento_order_id';

    const NOTE = 'note';

    const CREATED_AT = 'created_at';
}