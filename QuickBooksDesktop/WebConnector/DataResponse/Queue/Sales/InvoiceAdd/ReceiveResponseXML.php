<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 25/09/2020 17:17
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd;

use Magenest\QuickBooksDesktop\Api\Data\InvoiceInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\InvoiceFactory as InvoiceModel;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ReceiveResponseXML process response when import Invoice into Quickbooks
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements InvoiceAddRes
{
    /**
     * @var InvoiceModel
     */
    protected $_invoiceModel;

    /**
     * ReceiveResponseXML constructor.
     * @param InvoiceModel $invoiceModel
     * @param QueueCollection $queueCollection
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        InvoiceModel $invoiceModel,
        QueueCollection $queueCollection,
        QueueModelFactory $queueModelFactory, Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory, ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        $this->_invoiceModel = $invoiceModel;
        parent::__construct($queueCollection, $queueModelFactory, $configuration, $sessionConnectFactory, $parserXml, $qbLogger);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function saveEntityData($listQueue)
    {
        $invoicesData = [];
        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));

            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                $invoicesData[] = [
                    InvoiceInterface::MAGENTO_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID],
                    InvoiceInterface::LIST_ID => $this->getData(self::XML_INVOICE_TXN_ID),
                    InvoiceInterface::EDIT_SEQUENCE => $this->getData(self::XML_INVOICE_EDIT_SEQUENCE)
                ];
            }
        }
        $this->qbLogger->info('Save Invoice Data Before: ' . print_r($invoicesData, true));

        try {
            $this->_invoiceModel->create()->setInvoicesData($invoicesData)->save();
            $this->qbLogger->info('Save Invoice Data After.');
        } catch (\Exception $exception) {
            $this->qbLogger->critical($exception->getMessage());
        }
    }
}
