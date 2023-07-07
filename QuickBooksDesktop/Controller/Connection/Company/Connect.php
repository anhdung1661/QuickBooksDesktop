<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Connection\Company;

use Magenest\QuickBooksDesktop\Controller\Connection\AbstractConnector;
use Magenest\QuickBooksDesktop\WebConnector\Connection\Company;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Webapi\Rest\Request;
use Zend_Soap_Server as ZendSoapServer;

/**
 * Class Sync
 * @package Magenest\QuickBooksDesktop\Controller\Connection\Company
 */
class Connect extends AbstractConnector
{
    /**
     * @var Company
     */
    protected $_handler;

    /**
     * Start constructor.
     * @param Company $handler
     * @param Context $context
     * @param Reader $configReader
     * @param ZendSoapServer $soapServer
     * @param Request $request
     */
    public function __construct(
        Company $handler,
        Context $context,
        Reader $configReader,
        ZendSoapServer $soapServer,
        Request $request
    ) {
        parent::__construct($context, $configReader, $soapServer, $request);
        $this->_handler = $handler;
    }


    /**
     * @inheritDoc
     */
    protected function getHandler()
    {
        return $this->_handler;
    }
}
