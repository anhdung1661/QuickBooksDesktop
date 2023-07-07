<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 25/02/2020 14:59
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataRequest;

/**
 * Object return from Quickbooks WebConnector
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataRequest
 */
class SendRequestXML
{
    /**
     * The ticket from the web connector.
     * This is the session token your web service returned to the web connector’s authenticate call,
     * as the first element of the returned string array
     *
     * @var string
     */
    public $ticket;

    /**
     * Only for the first sendRequestXML call in a data exchange session
     * will this parameter contains response data from a HostQuery, a
     * CompanyQuery, and a PreferencesQuery request. This data is
     * provided at the outset of a data exchange because it is normally
     * useful for a web service to have this data. In the ensuing data
     * exchange session, subsequent sendRequestXML calls from the web
     * processor do not contain this data, (only an empty string is supplied)
     * as it is assumed your web service already has it for the session.
     *
     */
    public $strHCPResponse;

    /**
     * The company file being used in the current data exchange.
     */
    public $strCompanyFileName;

    /**
     * The country version of QuickBooks or QuickBooks POS product
     * being used to access the company. For example, US, CA (Canada), or UK.
     *
     */
    public $qbXMLCountry;

    /**
     * The major version number (corresponding to the qbXML or
     * qbposXML spec level) of the request processor being used. For
     * example, the major number of the request processor released to support qbXML spec 6.0 would be “6”.
     *
     */
    public $qbXMLMajorVers;

    /**
     * The minor version number (corresponding to the qbXML or qbposXML spec level) of the request processor being used.
     * For example, the major number of the request processor released to support qbXML spec 6.0 would be “0”.
     *
     */
    public $qbXMLMinorVers;
}