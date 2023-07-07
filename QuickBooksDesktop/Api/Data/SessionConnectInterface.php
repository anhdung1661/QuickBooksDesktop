<?php


namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Table structure
 *
 * Interface SessionConnect
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface SessionConnectInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TABLE_NAME = 'magenest_qbd__session_connect';

    const ENTITY_ID = 'id';

    const SESSION_TOKEN = 'session_token';

    const USER_NAME_ID = 'username';

    const PROCESSED = 'processed'; // number of records was processed

    const TOTAL = 'total'; // number of all records

    const ITERATOR_ID = 'iterator_id';

    const ITERATOR_ID_START = 1;

    const ITERATOR_ID_NONE = -1; // for transaction doesn't use this attribute

    const CREATE_AT = 'create_at';

    const LAST_ERROR_MESSAGE = 'lasterror_msg';

    const PROCESSED_DEFAULT = 0; // for first request
}