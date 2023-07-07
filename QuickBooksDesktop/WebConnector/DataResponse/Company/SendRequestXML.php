<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 27/02/2020 13:03
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Company;

use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\SendRequestXML as AbstractSendRequestXML;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Company
 */
class SendRequestXML extends AbstractSendRequestXML implements CompanyReq
{
    /**
     * SendRequestXML constructor.
     * @param Configuration $qbConfiguration
     * @param SessionModel $sessionCollection
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        Configuration $qbConfiguration,
        SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($qbConfiguration, $sessionCollection, $qbLogger);
    }

    /**
     * @param \Magenest\QuickBooksDesktop\WebConnector\DataRequest\SendRequestXML $dataFromQWC
     */
    public function process($dataFromQWC)
    {
        parent::process($dataFromQWC);
        $this->saveQuickBooksCountryVersion($dataFromQWC->qbXMLCountry);
    }

    /**
     * @return string
     */
    protected function getOnError()
    {
        return self::ATTR_STOP_ON_ERROR;
    }

    /**
     * @return string
     */
    protected function getBodyXml()
    {
        $xml = '<' . self::XML_COMPANY_QUERY . 'Rq' . $this->getRequestId(1) . '>';
        $xml .= '</' . self::XML_COMPANY_QUERY . 'Rq>';
        return $xml;
    }

    /**
     *
     * @param $countryCode
     */
    private function saveQuickBooksCountryVersion($countryCode)
    {
        $this->_moduleConfig->setConfigData(Configuration::XML_PATH_QUICKBOOKS_VERSION, $countryCode);
    }

    /**
     * @inheritDoc
     */
    protected function getIterator()
    {
        return '';
    }
}
