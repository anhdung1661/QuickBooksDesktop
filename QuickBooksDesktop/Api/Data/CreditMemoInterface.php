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
interface CreditMemoInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__credit_memo';

    const ENTITY_ID = 'id';

    const MAGENTO_ID = 'magento_credit_memo_id';

    const NOTE = 'note';

    const CREATED_AT = 'created_at';
}