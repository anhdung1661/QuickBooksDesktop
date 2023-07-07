<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

use Elasticsearch\Common\Exceptions\RuntimeException;
use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * return value after QWC send receiveResponseXML()
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
abstract class ReceiveResponseXML implements QWCRes
{
    /**
     * Quickbooks returns a list of item types corresponding to item type in the submitted request
     * Example: 2 requests have been submitted: ItemInventoryAdd and ItemNonInventoryAdd,
     *          Quickbooks will return two types, ItemInventoryAddRs and ItemNonInventoryAddRs respectively.
     *
     * @var array
     */
    private $listItemTypesRs;

    /**
     * In each listItemTypesRs, there are many of response corresponding to submitted requests
     * Example: In the request have been submitted, you request 3 ItemInventoryAdd,
     *          You will get 3 corresponding responses
     *
     * @var array
     */
    private $listResponse;

    /**
     * In each response there will be a lot of items
     *
     * @var array
     */
    private $responseData;

    /**
     * @var ParserXml
     */
    protected $_parserXml;

    /**
     * @var DataObject
     */
    private $currentSessionConnect;

    /**
     * @var SessionConnectFactory
     */
    protected $_sessionConnectFactory;

    /**
     * @var Configuration
     */
    protected $_configuration;
    /**
     * A positive integer less than 100 represents the percentage of work completed.
     * the web connector knows that the web service has additional requests to be sent to QuickBooks,
     * the connection status bar will be updated based on the percentage returned by the web service and the connector will call sendRequestXML again
     * A value of 1 means one percent complete, a value of 100 means 100 percent complete--there is no more work.
     * A negative value means an error has occurred and the Web Connector responds to this with a getLastError call.
     * The negative value could be used as a custom error code.
     * @var int
     */
    protected $receiveResponseXMLResult;

    /**
     * @var QuickbooksLogger
     */
    protected $qbLogger;

    /**
     * ReceiveResponseXML constructor.
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        $this->_configuration = $configuration;
        $this->_sessionConnectFactory = $sessionConnectFactory;
        $this->_parserXml = $parserXml;
        $this->qbLogger = $qbLogger;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->receiveResponseXMLResult = $errorCode;
    }

    /**
     * @return ReceiveResponseXML
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function processDataReponse()
    {
        $this->processResponseFromQB();

        return $this;
    }

    /**
     * @return $this
     */
    public function prepareReply()
    {
        $this->receiveResponseXMLResult = $this->calculatePercentageComplete();
        return $this;
    }

    /**
     * @return void
     * @throws \Exception
     */
    abstract protected function processResponseFromQB();

    /**
     * Update number of records was processed
     *
     * @param string $sessionToken
     * @param $numberProcessing
     * @return ReceiveResponseXML
     */
    public function updateTicketProcessed($sessionToken, $numberProcessing)
    {
        $sessionModel = $this->_sessionConnectFactory->create();
        $sessionModel->updateProcessed($sessionToken, $numberProcessing);

        return $this;
    }

    /**
     * @return int
     */
    protected function calculatePercentageComplete()
    {
        $sessionConnect = $this->_sessionConnectFactory->create()->loadByCode($this->currentSessionConnect->getData(SessionConnectInterface::SESSION_TOKEN));
        $processed = $sessionConnect->getData(SessionConnectInterface::PROCESSED);
        $total = $sessionConnect->getData(SessionConnectInterface::TOTAL);

        if (empty($total)) {
            return 100;
        }

        if (empty($processed)) {
            return 0;
        }

        $percentageComplete = $processed / $total * 100;

        return ($percentageComplete > 100) ? 100 : $percentageComplete;
    }

    /**
     * Process the first request.
     * This function to set number of request that send to Quickbooks
     *
     * When there are a large of records in Quickbooks that need to query into Magento,
     * can override this function to define number of request will process.
     * Total will be set by divide the total number of records / number of records per request
     *
     * There is only sync 1 record per request when sync from Magento into Quickbooks,
     * so total will be set by the number of records that need to sync
     *
     * @return ReceiveResponseXML
     * @throws \Exception
     */
    public function processFirstRequest()
    {
        // first transaction have processed = null
        $sessionModel = $this->_sessionConnectFactory->create();
        if ($this->currentSessionConnect->getData(SessionConnectInterface::PROCESSED) == SessionConnectInterface::PROCESSED_DEFAULT) {
            $sessionModel->setTotal($this->currentSessionConnect->getData(SessionConnectInterface::SESSION_TOKEN) , $this->getTotalProcess())
                ->updateProcessed($this->currentSessionConnect->getData(SessionConnectInterface::SESSION_TOKEN) , 0);
        }

        return $this;
    }

    /**
     * @return ReceiveResponseXML
     * @throws \Exception
     */
    public function updateIteratorInFirstQueryRequest()
    {
        $sessionModel = $this->_sessionConnectFactory->create();

        // For query transactions, start querying data from the second transaction. it mean the first request of query transactions is the second transaction
        if ($this->currentSessionConnect->getData(SessionConnectInterface::ITERATOR_ID) == SessionConnectInterface::ITERATOR_ID_START
            && $this->currentSessionConnect->getData(SessionConnectInterface::PROCESSED) == 1) {
            $sessionModel->updateIteratorId(
                $this->currentSessionConnect->getData(SessionConnectInterface::SESSION_TOKEN),
                $this->setResponseData($this->getResponseByRequestId($this->getListRequestId()[0]))->getAttribute(self::ITERATOR_ID)
            );
        }
        unset($sessionModel);

        return $this;
    }

    /**
     * @return int
     */
    protected function getTotalProcess(){
        return 1; // default value
    }

    /**
     * @param DataObject $sessionConnectData
     * @return ReceiveResponseXML
     */
    public function setCurrentSessionConnect(DataObject $sessionConnectData)
    {
        $this->currentSessionConnect = $sessionConnectData;

        return $this;
    }

    /**
     * @return DataObject
     */
    public function getSessionConnect()
    {
        return $this->currentSessionConnect;
    }

    /**
     * Get list requestId in response.
     *
     * @return array
     */
    public function getListRequestId()
    {
        $listRequestId = [];
        foreach ($this->listItemTypesRs as $listResponse) {
            foreach ($listResponse as $response) {
                $listRequestId[] = $response['_attribute'][self::REQUEST_ID];
            }
        }
        return $listRequestId;
    }

    /**
     *
     * @param $requestId
     * @return array
     */
    public function getResponseByRequestId($requestId)
    {
        foreach ($this->listItemTypesRs as $listResponse) {
            foreach ($listResponse as $response) {
                if ($response['_attribute'][self::REQUEST_ID] == $requestId) {
                    return $response;
                }
            }
        }
        return [];
    }

    /**
     * Change XML as string to as array
     *
     * @param $xmlData
     * @return ReceiveResponseXML
     * @throws LocalizedException
     */
    public function processXML($xmlData)
    {
        $this->_parserXml->loadXML($xmlData);
        $listItemTypesRs = $this->_parserXml->xmlToArray()['QBXML']['QBXMLMsgsRs'];
        if (!$listItemTypesRs) {
            $listItemTypesRs = [];
        }
        foreach ($listItemTypesRs as $keyItemTypeRs => $itemTypeRs) {
            $this->listItemTypesRs[$keyItemTypeRs] = array_key_exists('_value', $itemTypeRs)  ? [$itemTypeRs] : $itemTypeRs;
            foreach ($this->listItemTypesRs[$keyItemTypeRs] as $keyResponse => $response) {
                foreach ($this->listItemTypesRs[$keyItemTypeRs][$keyResponse]['_value'] as $keyItemTypeRet => $itemTypeRet) {
                    $this->listItemTypesRs[$keyItemTypeRs][$keyResponse]['_value'][$keyItemTypeRet] = array_key_exists('ListID', $itemTypeRet) ? [$itemTypeRet] : $itemTypeRet;
                }
            }
        }
        $this->qbLogger->info(__('Receive response to Quickbooks desktop.'));
        $this->qbLogger->debug(print_r($listItemTypesRs, true));

        return $this;
    }

    /**
     * @param array $responseData
     * @return ReceiveResponseXML
     */
    public function setResponseData($responseData)
    {
        $this->responseData = $responseData;
        return $this;
    }

    /**
     * Get data from Quickbooks response
     * *Must setResponseData first*
     *
     * @param $key
     * @param int $objectIndex
     * @return string
     * @throws \Exception
     */
    protected function getData($key = null, $objectIndex = null)
    {
        $arrKey = ['_value', $this->getDetailName()];

        if (isset($key) && $this->countObjectsReturn() == 1) {
            if ($objectIndex != null) {
                $arrKey[] = $objectIndex;
            } else {
                $arrKey[] = 0;
            }
        }

        if ($key) {
            $key = is_string($key) ? [$key] : $key;
            $arrKey = array_merge($arrKey, $key);
        }
        return $this->getValueFromResponseData($arrKey);
    }

    /**
     * get Attribute of a request from Quickbooks response
     * *Must setResponseData first
     *
     * @param $key
     * @return string
     * @throws \Exception
     */
    protected function getAttribute($key = null)
    {
        $arrKey = ['_attribute'];
        if ($key) {
            $key = is_string($key) ? [$key] : $key;
            $arrKey = array_merge($arrKey, $key);
        }
        return $this->getValueFromResponseData($arrKey);
    }

    /**
     * In each response, there may be more typeRet
     * This function get list key of the typeRet in a response
     * *Must setResponseData first
     *
     * @return array
     */
    public function getListKeyTypeRet()
    {
        return array_keys($this->responseData['_value'] ?? []);
    }

    /**
     * return number of objects in a request is returned by QB
     * *Must setResponseData first
     *
     * @return int
     */
    public function countObjectsReturn()
    {
        $objects = $this->responseData['_value'][$this->getDetailName()];
        if (!isset($objects['ListID'])) {
            return count($objects);
        }

        return 1;
    }

    /**
     * @return string
     */
    abstract protected function getDetailName();

    /**
     *
     * *Must setResponseData first*
     *
     * @param $arrKey
     * @return array|string
     * @throws \Exception
     */
    private function getValueFromResponseData($arrKey)
    {
        try {
            $value = $this->responseData;
            foreach ($arrKey as $key) {
                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    $value = '';
                    throw new \Exception(__('Cannot get ') . $key);
                }
            }
        } catch (RuntimeException $exception) {
            throw new \Exception($exception->getMessage());
        } finally {
            return $value;
        }
    }
}
