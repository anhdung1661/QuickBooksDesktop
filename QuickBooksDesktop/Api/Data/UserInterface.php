<?php


namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Table structure
 *
 * Interface UserInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface UserInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TABLE_NAME = 'magenest_qbd__user';

    /**#@+
     * Constants are the column names of the main table
     */
    const ENTITY_ID = 'user_id';

    const USERNAME_FIELD = 'username';

    const PASSWORD_FIELD = 'password';

    const STATUS_FIELD = 'status';

    const NOTE_FIELD = 'note';

    const Status_VALUE_ACTIVE = 1;

    const Status_VALUE_INACTIVE = 0;
}
