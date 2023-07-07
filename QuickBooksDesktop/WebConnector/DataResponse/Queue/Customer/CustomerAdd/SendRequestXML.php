<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 10:34
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerPrepareXml;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\GuestPrepareXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Config\Backend\Address\Street;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\OrderRepository;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd
 */
class SendRequestXML extends AbstractQueueAddSendRequestXML implements CustomerAddReq
{
    /**
     * @var CustomerPrepareXml
     */
    private $customerPrepareXml;

    /**
     * @var GuestPrepareXml
     */
    private $guestPrepareXml;

    /**
     * SendRequestXML constructor.
     * @param CustomerPrepareXml $customerPrepareXml
     * @param GuestPrepareXml $guestPrepareXml
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
        CustomerPrepareXml $customerPrepareXml,
        GuestPrepareXml $guestPrepareXml,
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
        $this->customerPrepareXml = $customerPrepareXml;
        $this->guestPrepareXml = $guestPrepareXml;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function prepareBodyXml()
    {
        $xml = $this->customerPrepareXml->setCustomer($this->getListAddInQueue(QueueInterface::TYPE_CUSTOMER))->getXml();
        $xml .= $this->guestPrepareXml->setOrderData($this->getListAddInQueue(QueueInterface::TYPE_GUEST))->getXml();
        $xml .= $this->customerPrepareXml->setCustomer($this->getListModifyInQueue(QueueInterface::TYPE_CUSTOMER))->setAction(QueueInterface::ACTION_MODIFY)->getXml();
        $xml .= $this->guestPrepareXml->setOrderData($this->getListModifyInQueue(QueueInterface::TYPE_GUEST))->setAction(QueueInterface::ACTION_MODIFY)->getXml();
        return $xml;
    }

    /**
     * This function doesn't use for Customer
     *
     * @param $queue
     */
    protected function processAQueue($queue)
    {
        // TODO: Implement processAQueue() method.
    }

    /**
     * @param null $type
     * @return array
     */
    protected function getListAddInQueue($type = null)
    {
        $listEntity = parent::getListEntityInQueue();
        $listQueue = [];
        if ($type == null) {
            // return both customer and guest
            foreach ($listEntity as $queue) {
                if ($queue[QueueInterface::ACTION] == QueueInterface::ACTION_ADD) {
                    $listQueue[] = $queue;
                }
            }
        } else {
            foreach ($listEntity as $queue) {
                if ($queue[QueueInterface::MAGENTO_ENTITY_TYPE] == $type && $queue[QueueInterface::ACTION] == QueueInterface::ACTION_ADD) {
                    $listQueue[] = $queue;
                }
            }
        }

        return $listQueue;
    }

    /**
     * @param null $type
     * @return array
     */
    protected function getListModifyInQueue($type = null)
    {
        $listEntity = parent::getListEntityInQueue();
        $listQueue = [];
        if ($type == null) {
            // return both customer and guest
            foreach ($listEntity as $queue) {
                if ($queue[QueueInterface::ACTION] == QueueInterface::ACTION_MODIFY) {
                    $listQueue[] = $queue;
                }
            }
        } else {
            foreach ($listEntity as $queue) {
                if ($queue[QueueInterface::MAGENTO_ENTITY_TYPE] == $type && $queue[QueueInterface::ACTION] == QueueInterface::ACTION_MODIFY) {
                    $listQueue[] = $queue;
                }
            }
        }

        return $listQueue;
    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return [QueueInterface::TYPE_CUSTOMER, QueueInterface::TYPE_GUEST];
    }
}
