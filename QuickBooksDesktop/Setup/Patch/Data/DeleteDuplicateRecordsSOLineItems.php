<?php
declare(strict_types=1);

/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Booking & Reservation extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 15/01/2021 09:29
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;

class DeleteDuplicateRecordsSOLineItems implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdateUniqueFieldsSalesOrderLineItems constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        // delete duplicate records in table
        $uniqueRecords = new \Zend_Db_Expr('DELETE origin FROM ' . $this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME) . ' AS origin LEFT JOIN (SELECT MAX(id) as id FROM ' . $this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME). ' GROUP BY company_id, order_txn_id, sku) as minimize ON origin.id = minimize.id WHERE minimize.id IS NULL');
        $this->moduleDataSetup->getConnection()->query($uniqueRecords);
        $this->moduleDataSetup->endSetup();
    }
}