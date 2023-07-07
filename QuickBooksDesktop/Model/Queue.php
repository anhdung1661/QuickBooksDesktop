<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Queue
 * @package Magenest\QuickBooksDesktop\Model
 */
class Queue extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = QueueInterface::TABLE_NAME;

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\Queue');
    }

    /**
     * This function will insert multiple records without duplicate the record which exist (company_id, entity_id, action, type)
     *
     * @param $data
     * @return int
     */
    public function insertMultipleQueue($data)
    {
        if (!empty($data)) {
            if (!is_array(reset($data))) {
                $data = [$data];
            }
            return $this->getResource()->insertMultipleRecords($data);
        }
        return 0;
    }

    /**
     * This function will update status, dequeue_time, msg columns of multiple records
     * It will create new records if not found queue_id
     *
     * @param $data
     * @return int
     */
    public function updateMultipleQueue($data)
    {
        if (!empty($data)) {
            if (!is_array(reset($data))) {
                $data = [$data];
            }
            return $this->getResource()->updateMultipleRecord($data);
        }
        return 0;
    }

    /**
     * @param array $listQueueIds
     * @param int $toStatus
     * @param int $isUpdateSuccessQueue
     */
    public function updateStatus($listQueueIds = [], $toStatus = QueueInterface::STATUS_QUEUE, $isUpdateSuccessQueue = 0)
    {
        if (!empty($listQueueIds)) {
            $this->getResource()->deleteRelatedSalesOrder($listQueueIds);
        }
        return $this->getResource()->updateStatus($listQueueIds, $toStatus, $isUpdateSuccessQueue);
    }

    /**
     * Update status of queues being processed
     *
     * @param $toStatus
     * @param $msg
     * @return int
     */
    public function updateStatusOfProcessingQueue($toStatus, $msg)
    {
        return $this->getResource()->updateQueueByStatus([
            QueueInterface::STATUS => $toStatus,
            QueueInterface::MESSAGE => $msg
        ], [QueueInterface::STATUS_PROCESSING]);
    }

    /**
     * @param $queueData
     * @return $this
     */
    public function insertTemporaryTable($queueData)
    {
        $this->getResource()->_createTemporaryTable();
        $this->getResource()->_fillTemporaryTable($queueData);

        return $this;
    }

    /**
     * @param $autoEnterModify
     * @return int
     */
    public function saveProducts($autoEnterModify)
    {
        return $this->getResource()->insertProductsFromTemporaryTable($autoEnterModify);
    }

    /**
     * @param $autoEnterModify
     * @return int
     */
    public function saveCustomers($autoEnterModify)
    {
        return $this->getResource()->insertCustomersFromTemporaryTable($autoEnterModify);
    }

    /**
     * @return int
     */
    public function saveOrders()
    {
        return $this->getResource()->insertOrdersFromTemporaryTable();
    }

    /**
     * @param $autoEnterModify
     * @return int
     */
    public function saveInvoices($autoEnterModify)
    {
        return $this->getResource()->insertInvoicesFromTemporaryTable($autoEnterModify);
    }

    /**
     * @return int
     */
    public function saveReceivePayments($autoEnterModify)
    {
        return $this->getResource()->insertReceivePaymentsFromTemporaryTable($autoEnterModify);
    }

    /**
     * @return int
     */
    public function saveCreditMemos()
    {
        return $this->getResource()->insertCreditMemosFromTemporaryTable();
    }

    /**
     * Delete by magento entity id
     *
     * @param $entityIds
     * @param $type
     * @return int
     */
    public function deleteRow($entityIds, $type = null)
    {
        if (!is_array($entityIds)) {
            $entityIds = [$entityIds];
        }
        $conditions = [
            QueueInterface::MAGENTO_ENTITY_ID . ' = ('.implode(',', $entityIds).')'
        ];
        if ($type) {
            $conditions[] = QueueInterface::MAGENTO_ENTITY_TYPE . ' = ' . $type;
        }
        return $this->getResource()->deleteRow($conditions);
    }

    /**
     * @param array $queueIds
     * @return int
     */
    public function deleteByQueueIds($queueIds = [])
    {
        if (!is_array($queueIds)) {
            $queueIds = [$queueIds];
        }

        $rowAffected = 0;
        if (!empty($queueIds)) {
            $conditions = [
                QueueInterface::ENTITY_ID . ' IN ('.implode(',', $queueIds).')'
            ];

            $this->getResource()->deleteRelatedSalesOrder($queueIds);
            $rowAffected = $this->getResource()->deleteRow($conditions);
        }
        return $rowAffected;
    }
}
