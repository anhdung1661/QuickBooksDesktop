<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 07/04/2020 01:44
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\Collection as QueueCollection;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\SendRequestXML;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class AbstractQueueAddSendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue
 */
abstract class AbstractQueueAddSendRequestXML extends SendRequestXML
{
    /**
     * @var SearchCriteriaInterface
     */
    protected $_searchCriteria;

    /**
     * @var FilterGroup
     */
    protected $_filterGroup;

    /**
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @var QueueFactory
     */
    protected $_queueFactory;

    protected $_queueCollection;

    /**
     * @var array
     */
    private $listEntityInQueue;

    /**
     * AbstractQueueAddSendRequestXML constructor.
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
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory,
        Configuration $configuration,
        SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($configuration, $sessionCollection, $qbLogger);
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroup = $filterGroup;
        $this->_filterBuilder = $filterBuilder;
        $this->_queueFactory = $queueFactory;
        $this->_queueCollection = $queueCollection;
    }

    /**
     * @inheritDoc
     */
    protected function getOnError()
    {
        return self::ATTR_CONTINUE_ON_ERROR;
    }

    /**
     * @inheritDoc
     */
    protected function getBodyXml()
    {
        $this->changeQueueStatus();
        return $this->prepareBodyXml();
    }

    /**
     * @return string
     */
    protected function prepareBodyXml()
    {
        $xml = '';
        $errors = [];
        foreach ($this->getListEntityInQueue() as $queue) {
            try {
                $xml .= $this->processAQueue($queue);
            } catch (\Exception $exception) {
                    $queueError = array_merge($queue, [QueueInterface::MESSAGE => $exception->getMessage()]);
                    $queueError[QueueInterface::STATUS] = QueueInterface::STATUS_FAIL;
                    $errors[] = $queueError;
                }
        }
        if (!empty($errors)) {
                $this->_queueFactory->create()->updateMultipleQueue($errors);
            }

        return $xml;
    }

    abstract protected function processAQueue($queue);

    /**
     * Profile changes are being processed
     */
    protected function changeQueueStatus()
    {
        $dataUpdate = ProcessArray::getColValueFromThreeDimensional($this->getListEntityInQueue(), [QueueInterface::ENTITY_ID], [QueueInterface::STATUS => QueueInterface::STATUS_PROCESSING]);

        $this->_queueFactory->create()->updateMultipleQueue($dataUpdate);
    }

    /**
     * Get list records in queue will sync to Quickbooks in this request
     */
    protected function getListEntityInQueue()
    {
        if ($this->listEntityInQueue == null) {
            $magentoType = $this->getMagentoType();
            $this->listEntityInQueue = $this->_queueCollection->create()
                ->addFieldToFilter(QueueInterface::STATUS, QueueInterface::STATUS_QUEUE)
                ->addFieldToFilter(QueueInterface::MAGENTO_ENTITY_TYPE, ['in' => is_array($magentoType) ? $magentoType : [$magentoType]])
                ->setOrder(QueueInterface::ENTITY_ID, QueueCollection::SORT_ORDER_ASC)
                ->addFieldToSelect([QueueInterface::ENTITY_ID, QueueInterface::MAGENTO_ENTITY_ID, QueueInterface::MAGENTO_ENTITY_TYPE, QueueInterface::ACTION]);

            if ($this->getMaxRecords()) {
                $this->listEntityInQueue = $this->listEntityInQueue->setPageSize($this->getMaxRecords());
            }

            $this->listEntityInQueue = $this->listEntityInQueue->getData();
            unset($magentoType);
        }

        return $this->listEntityInQueue;
    }

    /**
     * Return value of column type in Queue table
     * Find this value in QueueInterface
     *
     * @return int|array
     */
    abstract protected function getMagentoType();

    /**
     * Return number of records per request
     * return false if sync all
     *
     * @return bool|int
     */
    protected function getMaxRecords()
    {
        if (!empty($maxRecords = $this->_moduleConfig->getMaxRecordsPerAddRequest())) {
            return $maxRecords;
        }
        return false;
    }

    /**
     * Get queueId by entityId in listEntity that are processing
     *
     * @param $entityId
     * @param null $type
     * @return bool|mixed
     */
    protected function getQueueByEntityId($entityId, $type = null)
    {
        foreach ($this->getListEntityInQueue() as $queue) {
            if ($queue[QueueInterface::MAGENTO_ENTITY_ID] == $entityId) {
                if ($type != null) {
                    if ($queue[QueueInterface::MAGENTO_ENTITY_TYPE] == $type) {
                        return $queue;
                    }
                } else {
                    return $queue;
                }
            }
        }
        return false;
    }
}
