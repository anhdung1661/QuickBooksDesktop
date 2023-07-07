<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 13/10/2020 17:22
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateOrder
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateOrder extends IntegrateData
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $this->integrateOrderTable($connection);
        $this->integrateOrderItemTable($connection);

        $connection->endSetup();
    }


    /**
     * Integrate data from Mapping table in old version
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateOrderTable(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_mapping'))) {
            $columns = [
                SalesOrderInterface::COMPANY_ID => 'old_table.company_id',
                SalesOrderInterface::MAGENTO_ID => 'old_table.entity_id',
                SalesOrderInterface::LIST_ID => 'old_table.list_id',
                SalesOrderInterface::EDIT_SEQUENCE => 'old_table.edit_sequence'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_mapping')], $columns)->where('old_table.type = 3');
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(SalesOrderInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateOrderItemTable(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_item_sales_order'))) {
            $columns = [
                SalesOrderLineItemInterface::COMPANY_ID => 'old_table.company_id',
                SalesOrderLineItemInterface::ORDER_TXN_ID => 'old_table.list_id_order',
                SalesOrderLineItemInterface::ITEM_LIST_ID => 'old_table.list_id_item',
                SalesOrderLineItemInterface::TXN_LINE_ID => 'old_table.txn_line_id',
                SalesOrderLineItemInterface::ITEM_SKU => 'old_table.sku'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_item_sales_order')], $columns);
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }
    }
}
