<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 14/03/2020 14:24
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Table structure
 *
 * Interface TaxesInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface TaxesInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__taxes';

    const ENTITY_ID = 'id';

    const TAX_CODE = 'tax_code';

    const TAX_VALUE = 'tax_value';

    const TAX_NOTE = 'note';
}