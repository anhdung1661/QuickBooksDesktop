<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 16/03/2020 13:27
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Table structure
 *
 * Interface CompanyInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface CompanyInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TABLE_NAME = 'magenest_qbd__company';

    /**#@+
     * Constants are the column names of the main table
     */
    const ENTITY_ID = 'company_id';

    const COMPANY_NAME_FIELD = 'company_name';

    const COMPANY_STATUS_FIELD = 'status';

    const NOTE_FIELD = 'note';

    /**#@+
     * Constants are the value of the status column
     */
    const COMPANY_CONNECTED = 1;

    const COMPANY_DISCONNECTED = 0;
}