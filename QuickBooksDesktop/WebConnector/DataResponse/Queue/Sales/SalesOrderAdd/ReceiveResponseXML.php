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
 * @time: 28/04/2020 09:42
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SalesOrderFactory as SalesOrderModelFactory;
use Magenest\QuickBooksDesktop\Model\SalesOrderLineItemFactory as SalesOrderLineItemModelFactory;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements SalesOrderAddRes
{
    /**
     * @var SalesOrderModelFactory
     */
    protected $_salesOrderModel;

    /**
     * @var SalesOrderLineItemModelFactory
     */
    protected $_salesOrderLineItemModel;

    /**
     * ReceiveResponseXML constructor.
     * @param SalesOrderModelFactory $salesOrderFactory
     * @param SalesOrderLineItemModelFactory $salesOrderLineItemFactory
     * @param QueueCollection $queueCollection
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        SalesOrderModelFactory $salesOrderFactory,
        SalesOrderLineItemModelFactory $salesOrderLineItemFactory,
        QueueCollection $queueCollection,
        QueueModelFactory $queueModelFactory, Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory, ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        $this->_salesOrderModel = $salesOrderFactory;
        $this->_salesOrderLineItemModel = $salesOrderLineItemFactory;
        parent::__construct($queueCollection, $queueModelFactory, $configuration, $sessionConnectFactory, $parserXml, $qbLogger);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function saveEntityData($listQueue)
    {
        $salesOrder = [];
        $salesOrderLineItems = [];
        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));
            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                $orderListId = $this->getData(self::XML_SALES_ORDER_TXN_ID);
                // save sales order information which will use later
                $salesOrder[] = [
                    SalesOrderInterface::MAGENTO_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID],
                    SalesOrderInterface::LIST_ID => $orderListId,
                    SalesOrderInterface::EDIT_SEQUENCE => $this->getData(self::XML_SALES_ORDER_EDIT_SEQUENCE)
                ];

                $listItems = $this->getData(self::XML_SALES_ORDER_LINE_RET);
                if (isset($listItems[self::XML_SALES_ORDER_TXN_LINE_ID])) {
                    $listItems = [$listItems];
                }

                foreach ($listItems as $lineItem) {
                    $itemSku = $lineItem['ItemRef']['FullName'];
                    if ($itemSku == $this->_configuration->getDiscountItemName()) {
                        if (!empty($lineItem['SalesTaxCodeRef']['FullName']) && !in_array($lineItem['SalesTaxCodeRef']['FullName'], ['Non', 'E'])) {
                            $itemSku .= self::TAXABLE_DISCOUNT_ITEM;
                        }
                    }
                    $salesOrderLineItems[] = [
                        SalesOrderLineItemInterface::ORDER_TXN_ID => $orderListId,
                        SalesOrderLineItemInterface::TXN_LINE_ID => $lineItem[self::XML_SALES_ORDER_TXN_LINE_ID],
                        SalesOrderLineItemInterface::ITEM_LIST_ID => $lineItem['ItemRef']['ListID'],
                        SalesOrderLineItemInterface::ITEM_SKU => $itemSku,
                    ];
                }
            }
        }

        // save to database
        $this->_salesOrderModel->create()->setSalesOrderData($salesOrder)->save();
        $this->_salesOrderLineItemModel->create()->setSalesOrderLineItem($salesOrderLineItems)->save();
    }
}
