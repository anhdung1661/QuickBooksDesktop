<?php

namespace Magenest\QuickBooksDesktop\Setup\Patch\Schema;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * Class UpdateForeignKeySalesOrderLineItems
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Schema
 */
class UpdateForeignKeySalesOrderLineItems implements SchemaPatchInterface
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
        $childTable = $this->moduleDataSetup->getTable('magenest_qbd__sales_order_line_items');
        $primaryTable = $this->moduleDataSetup->getTable('magenest_qbd__sales_order');
        // add foreign key for table magenest_qbd__sales_order_line_items
        $sql = "ALTER TABLE {$childTable}
	            ADD CONSTRAINT FK_MAGENEST_QBD_SALES_ORDER_LINE_ITEMS_2901
		        FOREIGN KEY (order_txn_id) REFERENCES {$primaryTable} (list_id)
			    ON UPDATE CASCADE ON DELETE CASCADE";
        $this->moduleDataSetup->getConnection()->query($sql);

        $this->moduleDataSetup->endSetup();
    }
}
