<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 10/04/2020 13:43
 */

namespace Magenest\QuickBooksDesktop\Controller\Connection\Queue;

use Magenest\QuickBooksDesktop\Controller\Connection\AbstractConnector;
use Magenest\QuickBooksDesktop\WebConnector\AbstractConnection;
use Magenest\QuickBooksDesktop\WebConnector\Connection\SyncQueue;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Webapi\Rest\Request;
use Zend_Soap_Server as ZendSoapServer;

/**
 * Class SyncData
 * @package Magenest\QuickBooksDesktop\Controller\Connection\Queue
 */
class SyncData extends AbstractConnector
{
    /**
     * @var SyncQueue
     */
    protected $_syncQueue;

    /**
     * SyncData constructor.
     * @param SyncQueue $syncQueue
     * @param Context $context
     * @param Reader $configReader
     * @param ZendSoapServer $soapServer
     * @param Request $request
     */
    public function __construct(
        SyncQueue $syncQueue,
        Context $context,
        Reader $configReader,
        ZendSoapServer $soapServer,
        Request $request
    ) {
        parent::__construct($context, $configReader, $soapServer, $request);
        $this->_syncQueue = $syncQueue;
    }

    /**
     * @inheritDoc
     */
    protected function getHandler()
    {
        return $this->_syncQueue;
    }
}