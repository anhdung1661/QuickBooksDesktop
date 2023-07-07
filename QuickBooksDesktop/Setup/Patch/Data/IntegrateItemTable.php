<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 14/10/2020 13:54
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateItemTable
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateItemTable extends IntegrateData
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_mapping'))) {
            $this->integrateQuickBooksItem($connection);
            $this->integrateItemMappingTable($connection);
        }

        $connection->endSetup();
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateQuickBooksItem(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        $columns = [
            ItemInterface::COMPANY_ID => 'old_table.company_id',
            ItemInterface::ITEM_NAME => 'old_table.payment',
            ItemInterface::ITEM_TYPE => new \Zend_Db_Expr('IF(queue_table.action_name LIKE "ItemInventory%", ' . ItemInterface::ITEM_TYPE_ITEM_INVENTORY . ', ' . ItemInterface::ITEM_TYPE_ITEM_NONE_INVENTORY . ')'),
            ItemInterface::LIST_ID => 'old_table.list_id',
            ItemInterface::EDIT_SEQUENCE => 'old_table.edit_sequence'
        ];

        $select = $connection->select();
        $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_mapping')], $columns)
            ->joinLeft(
                ['queue_table' => $this->moduleDataSetup->getTable('magenest_qbd_queue')],
                'old_table.entity_id = queue_table.entity_id AND old_table.company_id = queue_table.company_id AND queue_table.type = "Product" AND old_table.type = 2',
                []
            )
            ->where('old_table.type in (2)');
        $select->useStraightJoin();
        $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(ItemInterface::TABLE_NAME), array_keys($columns),true);
        $connection->query($insertQuery);
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateItemMappingTable(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        $columns = [
            ItemMappingInterface::M2_PRODUCT_ID => 'old_table.entity_id',
            ItemMappingInterface::QB_ITEM_ID => 'new_table.' . ItemInterface::ENTITY_ID
        ];

        $select = $connection->select();
        $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_mapping')], $columns)
            ->joinLeft(
                ['new_table' => $this->moduleDataSetup->getTable(ItemInterface::TABLE_NAME)],
                'old_table.list_id = new_table.' . ItemInterface::LIST_ID,
                []
            )
            ->where('old_table.type = 2');
        $select->useStraightJoin();
        $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(ItemMappingInterface::TABLE_NAME), array_keys($columns),true);
        $connection->query($insertQuery);
    }
}
