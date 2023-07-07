<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 14:54
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd;

use Magenest\QuickBooksDesktop\Api\Data\PaymentMethodInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\PaymentMethodFactory;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ReceiveResponse
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements PaymentMethodAddRes
{
    /**
     * @var PaymentMethodFactory
     */
    protected $_paymentMethodsModel;

    /**
     * ReceiveResponseXML constructor.
     * @param QueueCollection $queueCollection
     * @param PaymentMethodFactory $paymentMethodFactory
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        QueueCollection $queueCollection,
        PaymentMethodFactory $paymentMethodFactory,
        QueueModelFactory $queueModelFactory,
        Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($queueCollection, $queueModelFactory, $configuration, $sessionConnectFactory, $parserXml, $qbLogger);
        $this->_paymentMethodsModel = $paymentMethodFactory;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function saveEntityData($listQueue)
    {
        $paymentMethodsData = [];
        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));

            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                $paymentMethodsData[] = [
                    PaymentMethodInterface::ENTITY_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID],
                    PaymentMethodInterface::LIST_ID => $this->getData(PaymentMethodAddRes::XML_PAYMENT_METHOD_ADD_LIST_ID),
                    PaymentMethodInterface::EDIT_SEQUENCE => $this->getData(PaymentMethodAddRes::XML_PAYMENT_METHOD_ADD_EDIT_SEQUENCE),
                    PaymentMethodInterface::NOTE => $this->getData(PaymentMethodAddRes::XML_PAYMENT_METHOD_ADD_NAME),
                ];
            }
        }

        $this->_paymentMethodsModel->create()->setPaymentMethodsData($paymentMethodsData)->updateQuickbooksInformation();
    }
}
