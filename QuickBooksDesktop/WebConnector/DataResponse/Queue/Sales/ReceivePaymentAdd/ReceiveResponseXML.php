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
 * @time: 29/09/2020 17:06
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\ReceivePaymentInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ReceivePaymentFactory as ReceivePaymentModel;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements ReceivePaymentRes
{
    /**
     * @var ReceivePaymentModel
     */
    protected $_receivePaymentModel;

    /**
     * ReceiveResponseXML constructor.
     * @param ReceivePaymentModel $receivePaymentModel
     * @param QueueCollection $queueCollection
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        ReceivePaymentModel $receivePaymentModel,
        QueueCollection $queueCollection,
        QueueModelFactory $queueModelFactory, Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory, ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        $this->_receivePaymentModel = $receivePaymentModel;
        parent::__construct($queueCollection, $queueModelFactory, $configuration, $sessionConnectFactory, $parserXml, $qbLogger);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function saveEntityData($listQueue)
    {
        $receivePaymentsData = [];

        foreach ($listQueue as $queue) {
            if ($queue[QueueInterface::ACTION] == QueueInterface::ACTION_DELETE) {
                continue;
            }
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));

            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                $receivePaymentsData[] = [
                    ReceivePaymentInterface::MAGENTO_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID],
                    ReceivePaymentInterface::LIST_ID => $this->getData(self::XML_RECEIVE_PAYMENT_TXN_ID),
                    ReceivePaymentInterface::EDIT_SEQUENCE => $this->getData(self::XML_RECEIVE_PAYMENT_EDIT_SEQUENCE)
                ];
            }
        }

        $this->_receivePaymentModel->create()->setReceivePaymentsData($receivePaymentsData)->save();
    }
}
