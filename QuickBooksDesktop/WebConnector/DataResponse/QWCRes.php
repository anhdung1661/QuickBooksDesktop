<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;


/**
 * Interface QWCRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
interface QWCRes
{
    /**#@+
     * Constants defined by QBWC
     */
    const REQUEST_ID = 'requestID';

    const STATUS_CODE = 'statusCode';

    const STATUS_MESSAGE = 'statusMessage';

    const RET_COUNT = 'retCount';

    const ITERATOR_REMAINING_COUNT = 'iteratorRemainingCount';

    const ITERATOR_ID = 'iteratorID';
}