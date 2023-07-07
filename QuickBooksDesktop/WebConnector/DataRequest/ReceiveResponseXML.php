<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 25/02/2020 15:38
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataRequest;

/**
 * Object return from Quickbooks WebConnector
 * Class ReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataRequest
 */
class ReceiveResponseXML
{
    /**
     * The ticket from the web connector. This is the session token your
     * web service returned to the web connector’s authenticate call, as the
     * first element of the returned string array.
     *
     */
    public $ticket;

    /**
     * Contains the qbXML response from QuickBooks or qbposXML response from QuickBooks POS.
     *
     * @var string
     */
    public $response;

    /**
     * The hresult and message could be returned as a result of certain
     * errors that could occur when QuickBooks or QuickBooks POS sends
     * requests is to the QuickBooks/QuickBooks POS request processor
     * via the ProcessRequest call. If this call to the request processor
     * resulted in an error (exception) instead of a response, then the web connector
     * will return the corresponding HRESULT and its text
     * message in the hresult and message parameters. If no such error
     * occurred, hresult and message will be empty strings.
     *
     * @var
     */
    public $hresult;

    /**
     * See above under hresult.
     */
    public $message;
}