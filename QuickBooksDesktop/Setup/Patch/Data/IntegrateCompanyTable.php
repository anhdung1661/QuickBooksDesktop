<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 13/10/2020 15:40
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateCompanyTable
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateCompanyTable extends IntegrateData
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_company'))) {
            $columns = [
                CompanyInterface::ENTITY_ID => 'old_table.company_id',
                CompanyInterface::COMPANY_NAME_FIELD => 'old_table.company_name',
                CompanyInterface::COMPANY_STATUS_FIELD => 'old_table.status',
                CompanyInterface::NOTE_FIELD => 'old_table.note'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_company')],$columns);
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(CompanyInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }

        $connection->endSetup();
    }
}
