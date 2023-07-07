<?php
/**
 * Copyright © 2013-2018 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\QuickBooksDesktop\Setup;

use Magento\Framework\Setup\SetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**@#+
     * @deprecated From version 3.0.0
     *
     * @constant
     */
    const TABLE_PREFIX = 'magenest_qbd_';

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    }

    /**
     * Add tax code table. From version 2.0.3
     * @deprecated From version 3.0.0
     *
     * @param $installer
     */
    private function createTaxCodeTable($installer)
    {
        $tableName = self::TABLE_PREFIX . 'tax_code_mapping';
        if ($installer->tableExists($tableName)) {
            return;
        }
        $table = $installer->getConnection()->newTable(
            $installer->getTable($tableName)
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Mapping Id'
        )->addColumn(
            'tax_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Tax ID'
        )->addColumn(
            'tax_title',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Tax Title'
        )->addColumn(
            'code',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Code'
        );
        $installer->getConnection()->createTable($table);
    }


    /**
     * Create the table magenest_qbonline_customer_queue
     * @deprecated From version 3.0.0
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createQBTax($installer)
    {
        $tableName = self::TABLE_PREFIX . 'tax_code';
        if ($installer->tableExists($tableName)) {
            return;
        }
        $table = $installer->getConnection()->newTable(
            $installer->getTable($tableName)
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Mapping Id'
        )->addColumn(
            'tax_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Tax Code'
        )->addColumn(
            'company_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Company ID'
        )->addColumn(
            'list_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'List ID'
        )->addColumn(
            'edit_sequence',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Edit Sequence'
        );
        $installer->getConnection()->createTable($table);
    }

    /**
     * Create the table magenest_qbonline_customer_queue
     * @deprecated From version 3.0.0
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createCreateCustomQueue($installer)
    {
        $tableName = self::TABLE_PREFIX . 'custom_queue';
        if ($installer->tableExists($tableName)) {
            return;
        }
        $table = $installer->getConnection()->newTable(
            $installer->getTable($tableName)
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Mapping Id'
        )->addColumn(
            'ticket_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Ticket ID'
        )->addColumn(
            'type',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Type'
        )->addColumn(
            'company_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Company ID'
        )->addColumn(
            'status',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Status'
        )->addColumn(
            'iterator_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Iterator ID'
        )->addColumn(
            'operation',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Operation'
        );
        $installer->getConnection()->createTable($table);
    }

     /**
      * @deprecated From 3.0.0
      *
      * @param SetupInterface $installer
      * @return void
      */
    private function removeExpire($installer)
    {
        $installer->getConnection()->dropColumn(
            $installer->getTable('magenest_qbd_user'),
            'expired_date'
        );
    }

    /**
     * Create the table magenest_qbd_item_sales_order
     * @deprecated from version 3.0.0
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createItemSalesOrderTable($installer)
    {
        $tableName = self::TABLE_PREFIX . 'item_sales_order';
        if ($installer->tableExists($tableName)) {
            return;
        }
        $table = $installer->getConnection()->newTable(
            $installer->getTable($tableName)
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Mapping Id'
        )->addColumn(
            'list_id_order',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'List ID Order'
        )->addColumn(
            'company_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Company Id'
        )->addColumn(
            'txn_line_id',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'TxnLineID'
        )->addColumn(
            'list_id_item',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'List ID Item'
        )->addColumn(
            'sku',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Sku'
        );
        $installer->getConnection()->createTable($table);
    }
}
