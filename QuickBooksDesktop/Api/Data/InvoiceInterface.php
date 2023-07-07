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
 * @time: 29/09/2020 16:16
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Interface InvoiceInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface InvoiceInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__invoice';

    const ENTITY_ID = 'id';

    const MAGENTO_ID = 'magento_invoice_id';

    const NOTE = 'note';

    const CREATED_AT = 'created_at';
}