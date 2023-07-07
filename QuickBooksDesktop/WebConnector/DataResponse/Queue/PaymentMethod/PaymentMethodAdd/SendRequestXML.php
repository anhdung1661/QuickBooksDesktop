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
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\PaymentMethod\CollectionFactory as PaymentMethodsCollectionFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd
 */
class SendRequestXML extends AbstractQueueAddSendRequestXML implements PaymentMethodAddReq
{
    /**
     * @var PaymentMethodsCollectionFactory
     */
    protected $_paymentMethodsCollection;

    /**
     * SendRequestXML constructor.
     * @param PaymentMethodsCollectionFactory $paymentMethodsCollection
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
        PaymentMethodsCollectionFactory $paymentMethodsCollection,
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
        $this->_paymentMethodsCollection = $paymentMethodsCollection;
    }

    /**
     * @inheritDoc
     */
    protected function prepareBodyXml()
    {
        $listPaymentMethod = $this->_paymentMethodsCollection->create()
            ->addFieldToFilter(PaymentMethodInterface::ENTITY_ID, ['in' => ProcessArray::getColValueFromThreeDimensional($this->getListEntityInQueue(), [QueueInterface::MAGENTO_ENTITY_ID])])
            ->getData();

        $xml = '';
        foreach ($listPaymentMethod as $paymentMethod) {
            $queue = $this->getQueueByEntityId($paymentMethod[PaymentMethodInterface::ENTITY_ID]);
            $xml .= '<' . self::XML_PAYMENT_METHOD_ADD . 'Rq' . $this->getRequestId($queue[QueueInterface::ENTITY_ID] ?? null) . '>';
            $xml .= '<' . self::XML_PAYMENT_METHOD_ADD . '>';
            $xml .= BuildXML::buildXml(self::XML_PAYMENT_METHOD_NAME, $paymentMethod[PaymentMethodInterface::PAYMENT_METHOD]);
            $xml .= BuildXML::buildXml(self::XML_PAYMENT_METHOD_TYPE, self::XML_PAYMENT_METHOD_TYPE_ECHECK);
            $xml .= '</' . self::XML_PAYMENT_METHOD_ADD . '>';
            $xml .= '</' . self::XML_PAYMENT_METHOD_ADD . 'Rq>';
        }
        return $xml;
    }

    /**
     * This function doesn't use for Payment method
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
        return QueueInterface::PRIORITY_PAYMENT_METHOD;
    }
}
