<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Connection;

use Magenest\QuickBooksDesktop\WebConnector\AbstractConnection;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Dir;
use Zend_Soap_Server as ZendSoapServer;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class Start
 * @package Magenest\QuickBooksDesktop\Controller\Connector
 */
abstract class AbstractConnector extends Action
{
    /**
     * @var string
     */
    protected $_wsdl;

    /**
     * @var ZendSoapServer
     */
    protected $_soapServer;

    /**
     * @var Request
     */
    protected $_requestWebapi;

    /**
     * Start constructor.
     * @param Context $context
     * @param Reader $configReader
     * @param ZendSoapServer $soapServer
     * @param Request $request
     */
    public function __construct(
        Context $context,
        Reader $configReader,
        ZendSoapServer $soapServer,
        Request $request
    ) {
        $this->_wsdl = $configReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Magenest_QuickBooksDesktop') . '/wsdl/QBWebConnectorSvc.wsdl';
        $this->_soapServer = $soapServer;
        $this->_requestWebapi = $request;
        parent::__construct($context);
    }

    /**
     * @return void|Redirect
     * @throws InputException
     * @throws \Zend_Soap_Server_Exception
     */
    public function execute()
    {
        $method = $this->_requestWebapi->getHttpMethod();
        if ($method != Request::HTTP_METHOD_POST) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('/');
        }

        $soapClass = $this->_soapServer;
        $soapClass->setWsdl($this->_wsdl);
        $soapClass->setObject($this->getHandler());
        $soapClass->handle();
    }

    /**
     * This class is responsible for the process of communicating with QWC
     *
     * @return AbstractConnection
     */
    abstract protected function getHandler();
}
