<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @category
 * @author doanhcn2 - Magenest
 * @time: 30/09/2020 13:50
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\CreditMemoAdd;

use Magenest\QuickBooksDesktop\Api\Data\CreditMemoInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\CreditMemoFactory as CreditMemoModel;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice;
use Magenest\QuickBooksDesktop\Model\ResourceModel\ReceivePayment;
use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class ReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\CreditMemoAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements CreditMemoAddRes
{
    /**
     * @var CreditMemoModel
     */
    protected $_creditMemoModel;

    /**
     * @var Invoice
     */
    protected $invoiceResource;

    /**
     * @var QueueAction
     */
    protected $queueAction;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    protected $receivePayment;

    /**
     * ReceiveResponseXML constructor.
     * @param ReceivePayment $receivePayment
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param QueueAction $queueAction
     * @param Invoice $invoiceResource
     * @param CreditMemoModel $creditMemoModel
     * @param QueueCollection $queueCollection
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        ReceivePayment $receivePayment,
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        QueueAction $queueAction,
        Invoice $invoiceResource,
        CreditMemoModel $creditMemoModel,
        QueueCollection $queueCollection,
        QueueModelFactory $queueModelFactory, Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory, ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        $this->receivePayment = $receivePayment;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->queueAction = $queueAction;
        $this->invoiceResource = $invoiceResource;
        $this->_creditMemoModel = $creditMemoModel;
        parent::__construct($queueCollection, $queueModelFactory, $configuration, $sessionConnectFactory, $parserXml, $qbLogger);
    }

    /**
     * @inheritDoc
     */
    protected function saveEntityData($listQueue)
    {
        $creditMemos = [];

        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));

            $creditMemoIds = [];
            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                $creditMemos[] = [
                    CreditMemoInterface::MAGENTO_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID],
                    CreditMemoInterface::LIST_ID => $this->getData(self::XML_CREDIT_MEMO_TXN_ID),
                    CreditMemoInterface::EDIT_SEQUENCE => $this->getData(self::XML_CREDIT_MEMO_EDIT_SEQUENCE)
                ];
                $creditMemoIds[] = $queue[QueueInterface::MAGENTO_ENTITY_ID];
            }
        }
        $this->qbLogger->info('Save Credit memo Data Before: ' . print_r($creditMemos, true));

        try {
            $this->_creditMemoModel->create()->setCreditMemosData($creditMemos)->save();
            $this->qbLogger->info('Save Credit Memo Data After.');
        } catch (\Exception $exception) {
            $this->qbLogger->critical($exception->getMessage());
        }
    }
}
