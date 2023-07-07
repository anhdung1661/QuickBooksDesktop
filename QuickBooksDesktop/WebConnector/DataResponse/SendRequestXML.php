<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Framework\DataObject;

/**
 * Class Authenticate
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
abstract class SendRequestXML implements QWCXML
{
    /**
     * If the web service has no requests to send, specify an empty string.
     */
    const NO_REQUEST = '';

    /**
     *  If you want the Web Connector to pause for an interval of time (currently 5 seconds) return the string “NoOp”,
     * which will cause the Web Connector to call your getLastError callback:
     * a “NoOp” returned from GetLastError will cause the QBWC to pause updates for 5 seconds before attempting to call sendRequestXML() again.
     */
    const PAUSE_5_SECONDS = 'NoOp';

    /**
     * Any other string will be taken as a qbXML for QuickBooks or a qbposXML request for QuickBooks POS.
     * The Web Connector sends the qbXML or qbposXML to QuickBooks or QuickBooks POS via the request processor’s ProcessRequest method call.
     * @var string
     */
    protected $sendRequestXMLResult;

    /**
     * @var Configuration
     */
    protected $_moduleConfig;

    /**
     * @var SessionModel
     */
    protected $_sessionModel;

    /**
     * @var DataObject
     */
    protected $_currentSessionConnect;

    /**
     * @var QuickbooksLogger
     */
    protected $qbLogger;

    /**
     * SendRequestXML constructor.
     * @param Configuration $configuration
     * @param SessionModel $sessionCollection
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        Configuration $configuration,
        SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        $this->_moduleConfig = $configuration;
        $this->_sessionModel = $sessionCollection;
        $this->qbLogger      = $qbLogger;
    }

    /**
     * @param DataObject $sessionConnectData
     * @return SendRequestXML
     */
    public function setCurrentSessionConnect(DataObject $sessionConnectData)
    {
        $this->_currentSessionConnect = $sessionConnectData;

        return $this;
    }

    /**
     * @param \Magenest\QuickBooksDesktop\WebConnector\DataRequest\SendRequestXML $dataFromQWC
     * @return void
     */
    public function process($dataFromQWC)
    {
        $this->sendRequestXMLResult = $this->prepareXML($dataFromQWC);
    }

    /**
     *
     * @return SendRequestXML
     */
    public function processFirstRequest()
    {
        // first transaction have processed = null
        if ($this->_currentSessionConnect->getData(SessionConnectInterface::PROCESSED) == SessionConnectInterface::PROCESSED_DEFAULT) {
            // default not use iteratorId
            $iteratorId = $this->isDividedRequest() ? SessionConnectInterface::ITERATOR_ID_START : SessionConnectInterface::ITERATOR_ID_NONE;
            $this->_sessionModel->create()->updateIteratorId($this->_currentSessionConnect->getData(SessionConnectInterface::SESSION_TOKEN), $iteratorId);
        }

        return $this;
    }

    /**
     * @param \Magenest\QuickBooksDesktop\WebConnector\DataRequest\SendRequestXML $dataFromQWC
     * @return string
     */
    private function prepareXML($dataFromQWC)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<?qbxml version="' . $dataFromQWC->qbXMLMajorVers . '.' . $dataFromQWC->qbXMLMinorVers . '"?>';
        $xml .= '<QBXML>';
        $xml .= '<QBXMLMsgsRq onError="' . $this->getOnError() . '">';
        $xml .= $this->getBodyXml();
        $xml .= '</QBXMLMsgsRq>';
        $xml .= '</QBXML>';

        $this->qbLogger->info(__('Prepare QBXML to send Request to QB Web Connector.'));
        $this->qbLogger->debug($xml);

        return $xml;
    }

    /**
     * @return string
     */
    abstract protected function getOnError();

    /**
     * Return the last transaction's iterator to continue query transaction
     * Return null if it not a continue transaction
     *
     * @return string|null
     */
    protected function getIterator()
    {
        $iteratorId = $this->_sessionModel->create()->loadByCode($this->_currentSessionConnect->getData(SessionConnectInterface::SESSION_TOKEN))
            ->getData(SessionConnectInterface::ITERATOR_ID);

        switch ($iteratorId) {
            case SessionConnectInterface::ITERATOR_ID_NONE:
                $xmlAttribute = '';
                break;
            case SessionConnectInterface::ITERATOR_ID_START:
                $xmlAttribute = ' iterator="Start"';
                break;
            default:
                $xmlAttribute = ' iterator="Continue" iteratorID="' . $iteratorId . '"';
        }

        return $xmlAttribute;
    }

    /**
     *
     * @param $requestId
     * @return int
     */
    public static function getRequestId($requestId)
    {
        return $requestId ? ' requestID="' . $requestId . '"' : '';
    }

    /**
     * @return string
     */
    abstract protected function getBodyXml();

    /**
     * Query all records (false) or divided into more transaction (true)?
     * the default value is false
     * This function not use for the add data into Quickbooks
     *
     * @return bool
     */
    protected function isDividedRequest()
    {
        return false;
    }
}
