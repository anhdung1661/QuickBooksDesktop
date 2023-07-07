<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;


/**
 * Interface QWCXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
interface QWCXML
{
    /**#@+
     * Constants defined by QBWC
     */
    const ATTR_STOP_ON_ERROR = 'stopOnError';

    const ATTR_CONTINUE_ON_ERROR = 'continueOnError';

    const XML_MAX_RETURN = 'MaxReturned';

    const XML_ACTIVE_STATUS = 'ActiveStatus';

    const VALUE_ACTIVE_STATUS_ALL = 'All';

    const VALUE_ACTIVE_STATUS_ACTIVE_ONLY = 'ActiveOnly';

    const VALUE_ACTIVE_STATUS_INACTIVE_ONLY = 'InactiveOnly';

    const DATE_FORMAT = 'Y-m-d';
}