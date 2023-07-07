<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 27/02/2020 11:46
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Company;

use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ReceiveResponseXML as AbstractReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Company
 */
class ReceiveResponseXML extends AbstractReceiveResponseXML implements CompanyRes
{
    private $companyResourceModel;

    /**
     * ReceiveResponseXML constructor.
     * @param Configuration $configuration
     * @param Company $company
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        Configuration $configuration,
        Company $company,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($configuration, $sessionConnectFactory, $parserXml, $qbLogger);
        $this->companyResourceModel = $company;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function processResponseFromQB()
    {
        $companyName = $this->setResponseData(
            $this->getResponseByRequestId($this->getListRequestId()[0]) // When querying data, quickbooks only returns 1 response. so get the first response
        )->getData(self::COMPANY_NAME);

        $this->companyResourceModel->disableAllCompany();
        $this->companyResourceModel->saveCompany($companyName);
        $this->_configuration->setConfigData(Configuration::PATH_COMPANY, $companyName);
    }

    /**
     * @inheritDoc
     */
    protected function getDetailName()
    {
        return self::DETAIL_NAME;
    }
}
