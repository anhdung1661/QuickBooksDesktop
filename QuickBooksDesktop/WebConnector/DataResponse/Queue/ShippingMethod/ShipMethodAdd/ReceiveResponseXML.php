<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 09:17
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\Model\ShippingMethodFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements ShipMethodAddRes
{
    /**
     * @var ShippingMethodFactory
     */
    protected $_shippingMethodsModel;

    /**
     * ReceiveResponseXML constructor.
     * @param ShippingMethodFactory $shippingMethodFactory
     * @param QueueCollection $queueCollection
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        ShippingMethodFactory $shippingMethodFactory,
        QueueCollection $queueCollection,
        QueueModelFactory $queueModelFactory,
        Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($queueCollection, $queueModelFactory, $configuration, $sessionConnectFactory, $parserXml, $qbLogger);
        $this->_shippingMethodsModel = $shippingMethodFactory;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function saveEntityData($listQueue)
    {
        $shipMethodsData = [];

        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));

            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                $shipMethodsData[] = [
                    ShippingMethodInterface::ENTITY_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID],
                    ShippingMethodInterface::LIST_ID => $this->getData(self::XML_SHIP_METHOD_LIST_ID),
                    ShippingMethodInterface::EDIT_SEQUENCE => $this->getData(self::XML_SHIP_METHOD_EDIT_SEQUENCE),
                    ShippingMethodInterface::NOTE => $this->getData(self::XML_SHIP_METHOD_NAME)
                ];
            }
        }

        // save data
        $this->_shippingMethodsModel->create()->setQuickbooksInformation($shipMethodsData)->updateQuickbooksInformation();
    }
}
