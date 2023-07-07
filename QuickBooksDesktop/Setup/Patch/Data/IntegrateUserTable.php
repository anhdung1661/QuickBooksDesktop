<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 13/10/2020 16:04
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\UserInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateUserTable
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateUserTable extends IntegrateData
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_user'))) {
            $columns = [
                UserInterface::USERNAME_FIELD => 'old_table.username',
                UserInterface::PASSWORD_FIELD => 'old_table.password',
                UserInterface::STATUS_FIELD => 'old_table.status'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_user')], $columns);
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(UserInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }

        $connection->endSetup();
    }
}
