<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 14/10/2020 16:21
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\CreditMemoInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateCreditMemo
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateCreditMemo extends IntegrateData
{

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();
        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_mapping'))) {
            $columns = [
                CreditMemoInterface::COMPANY_ID => 'old_table.company_id',
                CreditMemoInterface::MAGENTO_ID => 'old_table.entity_id',
                CreditMemoInterface::LIST_ID => 'old_table.list_id',
                CreditMemoInterface::EDIT_SEQUENCE => 'old_table.edit_sequence'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_mapping')], $columns)->where('old_table.type = 8');
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(CreditMemoInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }

        $connection->endSetup();
    }
}
