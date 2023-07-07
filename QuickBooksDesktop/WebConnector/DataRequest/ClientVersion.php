<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 25/02/2020 16:05
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataRequest;

/**
 * object return from QWC when it calls clientVersion()
 * @package Magenest\QuickBooksDesktop\WebConnector\DataRequest
 */
class ClientVersion
{
    /**
     * The version of the QB web connector supplied in the web
     * connector’s call to clientVersion.
     */
    public $strVersion;
}