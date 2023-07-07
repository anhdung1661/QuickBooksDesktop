<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 15:30
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Table structure
 *
 * Interface PaymentMethodInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface PaymentMethodInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__payment_method';

    const ENTITY_ID = 'id';

    const PAYMENT_METHOD = 'payment_method';

    const NOTE = 'note';
}