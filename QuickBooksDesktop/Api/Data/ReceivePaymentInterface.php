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
 * Interface ReceivePaymentInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface ReceivePaymentInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__receive_payment';

    const ENTITY_ID = 'id';

    const MAGENTO_ID = 'magento_invoice_id';

    const NOTE = 'note';

    const CREATED_AT = 'created_at';
}