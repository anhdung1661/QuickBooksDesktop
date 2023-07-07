<?php

namespace Magenest\QuickBooksDesktop\Setup\Patch\Schema;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\Data\DeleteDuplicateTaxMapping;

/**
 * Class DropUniqueIndexToTaxMapping
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Schema
 */
class DropUniqueIndexToTaxMapping implements SchemaPatchInterface
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
        return [DeleteDuplicateTaxMapping::class];
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
        $this->moduleDataSetup->getConnection()->addIndex(
            $this->moduleDataSetup->getTable(TaxesMappingInterface::TABLE_NAME),
            'MAGENEST_QBD__TAX_MAPPING_TAX_ITEM_MAPPING_UNIQUE',
            [TaxesMappingInterface::MAGENTO_ID],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        );

        $this->moduleDataSetup->endSetup();
    }
}