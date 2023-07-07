<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 14/10/2020 16:31
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateShippingMethod
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateShippingMethod extends IntegrateData
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
                ShippingMethodInterface::COMPANY_ID => 'old_table.company_id',
                ShippingMethodInterface::SHIPPING_ID => 'old_table.payment',
                ShippingMethodInterface::LIST_ID => 'old_table.list_id',
                ShippingMethodInterface::EDIT_SEQUENCE => 'old_table.edit_sequence'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_mapping')], $columns)->where('old_table.type = 9');
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(ShippingMethodInterface::TABLE_NAME), array_keys($columns),true);
            $connection->query($insertQuery);
        }

        $connection->endSetup();
    }
}
