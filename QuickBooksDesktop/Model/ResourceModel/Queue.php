<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\QuickbooksEntityInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;
use Magento\Catalog\Model\Indexer\Product\Flat\Table\BuilderInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Queue
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class Queue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const QUEUE_TEMPORARY_TABLE = 'magenest_qbd_queue_tmp';

    const AUTO_ENTER_MODIFY = 1;

    const LIST_QUEUE_STATUS = [
        QueueInterface::STATUS_SUCCESS,
        QueueInterface::STATUS_QUEUE,
        QueueInterface::STATUS_FAIL,
        QueueInterface::STATUS_PROCESSING,
        QueueInterface::STATUS_BLOCKED
    ];

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connectionTemp;

    /**
     * @var BuilderInterfaceFactory
     */
    private $tableBuilderFactory;

    /**
     * Queue constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param BuilderInterfaceFactory|null $tableBuilderFactory
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        BuilderInterfaceFactory $tableBuilderFactory = null,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->connectionTemp = $resource->getConnection();
        $this->tableBuilderFactory = $tableBuilderFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(BuilderInterfaceFactory::class);
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(QueueInterface::TABLE_NAME, QueueInterface::ENTITY_ID);
    }

    /**
     * @param $data
     * @return int
     * @throws LocalizedException
     */
    public function updateMultipleRecord($data)
    {
        return $this->getConnection()
            ->insertOnDuplicate($this->getMainTable(), $data, [QueueInterface::STATUS, QueueInterface::MESSAGE]);
    }

    /**
     * @param array $listQueueIds
     * @param int $status
     * @param int $isUpdateSuccessQueue
     * @return int
     * @throws LocalizedException
     */
    public function updateStatus($listQueueIds = [], $status = QueueInterface::STATUS_QUEUE, $isUpdateSuccessQueue = 0)
    {
        $where = [
            QueueInterface::ENTITY_ID . ' IN (' .implode(',', $listQueueIds). ') '
        ];
        if (!$isUpdateSuccessQueue) {
            $where[] = QueueInterface::STATUS . ' NOT IN (' .QueueInterface::STATUS_SUCCESS. ')';
        }
        return $this->getConnection()->update(
            $this->getMainTable(),
            [QueueInterface::STATUS => $status, QueueInterface::MESSAGE => ''],
            $where
        );
    }

    /**
     * Update all of the records in Queue by status
     *
     * @param $dataUpdate
     * @param $fromStatus
     * @return int
     * @throws LocalizedException
     */
    public function updateQueueByStatus($dataUpdate, $fromStatus)
    {
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, [QueueInterface::STATUS . ' in (' . implode(',', $fromStatus) .')']);
    }

    /**
     * @param $data
     * @return int
     * @throws LocalizedException
     */
    public function insertMultipleRecords($data)
    {
        return $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data, []);
    }

    /**
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function _createTemporaryTable()
    {
        $temporaryTableBuilder = $this->tableBuilderFactory->create([
            'connection' => $this->connectionTemp,
            'tableName' => self::QUEUE_TEMPORARY_TABLE
        ]);
        $temporaryTableBuilder->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
            ]
        )->addColumn(
            QueueInterface::MAGENTO_ENTITY_TYPE,
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null
        )->addColumn(
            QueueInterface::MAGENTO_ENTITY_ID,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null
        );

        $this->connectionTemp->dropTemporaryTable(self::QUEUE_TEMPORARY_TABLE);
        $this->connectionTemp->createTemporaryTable($temporaryTableBuilder->getTable());

        return $this;
    }

    /**
     * @param $queueData
     * @return int
     */
    public function _fillTemporaryTable($queueData)
    {
        if (!empty($queueData)) {
            return $this->connectionTemp->insertArray(
                $this->connectionTemp->getTableName(self::QUEUE_TEMPORARY_TABLE),
                [QueueInterface::MAGENTO_ENTITY_ID, QueueInterface::MAGENTO_ENTITY_TYPE],
                $queueData
            );
        }

        return 0;
    }

    /**
     * @param int $autoEnterModify
     * @return int
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertProductsFromTemporaryTable($autoEnterModify = 0)
    {
        $columns = [
            QueueInterface::MAGENTO_ENTITY_ID => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID,
            QueueInterface::MAGENTO_ENTITY_TYPE => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE,
            QueueInterface::ACTION => new \Zend_Db_Expr($autoEnterModify ? 'IF(ISNULL(mapping_table.list_id), '. QueueInterface::ACTION_ADD .', '. QueueInterface::ACTION_MODIFY .')' : QueueInterface::ACTION_ADD),
            QueueInterface::COMPANY_ID => '(' . $this->getActiveCompanySql() . ')',
            QueueInterface::STATUS => new \Zend_Db_Expr(QueueInterface::STATUS_QUEUE),
            QueueInterface::PRIORITY => new \Zend_Db_Expr(QueueInterface::PRIORITY_PRODUCT)
        ];

        $select = $this->connectionTemp->select();

        $itemsMappingData = $this->connectionTemp->select();
        $itemsMappingData->from(
            ['mapping_table' => $this->getTable(ItemMappingInterface::TABLE_NAME)],
            [ItemMappingInterface::M2_PRODUCT_ID, ItemMappingInterface::QB_ITEM_ID]
        )->joinLeft(['quickbooks_data' => $this->getTable(ItemInterface::TABLE_NAME)],
            'mapping_table.qb_item_id = quickbooks_data.id',
            [QuickbooksEntityInterface::LIST_ID, QuickbooksEntityInterface::COMPANY_ID]
        );

        $select->from(
            ['temp_table' => $this->connectionTemp->getTableName(self::QUEUE_TEMPORARY_TABLE)],
            $columns
        )->joinLeft(
            ['queue_table' => $this->getTable(QueueInterface::TABLE_NAME)],
            'queue_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' AND '
            . 'queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')',
            []
        )->joinLeft(
            ['mapping_table' => $itemsMappingData],
            'mapping_table.' .ItemMappingInterface::M2_PRODUCT_ID. ' = temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.company_id = mapping_table.company_id',
            []
        )->where(
            'queue_table.' . QuickbooksEntityInterface::COMPANY_ID . ' IS NULL OR queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')'
        );
        if (!$autoEnterModify) {
            $select->where('queue_table.status IS NULL OR queue_table.status NOT IN (?)', self::LIST_QUEUE_STATUS);
        }
        $select->useStraightJoin();
        // important!
        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns),true);
        return $this->connectionTemp->query($insertQuery)->rowCount();
    }

    /**
     * @param int $autoEnterModify
     * @return int
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertCustomersFromTemporaryTable($autoEnterModify = 0)
    {
        $columns = [
            QueueInterface::MAGENTO_ENTITY_ID => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID,
            QueueInterface::MAGENTO_ENTITY_TYPE => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE,
            QueueInterface::ACTION => new \Zend_Db_Expr($autoEnterModify ? 'IF(ISNULL(mapping_table.list_id), '. QueueInterface::ACTION_ADD .', '. QueueInterface::ACTION_MODIFY .')' : QueueInterface::ACTION_ADD),
            QueueInterface::COMPANY_ID => '(' . $this->getActiveCompanySql() . ')',
            QueueInterface::STATUS => new \Zend_Db_Expr(QueueInterface::STATUS_QUEUE),
            QueueInterface::PRIORITY => new \Zend_Db_Expr(QueueInterface::PRIORITY_CUSTOMER)
        ];

        $select = $this->connectionTemp->select();

        $customersMappingData = $this->connectionTemp->select();
        $customersMappingData->from(
            ['mapping_table' => $this->getTable(CustomerMappingInterface::TABLE_NAME)],
            [CustomerMappingInterface::M2_ENTITY_ID, CustomerMappingInterface::QB_ID, CustomerMappingInterface::M2_ENTITY_TYPE]
        )->joinLeft(['quickbooks_data' => $this->getTable(CustomerInterface::TABLE_NAME)],
            'mapping_table.' . CustomerMappingInterface::QB_ID . ' = quickbooks_data.' . CustomerInterface::ENTITY_ID,
            [QuickbooksEntityInterface::LIST_ID, QuickbooksEntityInterface::COMPANY_ID]
        );

        $select->from(
            ['temp_table' => $this->connectionTemp->getTableName(self::QUEUE_TEMPORARY_TABLE)],
            $columns
        )->joinLeft(
            ['queue_table' => $this->getTable(QueueInterface::TABLE_NAME)],
            'queue_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' AND '
            . 'queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')',
            []
        )->joinLeft(
            ['mapping_table' => $customersMappingData],
            'mapping_table.' .CustomerMappingInterface::M2_ENTITY_ID. ' = temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'mapping_table.' .CustomerMappingInterface::M2_ENTITY_TYPE. ' = temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' AND '
            . 'queue_table.company_id = mapping_table.company_id',
            []
        )->where(
            'queue_table.' . QuickbooksEntityInterface::COMPANY_ID . ' IS NULL OR queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')'
        );
        if (!$autoEnterModify) {
            $select->where('queue_table.status IS NULL OR queue_table.status NOT IN (?)', self::LIST_QUEUE_STATUS);
        }

        $select->useStraightJoin();
        // important!
        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns),true);
        return $this->connectionTemp->query($insertQuery)->rowCount();
    }

    /**
     * @return int
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertOrdersFromTemporaryTable()
    {
        $columns = [
            QueueInterface::MAGENTO_ENTITY_ID => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID,
            QueueInterface::MAGENTO_ENTITY_TYPE => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE,
            QueueInterface::ACTION => new \Zend_Db_Expr(QueueInterface::ACTION_ADD),
            QueueInterface::COMPANY_ID => '(' . $this->getActiveCompanySql() . ')',
            QueueInterface::STATUS => new \Zend_Db_Expr(QueueInterface::STATUS_QUEUE),
            QueueInterface::PRIORITY => new \Zend_Db_Expr(QueueInterface::PRIORITY_SALES_ORDER)
        ];


        $select = $this->connectionTemp->select();
        $select->from(
            ['temp_table' => $this->connectionTemp->getTableName(self::QUEUE_TEMPORARY_TABLE)],
            $columns
        )->joinLeft(
            ['queue_table' => $this->getTable(QueueInterface::TABLE_NAME)],
            'queue_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' AND '
            . 'queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')',
            []
        )->joinLeft(
            ['mapping_table' => $this->getTable(SalesOrderInterface::TABLE_NAME)],
            'mapping_table.' .SalesOrderInterface::MAGENTO_ID. ' = temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.company_id = mapping_table.company_id',
            []
        )->where(
            'queue_table.' . QuickbooksEntityInterface::COMPANY_ID . ' IS NULL OR queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')'
        )->where('queue_table.status IS NULL OR queue_table.status NOT IN (?)', self::LIST_QUEUE_STATUS);

        $select->useStraightJoin();
        // important!
        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns),true);
        return $this->connectionTemp->query($insertQuery)->rowCount();
    }

    /**
     * @param $autoEnterModify
     * @return int
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertInvoicesFromTemporaryTable($autoEnterModify)
    {
        $actionCondition = new \Zend_Db_Expr(QueueInterface::ACTION_ADD);
        $priority = new \Zend_Db_Expr(QueueInterface::PRIORITY_INVOICE);
        if ($autoEnterModify) {
            $actionCondition = new \Zend_Db_Expr(QueueInterface::ACTION_MODIFY);
            $priority = new \Zend_Db_Expr(QueueInterface::PRIORITY_MODIFY_INVOICE);
        }
        $columns = [
            QueueInterface::MAGENTO_ENTITY_ID => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID,
            QueueInterface::MAGENTO_ENTITY_TYPE => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE,
            QueueInterface::ACTION => $actionCondition,
            QueueInterface::COMPANY_ID => '(' . $this->getActiveCompanySql() . ')',
            QueueInterface::STATUS => new \Zend_Db_Expr(QueueInterface::STATUS_QUEUE),
            QueueInterface::PRIORITY => $priority
        ];

        $select = $this->connectionTemp->select();
        $select->from(
            ['temp_table' => $this->connectionTemp->getTableName(self::QUEUE_TEMPORARY_TABLE)],
            $columns
        )->joinLeft(
            ['queue_table' => $this->getTable(QueueInterface::TABLE_NAME)],
            'queue_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' AND '
            . 'queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')',
            []
        )->where(
            'queue_table.' . QuickbooksEntityInterface::COMPANY_ID . ' IS NULL OR queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')'
        );

        // If item already exists in queue, not add.
        if (!$autoEnterModify) {
            $select->where('queue_table.status IS NULL OR queue_table.status NOT IN (?)', self::LIST_QUEUE_STATUS);
        }

        $select->useStraightJoin();
        // important!
        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns),true);
        return $this->connectionTemp->query($insertQuery)->rowCount();
    }

    /**
     * @return int
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertReceivePaymentsFromTemporaryTable($autoEnterModify)
    {
        $priority = new \Zend_Db_Expr(QueueInterface::PRIORITY_RECEIVE_PAYMENT);
        $action = new \Zend_Db_Expr(QueueInterface::ACTION_ADD);
        if ($autoEnterModify == QueueInterface::ACTION_DELETE) {
            $priority = new \Zend_Db_Expr(QueueInterface::PRIORITY_DELETE_PAYMENT);
            $action = new \Zend_Db_Expr(QueueInterface::ACTION_DELETE);
        }
        $columns = [
            QueueInterface::MAGENTO_ENTITY_ID => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID,
            QueueInterface::MAGENTO_ENTITY_TYPE => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE,
            QueueInterface::ACTION => $action,
            QueueInterface::COMPANY_ID => '(' . $this->getActiveCompanySql() . ')',
            QueueInterface::STATUS => new \Zend_Db_Expr(QueueInterface::STATUS_QUEUE),
            QueueInterface::PRIORITY => $priority
        ];

        $select = $this->connectionTemp->select();
        $select->from(
            ['temp_table' => $this->connectionTemp->getTableName(self::QUEUE_TEMPORARY_TABLE)],
            $columns
        )->joinLeft(
            ['queue_table' => $this->getTable(QueueInterface::TABLE_NAME)],
            'queue_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE. ' AND '
            . 'queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')',
            []
        )->where(
            'queue_table.' . QuickbooksEntityInterface::COMPANY_ID . ' IS NULL OR queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')'
        );

        if (!$autoEnterModify) {
            $select->where('queue_table.status IS NULL OR queue_table.status NOT IN (?)', self::LIST_QUEUE_STATUS);
        }
        $select->useStraightJoin();
        // important!
        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns),true);
        return $this->connectionTemp->query($insertQuery)->rowCount();
    }

    /**
     * @return int
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertCreditMemosFromTemporaryTable()
    {
        $columns = [
            QueueInterface::MAGENTO_ENTITY_ID => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID,
            QueueInterface::MAGENTO_ENTITY_TYPE => 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE,
            QueueInterface::ACTION => new \Zend_Db_Expr(QueueInterface::ACTION_ADD),
            QueueInterface::COMPANY_ID => '(' . $this->getActiveCompanySql() . ')',
            QueueInterface::STATUS => new \Zend_Db_Expr(QueueInterface::STATUS_QUEUE),
            QueueInterface::PRIORITY => new \Zend_Db_Expr(QueueInterface::TYPE_CREDIT_MEMO)
        ];

        $select = $this->connectionTemp->select();
        $select->from(
            ['temp_table' => $this->connectionTemp->getTableName(self::QUEUE_TEMPORARY_TABLE)],
            $columns
        )->joinLeft(
            ['queue_table' => $this->getTable(QueueInterface::TABLE_NAME)],
            'queue_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_ID . ' AND '
            . 'queue_table.' . QueueInterface::MAGENTO_ENTITY_TYPE . ' = ' . 'temp_table.' . QueueInterface::MAGENTO_ENTITY_TYPE. ' AND '
            . 'queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')',
            []
        )->where(
            'queue_table.' . QuickbooksEntityInterface::COMPANY_ID . ' IS NULL OR queue_table.'. QuickbooksEntityInterface::COMPANY_ID . '= (' . $this->getActiveCompanySql() . ')'
        )->where('queue_table.status IS NULL OR queue_table.status NOT IN (?)', self::LIST_QUEUE_STATUS);

        $select->useStraightJoin();
        // important!
        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns),true);
        return $this->connectionTemp->query($insertQuery)->rowCount();
    }

    /**
     * @param array $conditions
     * @return int The number of affected rows.
     */
    public function deleteRow($conditions = [])
    {
        $rowAffected = 0;
        if (!empty($conditions)) {
            $conditions[QueueInterface::COMPANY_ID . ' = (?)'] = $this->getActiveCompanySql();
            $rowAffected = $this->getConnection()->delete($this->getTable(QueueInterface::TABLE_NAME), $conditions);
        }
        return $rowAffected;
    }

    /**
     * @return \Zend_Db_Expr
     */
    private function getActiveCompanySql()
    {
        return new \Zend_Db_Expr('SELECT ' . CompanyInterface::ENTITY_ID . ' FROM ' . $this->getTable(CompanyInterface::TABLE_NAME) . ' WHERE ' . CompanyInterface::COMPANY_STATUS_FIELD . ' = ' . CompanyInterface::COMPANY_CONNECTED);
    }

    /**
     * @param $queueIds
     */
    public function deleteRelatedSalesOrder($queueIds)
    {
        $sql = $this->getConnection()->select()
            ->from([$this->getMainTable()], 'entity_id')
            ->where('queue_id IN (?)', $queueIds)
            ->where('type = ?', QueueInterface::TYPE_SALES_ORDER)
            ->where($this->getActiveCompanySql());
        $orderIds = $this->getConnection()->fetchCol($sql);
        $this->getConnection()->delete(
            $this->getTable(SalesOrderInterface::TABLE_NAME),
            [
                'magento_order_id IN (?)' => $orderIds,
                'company_id'    => $this->getActiveCompanySql()
            ]
        );
    }

    /**
     * @param $entityIds
     * @return int
     * @throws LocalizedException
     */
    public function deleteByEntityIds($entityIds)
    {
        return $this->getConnection()->delete(
            $this->getMainTable(),
            [
                'entity_id IN (?)' => $entityIds,
                'type = ?'         => QueueInterface::TYPE_RECEIVE_PAYMENT,
                'company_id'       => $this->getActiveCompanySql()
            ]
        );
    }
}
