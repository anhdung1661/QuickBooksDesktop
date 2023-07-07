<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/02/2020 08:17
 */

namespace Magenest\QuickBooksDesktop\WebConnector;

use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\Model\ResourceModel\SessionConnect\CollectionFactory as SessionConnectCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnect;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionConnectModelFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\ClientVersion;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\CloseConnection;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\GetLastError;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\ReceiveResponseXML;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\SendRequestXML;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\UserConnect;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ClientVersion as ClientVersionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\CloseConnection as CloseConnectionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\GetLastError as GetLastErrorResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ReceiveResponseXML as ReceiveResponseXMLResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\SendRequestXML as SendRequestXMLResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ServerVersion as ServerVersionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Authenticate as AuthenticateResponse;
use Magento\Framework\DataObject;

/**
 * Class AbstractConnection processes the response returned from Quickbooks
 * @package Magenest\QuickBooksDesktop\WebConnector
 */
abstract class AbstractConnection
{
    /**
     * @var DataObject
     */
    private $currentSessionConnect;

    /**
     * @var QuickbooksLogger
     */
    protected $_quickbooksLogger;

    /**
     * @var SessionConnectModelFactory
     */
    private $sessionConnect;

    /**
     * @var SessionConnectCollection
     */
    protected $_sessionCollection;

    /**
     * @var ServerVersionResponse
     */
    protected $_serverVersion;

    /**
     * @var CloseConnectionResponse
     */
    protected $_clientVersion;

    /**
     * @var AuthenticateResponse
     */
    protected $_authentication;

    /**
     * @var GetLastErrorResponse
     */
    protected $_getLastError;
    /**
     * @var CloseConnectionResponse
     */
    protected $_closeConnection;

    /**
     * AbstractConnection constructor.
     * @param ServerVersionResponse $serverVersion
     * @param ClientVersionResponse $clientVersion
     * @param AuthenticateResponse $authenticate
     * @param GetLastErrorResponse $getLastError
     * @param CloseConnectionResponse $closeConnection
     * @param SessionConnectModelFactory $sessionConnectFactory
     * @param SessionConnectCollection $sessionConnectCollection
     * @param QuickbooksLogger $quickbooksLogger
     */
    public function __construct(
        ServerVersionResponse $serverVersion,
        ClientVersionResponse $clientVersion,
        AuthenticateResponse $authenticate,
        GetLastErrorResponse $getLastError,
        CloseConnectionResponse $closeConnection,
        SessionConnectModelFactory $sessionConnectFactory,
        SessionConnectCollection $sessionConnectCollection,
        QuickbooksLogger $quickbooksLogger
    ) {
        $this->_serverVersion = $serverVersion;
        $this->_clientVersion = $clientVersion;
        $this->_authentication = $authenticate;
        $this->_getLastError = $getLastError;
        $this->_closeConnection = $closeConnection;
        $this->sessionConnect = $sessionConnectFactory;
        $this->_sessionCollection = $sessionConnectCollection;
        $this->_quickbooksLogger = $quickbooksLogger;
    }

    /**
     * Step 1: Quickbooks check version of Web node
     * WebMethod: serverVersion() has been called by QB Web connector
     *
     * @return ServerVersionResponse
     */
    public function serverVersion()
    {
        return $this->_serverVersion;
    }

    /**
     * Step 2: Get and check QB Web Connector version
     * WebMethod: clientVersion() has been called by QB Web connector
     *
     *
     * @param ClientVersion $clientVersion
     * @return ClientVersionResponse
     */
    public function clientVersion($clientVersion)
    {
        return $this->_clientVersion;
    }

    /**
     * Step 3: Check user connect between M2 and QBD
     * WebMethod: authenticate() has been called by QB Web connector
     *
     * @param UserConnect $userConnect
     * @return AuthenticateResponse
     */
    public function authenticate($userConnect)
    {
        try {
            $this->_authentication->processAuth($userConnect);
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical('Error while Authenticating: ' . $exception->getMessage());
        }

        return $this->_authentication;
    }

    /**
     * Step 4: Receive data, prepare QBXML to send Request to QB Web Connector
     * WebMethod: sendRequestXML() has been called by QB Web connector
     *
     * @param SendRequestXML $dataFromQWC
     * @return SendRequestXMLResponse
     */
    public function sendRequestXML($dataFromQWC){
        try {
            $this->getSendRequestXML()
                ->setCurrentSessionConnect($this->getSessionConnecting($dataFromQWC->ticket))
                ->processFirstRequest()
                ->process($dataFromQWC);
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical(__('Error when prepare XML: ') . $exception->getMessage());
        } finally {
            return $this->getSendRequestXML();
        }
    }

    /**
     * Step 5.1: If success, QBWC return information of object
     * WebMethod: receiveResponseXML() has been called by QB Web connector
     *
     * @param ReceiveResponseXML $dataResponse
     * @return ReceiveResponseXMLResponse
     */
    public function receiveResponseXML($dataResponse)
    {
        try {
            $this->getReceiveResponseXML($dataResponse->response)->processXML($dataResponse->response)
                ->setCurrentSessionConnect($this->getSessionConnecting($dataResponse->ticket))
                ->processFirstRequest()
                ->updateTicketProcessed($dataResponse->ticket, 1); // increase number of records that be processed;

            if ($dataResponse->hresult) {
                throw new \RuntimeException($dataResponse->hresult . ': ' . $dataResponse->message);
            }
            $this->getReceiveResponseXML($dataResponse->response)
                ->updateIteratorInFirstQueryRequest()
                ->processDataReponse();
        } catch (\Exception $exception) {
            $this->processException($dataResponse->ticket, __('Error data response: ') . $exception->getMessage());
            $this->getReceiveResponseXML($dataResponse->response)->setErrorCode(-1);
        } finally {
            return $this->getReceiveResponseXML($dataResponse->response)->prepareReply();
        }
    }

    /**
     * Step 5.2: If error, QBWC return Last Error
     * WebMethod: getLastError() has been called by QBWebconnector
     *
     * @param GetLastError $sessionToken
     * @return GetLastErrorResponse
     */
    public function getLastError($sessionToken)
    {
        $session = $this->getSessionConnecting($sessionToken->ticket);
        if ($session->getData(SessionConnectInterface::ENTITY_ID)) {
            $this->_getLastError->getLastErrorResult = $session->getData(SessionConnectInterface::LAST_ERROR_MESSAGE);
        }
        return $this->_getLastError;
    }

    /**
     * Step 6: Close Connection
     * WebMethod: closeConnection() has been called by QB Web connector
     *
     * @param CloseConnection $dataResponse
     * @return CloseConnectionResponse
     */
    public function closeConnection($dataResponse)
    {
        return $this->_closeConnection;
    }

    /**
     * @return SendRequestXMLResponse
     */
    abstract protected function getSendRequestXML();

    /**
     * @param null $response
     * @return ReceiveResponseXMLResponse
     */
    abstract protected function getReceiveResponseXML($response = null);

    /**
     * @param $sessionToken
     * @return DataObject
     */
    public function getSessionConnecting($sessionToken)
    {
        if (!$this->currentSessionConnect) {
            $this->currentSessionConnect = $this->_sessionCollection->create()->addFieldToFilter(SessionConnectInterface::SESSION_TOKEN, $sessionToken)->getLastItem();
        }
        return $this->currentSessionConnect;
    }

    /**
     * @param $sessionToken
     * @param $errorMsg
     * @return void
     */
    private function processException($sessionToken, $errorMsg)
    {
        $this->writeDebugFile($sessionToken, $errorMsg);
        $this->otherActionWhenError($sessionToken, $errorMsg);
    }

    /**
     * @param $sessionToken
     * @param $errorMsg
     */
    private function writeDebugFile($sessionToken, $errorMsg)
    {
        try {
            $this->_quickbooksLogger->critical($errorMsg);
            $this->sessionConnect->create()->setLastErrorMsg($sessionToken, $errorMsg);
        } catch (\Exception $e) {
            $this->_quickbooksLogger->critical('Error when save last session error: ' . $e->getMessage());

        }
    }

    /**
     * Another action will be taken when the exception occurs
     *
     * @param $sessionToken
     * @param $errorMsg
     */
    protected function otherActionWhenError($sessionToken, $errorMsg)
    {

    }
}
