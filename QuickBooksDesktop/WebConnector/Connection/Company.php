<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 27/02/2020 11:47
 */

namespace Magenest\QuickBooksDesktop\WebConnector\Connection;

use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\Model\ResourceModel\SessionConnect\CollectionFactory as SessionConnectCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionConnectModelFactory;
use Magenest\QuickBooksDesktop\WebConnector\AbstractConnection;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Authenticate as AuthenticateResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ClientVersion as ClientVersionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\CloseConnection as CloseConnectionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Company\ReceiveResponseXML as ReceiveResponseXMLResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Company\SendRequestXML as SendRequestXMLResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\GetLastError as GetLastErrorResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ServerVersion as ServerVersionResponse;

/**
 * Class Company
 * @package Magenest\QuickBooksDesktop\WebConnector\Connection
 */
class Company extends AbstractConnection
{
    /**
     * @var SendRequestXMLResponse
     */
    protected $_sendRequestXML;

    /**
     * @var ReceiveResponseXMLResponse
     */
    protected $_receiveReposneXML;

    /**
     * Company constructor.
     * @param ServerVersionResponse $serverVersion
     * @param ClientVersionResponse $clientVersion
     * @param AuthenticateResponse $authenticate
     * @param GetLastErrorResponse $getLastError
     * @param CloseConnectionResponse $closeConnection
     * @param SendRequestXMLResponse $sendRequestXML
     * @param ReceiveResponseXMLResponse $_receiveReposneXML
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
        SendRequestXMLResponse $sendRequestXML,
        ReceiveResponseXMLResponse $_receiveReposneXML,
        SessionConnectModelFactory $sessionConnectFactory,
        SessionConnectCollection $sessionConnectCollection,
        QuickbooksLogger $quickbooksLogger
    ) {
        parent::__construct($serverVersion, $clientVersion, $authenticate, $getLastError, $closeConnection, $sessionConnectFactory, $sessionConnectCollection, $quickbooksLogger);
        $this->_sendRequestXML = $sendRequestXML;
        $this->_receiveReposneXML = $_receiveReposneXML;
    }

    /**
     * @inheritDoc
     */
    protected function getSendRequestXML()
    {
        return $this->_sendRequestXML;
    }

    /**
     * @inheritDoc
     */
    protected function getReceiveResponseXML($response = null)
    {
        return $this->_receiveReposneXML;
    }
}