<?php

namespace Magenest\QuickBooksDesktop\Cron;

use Magento\Framework\App\ResourceConnection;

/**
 * Class BlockQueue
 * @package Magenest\QuickBooksDesktop\Cron
 */
class BlockQueue
{
    /**
     * @var ResourceConnection
     */
    protected $connection;

    /**
     * BlockQueue constructor.
     * @param ResourceConnection $connection
     */
    public function __construct(
        ResourceConnection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * Block queues have message look like list element already in use.
     */
    public function execute()
    {
        $listElementAlreadyInUseSql = $this->connection->getConnection()
            ->select()->from([$this->connection->getTableName('magenest_qbd__queue')], 'queue_id')
            ->where('msg LIKE ?', '%list element is already in use%');
        $queueIdsNeedBlock = $this->connection->getConnection()->fetchCol($listElementAlreadyInUseSql);
        if ($queueIdsNeedBlock) {
            $this->connection->getConnection()->update(
                $this->connection->getTableName('magenest_qbd__queue'),
                ['status' => 5, 'msg' => ''],
                ['queue_id IN (?)' => $queueIdsNeedBlock]
            );
        }
    }
}