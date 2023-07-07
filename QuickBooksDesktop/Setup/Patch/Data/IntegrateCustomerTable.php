<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 14/10/2020 10:43
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateCustomerTable
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateCustomerTable extends IntegrateData
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();
        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_mapping'))) {
            $this->integrateQuickBooksCustomer($connection);
            $this->integrateCustomerMapping($connection);
        }

        $connection->endSetup();
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateQuickBooksCustomer(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        $columns = [
            CustomerInterface::COMPANY_ID => 'old_table.company_id',
            CustomerInterface::CUSTOMER_NAME => 'old_table.payment',
            CustomerInterface::LIST_ID => 'old_table.list_id',
            CustomerInterface::EDIT_SEQUENCE => 'old_table.edit_sequence'
        ];

        $select = $connection->select();
        $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_mapping')], $columns)->where('old_table.type in (1,11)');
        $select->useStraightJoin();
        $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(CustomerInterface::TABLE_NAME), array_keys($columns),true);
        $connection->query($insertQuery);
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateCustomerMapping(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        $columns = [
            CustomerMappingInterface::M2_ENTITY_ID => 'old_table.entity_id',
            CustomerMappingInterface::QB_ID => 'new_table.'.CustomerInterface::ENTITY_ID,
            CustomerMappingInterface::M2_ENTITY_TYPE => new \Zend_Db_Expr('IF(old_table.type = 1, ' . CustomerMappingInterface::M2_ENTITY_TYPE_CUSTOMER . ', ' . CustomerMappingInterface::M2_ENTITY_TYPE_GUEST . ')')
        ];

        $select = $connection->select();
        $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_mapping')], $columns)
            ->joinLeft(
                ['new_table' => $this->moduleDataSetup->getTable(CustomerInterface::TABLE_NAME)],
                'old_table.list_id = new_table.'.CustomerInterface::LIST_ID,
                []
            )
            ->where('old_table.type in (1,11)');
        $select->useStraightJoin();
        $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(CustomerMappingInterface::TABLE_NAME), array_keys($columns),true);
        $connection->query($insertQuery);
    }
}
