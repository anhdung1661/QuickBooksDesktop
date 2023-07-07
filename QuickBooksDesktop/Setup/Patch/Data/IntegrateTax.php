<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 13/10/2020 16:57
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateTax
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateTax extends IntegrateData
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $this->integrateTaxTable($connection);
        $this->integrateTaxMappingTable($connection);

        $connection->endSetup();
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateTaxTable(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_tax_code'))) {
            $columns = [
                TaxesInterface::ENTITY_ID => 'old_table.id',
                TaxesInterface::COMPANY_ID => 'old_table.company_id',
                TaxesInterface::TAX_CODE => 'old_table.tax_code',
                TaxesInterface::LIST_ID => 'old_table.list_id',
                TaxesInterface::EDIT_SEQUENCE => 'old_table.edit_sequence'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_tax_code')], $columns);
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(TaxesInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private function integrateTaxMappingTable(\Magento\Framework\DB\Adapter\AdapterInterface $connection)
    {
        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_tax_code_mapping'))) {
            $columns = [
                TaxesMappingInterface::MAGENTO_ID => 'old_table.tax_id',
                TaxesMappingInterface::QUICKBOOKS_ENTITY_ID => 'old_table.code'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_tax_code_mapping')], $columns);
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(TaxesMappingInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }
    }
}
