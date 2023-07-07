<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

/**
 * return value after QWC send getLastError()
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
class GetLastError
{
    /**
     *Your web service should return a message string describing the problem and any other information that you want your user to see.
     * The web connector writes this message to the web connector log for the user and also displays it in the web connector’s Status column.
     * If you want your web service to go into interactive mode,
     *  you return the string “Interactive mode” and QBWC will respond by calling your web service’s getInteractiveURL method,
     *  and open a web browser to the URL that you provide via this callback.
     * If you want the Web Connector to pause for an interval of time (currently 5 seconds) return the string “NoOp” from your sendRequestXML callback,
     *  followed by the string “NoOp” returned from your “getLastError callback.
     * This will cause the QBWC to pause updates for 5 seconds before attempting to call sendRequestXML() again.
     * @param string
     */
    public $getLastErrorResult;

    /**
     * GetLastError constructor.
     * @param $result
     */
    public function __construct($result = '')
    {
        $this->getLastErrorResult = $result;
    }
}
