<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 03/03/2020 17:08
 */

namespace Magenest\QuickBooksDesktop\WebConnector\Connection;

use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\Model\ResourceModel\SessionConnect\CollectionFactory as SessionConnectCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionConnectModelFactory;
use Magenest\QuickBooksDesktop\WebConnector\AbstractConnection;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Authenticate as AuthenticateResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ClientVersion as ClientVersionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\CloseConnection as CloseConnectionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\GetLastError as GetLastErrorResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ServerVersion as ServerVersionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax\QueryReceiveResponseXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax\QuerySendRequestXML;

/**
 * Class QueryTax
 * @package Magenest\QuickBooksDesktop\WebConnector\Connection
 */
class QueryTax extends AbstractConnection
{
    /**
     * @var QuerySendRequestXML
     */
    protected $_sendRequestXML;

    /**
     * @var QueryReceiveResponseXML
     */
    protected $_receiveResponseXML;

    /**
     * QueryTax constructor.
     * @param QuerySendRequestXML $sendRequestXML
     * @param QueryReceiveResponseXML $receiveResponseXML
     * @param ServerVersionResponse $serverVersion
     * @param ClientVersionResponse $clientVersion
     * @param AuthenticateResponse $authenticate
     * @param GetLastErrorResponse $getLastError
     * @param CloseConnectionResponse $closeConnection
     * @param SessionConnectModelFactory $sessionConnectFactory
     * @param QuickbooksLogger $quickbooksLogger
     */
    public function __construct(
        QuerySendRequestXML $sendRequestXML,
        QueryReceiveResponseXML $receiveResponseXML,
        ServerVersionResponse $serverVersion,
        ClientVersionResponse $clientVersion,
        AuthenticateResponse $authenticate,
        GetLastErrorResponse $getLastError,
        CloseConnectionResponse $closeConnection,
        SessionConnectModelFactory $sessionConnectFactory,
        SessionConnectCollection $sessionConnectCollection,
        QuickbooksLogger $quickbooksLogger
    ) {
        parent::__construct($serverVersion, $clientVersion, $authenticate, $getLastError, $closeConnection, $sessionConnectFactory, $sessionConnectCollection, $quickbooksLogger);
        $this->_sendRequestXML = $sendRequestXML;
        $this->_receiveResponseXML = $receiveResponseXML;
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
        return $this->_receiveResponseXML;
    }
}