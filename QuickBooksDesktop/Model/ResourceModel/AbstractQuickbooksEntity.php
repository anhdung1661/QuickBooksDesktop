<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 18/03/2020 13:54
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AbstractQuickbooksEntity
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
abstract class AbstractQuickbooksEntity extends AbstractDb
{
    /**
     * @param $tableName
     * @param $data
     * @return int
     */
    protected function saveQuickbooksEntity($tableName, $data)
    {
        $row = reset($data);
        if (!is_array($row)) {
            return $this->getConnection()->insert($this->getTable($tableName), $data);

        }
        unset($row);

        $rowCount = $this->getConnection()->insertOnDuplicate(
            $this->getTable($tableName),
            $data,
            ['list_id', 'edit_sequence', 'note']
        );

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $qbLogger = $objectManager->create(\Magenest\QuickBooksDesktop\Helper\QuickbooksLogger::class);
        $qbLogger->info('Invoice save entity number: ' . $rowCount);

        return $rowCount;
    }
}