<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 09:16
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\ShippingMethod\CollectionFactory as ShippingMethodsCollectionFactory;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd
 */
class SendRequestXML extends AbstractQueueAddSendRequestXML implements ShipMethodAddReq
{
    /**
     * @var ShippingMethodsCollectionFactory
     */
    protected $_shippingMethodsCollection;

    /**
     * SendRequestXML constructor.
     * @param ShippingMethodsCollectionFactory $shippingMethodsCollection
     * @param SearchCriteriaInterface $searchCriteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param QueueCollectionFactory $queueCollection
     * @param QueueFactory $queueFactory
     * @param Configuration $configuration
     * @param SessionModel $sessionCollection
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        ShippingMethodsCollectionFactory $shippingMethodsCollection,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory,
        Configuration $configuration,
        SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($searchCriteria, $filterGroup, $filterBuilder, $queueCollection, $queueFactory, $configuration, $sessionCollection, $qbLogger);
        $this->_shippingMethodsCollection = $shippingMethodsCollection;
    }

    /**
     * @inheritDoc
     */
    protected function prepareBodyXml()
    {
        $listShippingMethods = $this->_shippingMethodsCollection->create()
            ->addFieldToFilter(ShippingMethodInterface::ENTITY_ID, ['in' => ProcessArray::getColValueFromThreeDimensional($this->getListEntityInQueue(), [QueueInterface::MAGENTO_ENTITY_ID])])
            ->getData();

        $xml = '';
        foreach ($listShippingMethods as $shippingMethod) {
            $queue = $this->getQueueByEntityId($shippingMethod[ShippingMethodInterface::ENTITY_ID]);
            $xml .= '<' . self::XML_SHIP_METHOD_ADD . 'Rq' . $this->getRequestId($queue[QueueInterface::ENTITY_ID] ?? null). '>';
            $xml .= '<' . self::XML_SHIP_METHOD_ADD . '>';
            $xml .= BuildXML::buildXml(self::XML_SHIP_METHOD_NAME, $shippingMethod[ShippingMethodInterface::SHIPPING_ID]);
            $xml .= '</' . self::XML_SHIP_METHOD_ADD . '>';
            $xml .= '</' . self::XML_SHIP_METHOD_ADD . 'Rq>';
        }

        return $xml;
    }

    /**
     * This function doesn't use for Shipping method
     * @param $queue
     */
    protected function processAQueue($queue)
    {
        // TODO: Implement processAQueue() method.
    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_SHIPPING_METHOD;
    }
}
