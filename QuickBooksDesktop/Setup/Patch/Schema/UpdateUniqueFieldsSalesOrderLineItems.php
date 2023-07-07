<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 15/12/2020 09:26
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Schema;

use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\Data\DeleteDuplicateRecordsSOLineItems;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * Class UpdateUniqueFields
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Schema
 */
class UpdateUniqueFieldsSalesOrderLineItems implements SchemaPatchInterface
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
        return [DeleteDuplicateRecordsSOLineItems::class];
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

        // drop last index if it's installed by db_schema
        $this->moduleDataSetup->getConnection()->dropIndex(
            $this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME),
            'CATALOG_PRODUCT_ENTITY_DATETIME_ENTITY_ID_ATTRIBUTE_ID_STORE_ID'
        );

        // drop last index if it's installed by InstallSchema/UpgradeSchema
        $this->moduleDataSetup->getConnection()->dropIndex(
            $this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME),
            $this->moduleDataSetup->getConnection()->getIndexName(
                $this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME),
                [SalesOrderLineItemInterface::COMPANY_ID, SalesOrderLineItemInterface::ORDER_TXN_ID, SalesOrderLineItemInterface::TXN_LINE_ID, SalesOrderLineItemInterface::ITEM_LIST_ID],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            )
        );

        // fix index
        $this->moduleDataSetup->getConnection()->addIndex(
            $this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME),
            $this->moduleDataSetup->getConnection()->getIndexName(
                $this->moduleDataSetup->getTable(SalesOrderLineItemInterface::TABLE_NAME),
                [SalesOrderLineItemInterface::COMPANY_ID, SalesOrderLineItemInterface::ORDER_TXN_ID, SalesOrderLineItemInterface::ITEM_SKU],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [SalesOrderLineItemInterface::COMPANY_ID, SalesOrderLineItemInterface::ORDER_TXN_ID, SalesOrderLineItemInterface::ITEM_SKU],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        );

        $this->moduleDataSetup->endSetup();
    }
}