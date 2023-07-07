<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 03/03/2020 17:14
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax;

use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\Model\Taxes;
use Magenest\QuickBooksDesktop\Model\TaxesFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\AbstractQueryReceiveResponseXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class QueryReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax
 */
class QueryReceiveResponseXML extends AbstractQueryReceiveResponseXML implements ItemSalesTaxQueryRes
{
    /**
     * @var TaxesFactory
     */
    protected $_taxesFactory;

    /**
     * QueryReceiveResponseXML constructor.
     * @param TaxesFactory $taxesFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        TaxesFactory $taxesFactory,
        Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($configuration, $sessionConnectFactory, $parserXml, $qbLogger);
        $this->_taxesFactory = $taxesFactory;
    }

    /**
     * @inheritDoc
     */
    protected function processResponseFromQB()
    {
        $sessionConnect = $this->_sessionConnectFactory->create()->loadByCode($this->getSessionConnect()->getData(SessionConnectInterface::SESSION_TOKEN));
        if ($sessionConnect->getData(SessionConnectInterface::ITERATOR_ID) == SessionConnectInterface::ITERATOR_ID_START && $sessionConnect->getData(SessionConnectInterface::PROCESSED) == 1) {
            // do not process data of the first request if query large data that be divided multi transactions
            return;
        }
        /**
         * @var Taxes $taxModel
         */
        $taxModel = $this->_taxesFactory->create();

        $taxData = $this->setResponseData(
            $this->getResponseByRequestId($this->getListRequestId()[0]) // When querying data, quickbooks only returns 1 response. so get the first response
        )->getData();
        $taxModel->setTaxData($taxData);

        $taxModel->save();
    }

    /**
     * @inheritDoc
     */
    protected function getDetailName()
    {
        return self::DETAIL_NAME;
    }

    /**
     * @inheritDoc
     */
    protected function getMaxReturned()
    {
        return $this->_configuration->getNumRecordsPerTaxRequest();
    }
}
