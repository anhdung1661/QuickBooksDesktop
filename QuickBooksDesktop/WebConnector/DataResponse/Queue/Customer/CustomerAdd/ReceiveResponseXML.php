<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 10:35
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd;

use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\CustomerFactory;
use Magenest\QuickBooksDesktop\Model\CustomerMappingFactory;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ReceiveResponse
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements CustomerAddRes
{
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var CustomerMappingFactory
     */
    protected $_customerMappingFactory;

    /**
     * ReceiveResponseXML constructor.
     * @param QueueCollection $queueCollection
     * @param CustomerMappingFactory $customerMappingFactory
     * @param CustomerFactory $customerFactory
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        QueueCollection $queueCollection,
        CustomerMappingFactory $customerMappingFactory,
        CustomerFactory $customerFactory,
        QueueModelFactory $queueModelFactory,
        Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($queueCollection, $queueModelFactory, $configuration, $sessionConnectFactory, $parserXml, $qbLogger);
        $this->_customerFactory = $customerFactory;
        $this->_customerMappingFactory = $customerMappingFactory;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function saveEntityData($listQueue)
    {
        $customerData = []; // customer data in Quickbooks
        $customersMappingData = [];

        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));

            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                // prepare customer data
                $customerData[] = [
                    CustomerInterface::CUSTOMER_NAME => $this->getData(self::CUSTOMER_NAME),
                    CustomerInterface::LIST_ID => $this->getData(self::LIST_ID),
                    CustomerInterface::EDIT_SEQUENCE => $this->getData(self::EDIT_SEQUENCE),
                    CustomerInterface::EMAIL => $this->getData(self::CUSTOMER_EMAIL)
                ];

                // prepare customer mapping data
                $customerMappingData = [
                    CustomerInterface::LIST_ID => $this->getData(self::LIST_ID),
                    CustomerMappingInterface::M2_ENTITY_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID]
                ];
                if ($queue[QueueInterface::MAGENTO_ENTITY_TYPE] == QueueInterface::TYPE_CUSTOMER) {
                    $customerMappingData[CustomerMappingInterface::M2_ENTITY_TYPE] = CustomerMappingInterface::M2_ENTITY_TYPE_CUSTOMER;
                } else {
                    $customerMappingData[CustomerMappingInterface::M2_ENTITY_TYPE] = CustomerMappingInterface::M2_ENTITY_TYPE_GUEST;
                }
                $customersMappingData[] = $customerMappingData;
            }
        }

        // save customer
        $this->_customerFactory->create()->setCustomerData($customerData)->save();
        $this->_customerMappingFactory->create()->setCustomerMapping($customersMappingData)->saveMapping();
    }
}
