<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 02/03/2020 16:06
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataRequest;

/**
 * object return from QWC when it calls getLastError()
 * @package Magenest\QuickBooksDesktop\WebConnector\DataRequest
 */
class GetLastError
{
    /**
     * The ticket from the web connector. This is the session token your
     * web service returned to the web connector’s authenticate call, as the
     * first element of the returned string array
     *
     * @var string
     */
    public $ticket;
}