<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 25/02/2020 08:17
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataRequest;

/**
 * Object return from Quickbooks WebConnector
 * @package Magenest\QuickBooksDesktop\WebConnector
 */
class UserConnect
{
    /**
     * The web connector supplies the user name that you provided to your user in the QWC file
     * to allow that username to access your web service.
     */
    public $strUserName;

    /**
     * The web connector supplies the user password that you provided to
     * your user and which was stored by the user in the web connector.
     */
    public $strPassword;
}