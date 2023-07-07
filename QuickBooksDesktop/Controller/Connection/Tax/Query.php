<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Connection\Tax;

use Magenest\QuickBooksDesktop\Controller\Connection\AbstractConnector;
use Magenest\QuickBooksDesktop\WebConnector\Connection\QueryTax;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Webapi\Rest\Request;
use Zend_Soap_Server as ZendSoapServer;

/**
 * Class Sync
 * @package Magenest\QuickBooksDesktop\Controller\Connection\Product
 */
class Query extends AbstractConnector
{
    /**
     * @var QueryTax
     */
    protected $_handler;

    /**
     * Query constructor.
     * @param Context $context
     * @param Reader $configReader
     * @param ZendSoapServer $soapServer
     * @param QueryTax $handler
     * @param Request $request
     */
    public function __construct(
        Context $context,
        Reader $configReader,
        ZendSoapServer $soapServer,
        QueryTax $handler,
        Request $request
    ) {
        parent::__construct($context, $configReader, $soapServer, $request);
        $this->_handler = $handler;
    }


    /**
     * @return QueryTax
     */
    protected function getHandler()
    {
        return $this->_handler;
    }
}
