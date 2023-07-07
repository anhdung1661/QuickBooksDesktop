<?php

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;

/**
 * Class DeleteDuplicateTaxMapping
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class DeleteDuplicateTaxMapping implements DataPatchInterface
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
        $uniqueRecords = new \Zend_Db_Expr('DELETE origin FROM ' . $this->moduleDataSetup->getTable(TaxesMappingInterface::TABLE_NAME) . ' AS origin LEFT JOIN (SELECT MAX(id) as id FROM ' . $this->moduleDataSetup->getTable(TaxesMappingInterface::TABLE_NAME). ' GROUP BY magento_tax_id) as minimize ON origin.id = minimize.id WHERE minimize.id IS NULL');
        $this->moduleDataSetup->getConnection()->query($uniqueRecords);
        $this->moduleDataSetup->endSetup();
    }
}