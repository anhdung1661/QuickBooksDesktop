<?php
/**
 * Copyright © 2017 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Setup;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\CreditMemoInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\InvoiceInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\PaymentMethodInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\ReceivePaymentInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\UserInterface;
use Magento\Backend\Block\Widget\Tab;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Magenest\QuickBooksDesktop\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists(CompanyInterface::TABLE_NAME)) {
            $this->addCompanyTable($installer);
        }

        if (!$installer->tableExists(UserInterface::TABLE_NAME)) {
            $this->addUserTable($installer);
        }

        if (!$installer->tableExists(SessionConnectInterface::TABLE_NAME)) {
            $this->addSessionConnectTable($installer);
        }

        if (!$installer->tableExists(TaxesInterface::TABLE_NAME)) {
            $this->addTaxesTable($installer);
        }

        if (!$installer->tableExists(TaxesMappingInterface::TABLE_NAME)) {
            $this->addTaxesMappingTable($installer);
        }

        if (!$installer->tableExists(CustomerInterface::TABLE_NAME)) {
            $this->addCustomerTable($installer);
        }

        if (!$installer->tableExists(CustomerMappingInterface::TABLE_NAME)) {
            $this->addCustomerMappingTable($installer);
        }

        if (!$installer->tableExists(ShippingMethodInterface::TABLE_NAME)) {
            $this->addShippingMethodTable($installer);
        }

        if (!$installer->tableExists(PaymentMethodInterface::TABLE_NAME)) {
            $this->addPaymentMethodTable($installer);
        }

        if (!$installer->tableExists(ItemInterface::TABLE_NAME)) {
            $this->addItemTable($installer);
        }

        if (!$installer->tableExists(ItemMappingInterface::TABLE_NAME)) {
            $this->addItemMappingTable($installer);
        }

        if (!$installer->tableExists(QueueInterface::TABLE_NAME)) {
            $this->addQueueTable($installer);
        }

        if (!$installer->tableExists(SalesOrderInterface::TABLE_NAME)) {
            $this->addSalesOrderTable($installer);
        }

        if (!$installer->tableExists(SalesOrderLineItemInterface::TABLE_NAME)) {
            $this->addSalesOrderLineItemTable($installer);
        }

        if (!$installer->tableExists(InvoiceInterface::TABLE_NAME)) {
            $this->addInvoiceTable($installer);
        }

        if (!$installer->tableExists(ReceivePaymentInterface::TABLE_NAME)) {
            $this->addReceivePaymentTable($installer);
        }

        if (!$installer->tableExists(CreditMemoInterface::TABLE_NAME)) {
            $this->addCreditMemoTable($installer);
        }

        $installer->endSetup();
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addCompanyTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(CompanyInterface::TABLE_NAME))
            ->addColumn(
                CompanyInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Company ID'
            )->addColumn(
                CompanyInterface::COMPANY_NAME_FIELD,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Company name'
            )->addColumn(
                CompanyInterface::COMPANY_STATUS_FIELD,
                Table::TYPE_SMALLINT,
                '4',
                [Table::OPTION_NULLABLE => false],
                'Connect or Disconnect'
            )->addColumn(
                CompanyInterface::NOTE_FIELD,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addIndex(
                $installer->getIdxName(CompanyInterface::TABLE_NAME, [CompanyInterface::COMPANY_NAME_FIELD]),
                [CompanyInterface::COMPANY_NAME_FIELD]
            )->setComment('List companies connected');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addUserTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(UserInterface::TABLE_NAME))
            ->addColumn(
                UserInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                UserInterface::USERNAME_FIELD,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'User name'
            )->addColumn(
                UserInterface::PASSWORD_FIELD,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Password'
            )->addColumn(
                UserInterface::STATUS_FIELD,
                Table::TYPE_SMALLINT,
                '4',
                [Table::OPTION_NULLABLE => false],
                'Active or Inactive'
            )->addColumn(
                UserInterface::NOTE_FIELD,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addIndex(
                $installer->getIdxName(UserInterface::TABLE_NAME, [UserInterface::USERNAME_FIELD]),
                [UserInterface::USERNAME_FIELD]
            )->setComment('Users use to connect to Web connector');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addSessionConnectTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(SessionConnectInterface::TABLE_NAME))
            ->addColumn(
                SessionConnectInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                SessionConnectInterface::USER_NAME_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'User name'
            )->addColumn(
                SessionConnectInterface::SESSION_TOKEN,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Session token'
            )->addColumn(
                SessionConnectInterface::TOTAL,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false],
                'All of requests in this session'
            )->addColumn(
                SessionConnectInterface::PROCESSED,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => true, Table::OPTION_DEFAULT => 0],
                'Number of requests processed'
            )->addColumn(
                SessionConnectInterface::ITERATOR_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Iterator of session'
            )->addColumn(
                SessionConnectInterface::LAST_ERROR_MESSAGE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Message error'
            )->addColumn(
                SessionConnectInterface::CREATE_AT,
                Table::TYPE_DATETIME,
                null,
                [Table::OPTION_NULLABLE => true, Table::OPTION_DEFAULT => Table::TIMESTAMP_UPDATE],
                'Created Time'
            )->setComment('Store all session connect to Web connector');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addTaxesTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(TaxesInterface::TABLE_NAME))
            ->addColumn(
                TaxesInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                TaxesInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                5,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                TaxesInterface::TAX_CODE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Quickbooks tax code'
            )->addColumn(
                TaxesInterface::TAX_VALUE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Quickbooks tax value'
            )->addColumn(
                TaxesInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                TaxesInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                TaxesInterface::TAX_NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addIndex(
                $installer->getIdxName(TaxesInterface::TABLE_NAME, [TaxesInterface::COMPANY_ID, TaxesInterface::TAX_CODE], AdapterInterface::INDEX_TYPE_UNIQUE),
                [TaxesInterface::COMPANY_ID, TaxesInterface::TAX_CODE],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Quickbooks Tax information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addTaxesMappingTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(TaxesMappingInterface::TABLE_NAME))
            ->addColumn(
                TaxesMappingInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                TaxesMappingInterface::MAGENTO_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Magento tax ID'
            )->addColumn(
                TaxesMappingInterface::QUICKBOOKS_ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0],
                'ID of magenest_qbd_taxes table'
            )->addIndex(
                $installer->getIdxName(TaxesMappingInterface::TABLE_NAME, [TaxesMappingInterface::MAGENTO_ID, TaxesMappingInterface::QUICKBOOKS_ENTITY_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [TaxesMappingInterface::MAGENTO_ID, TaxesMappingInterface::QUICKBOOKS_ENTITY_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $installer->getFkName(TaxesMappingInterface::TABLE_NAME, TaxesMappingInterface::QUICKBOOKS_ENTITY_ID, TaxesInterface::TABLE_NAME, TaxesInterface::ENTITY_ID),
                TaxesMappingInterface::QUICKBOOKS_ENTITY_ID,
                $installer->getTable(TaxesInterface::TABLE_NAME),
                TaxesInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Quickbooks Tax information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addCustomerTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(CustomerInterface::TABLE_NAME))
            ->addColumn(
                CustomerInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                CustomerInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                5,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                CustomerInterface::CUSTOMER_NAME,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Customer name'
            )->addColumn(
                CustomerInterface::EMAIL,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Customer email'
            )->addColumn(
                CustomerInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                CustomerInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                CustomerInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addIndex(
                $installer->getIdxName(CustomerInterface::TABLE_NAME, [CustomerInterface::COMPANY_ID, CustomerInterface::CUSTOMER_NAME], AdapterInterface::INDEX_TYPE_UNIQUE),
                [CustomerInterface::COMPANY_ID, CustomerInterface::CUSTOMER_NAME],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Quickbooks Customer information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addCustomerMappingTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(CustomerMappingInterface::TABLE_NAME))
            ->addColumn(
                CustomerMappingInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                CustomerMappingInterface::M2_ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Magento customer id'
            )->addColumn(
                CustomerMappingInterface::QB_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0],
                'ID of magenest_qbd_customer table'
            )->addColumn(
                CustomerMappingInterface::M2_ENTITY_TYPE,
                Table::TYPE_SMALLINT,
                2,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => CustomerMappingInterface::M2_ENTITY_TYPE_CUSTOMER],
                'Guest or Customer'
            )->addIndex(
                $installer->getIdxName(CustomerMappingInterface::TABLE_NAME, [CustomerMappingInterface::M2_ENTITY_ID, CustomerMappingInterface::QB_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [CustomerMappingInterface::M2_ENTITY_ID, CustomerMappingInterface::QB_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $installer->getFkName(CustomerMappingInterface::TABLE_NAME, CustomerMappingInterface::QB_ID, CustomerInterface::TABLE_NAME, CustomerInterface::ENTITY_ID),
                CustomerMappingInterface::QB_ID,
                $installer->getTable(CustomerInterface::TABLE_NAME),
                CustomerInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Mapping customer between Quickbooks and Magento');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addItemTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(ItemInterface::TABLE_NAME))
            ->addColumn(
                ItemInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                ItemInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                ItemInterface::ITEM_NAME,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Item name'
            )->addColumn(
                ItemInterface::ITEM_TYPE,
                Table::TYPE_SMALLINT,
                4,
                [Table::OPTION_NULLABLE => true],
                'Inventory item, non-inventory item ...'
            )->addColumn(
                ItemInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                ItemInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                ItemInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addIndex(
                $installer->getIdxName(ItemInterface::TABLE_NAME, [ItemInterface::COMPANY_ID, ItemInterface::ITEM_NAME], AdapterInterface::INDEX_TYPE_UNIQUE),
                [ItemInterface::COMPANY_ID, ItemInterface::ITEM_NAME],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Quickbooks Item information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addItemMappingTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(ItemMappingInterface::TABLE_NAME))
            ->addColumn(
                ItemMappingInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                ItemMappingInterface::M2_PRODUCT_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Magento product id'
            )->addColumn(
                ItemMappingInterface::QB_ITEM_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0],
                'ID of magenest_qbd_item table'
            )->addIndex(
                $installer->getIdxName(ItemMappingInterface::TABLE_NAME, [ItemMappingInterface::M2_PRODUCT_ID, ItemMappingInterface::QB_ITEM_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [ItemMappingInterface::M2_PRODUCT_ID, ItemMappingInterface::QB_ITEM_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $installer->getFkName(ItemMappingInterface::TABLE_NAME, ItemMappingInterface::QB_ITEM_ID, ItemInterface::TABLE_NAME, ItemInterface::ENTITY_ID),
                ItemMappingInterface::QB_ITEM_ID,
                $installer->getTable(ItemInterface::TABLE_NAME),
                ItemInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Mapping product between Quickbooks and Magento');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addQueueTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(QueueInterface::TABLE_NAME))
            ->addColumn(
                QueueInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                QueueInterface::COMPANY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                QueueInterface::MAGENTO_ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_NULLABLE => false],
                'Magento entity ID'
            )->addColumn(
                QueueInterface::MAGENTO_ENTITY_TYPE,
                Table::TYPE_SMALLINT,
                4,
                [Table::OPTION_NULLABLE => false],
                'Magento entity type'
            )->addColumn(
                QueueInterface::ACTION,
                Table::TYPE_SMALLINT,
                4,
                [Table::OPTION_NULLABLE => false],
                'Add or Mod'
            )->addColumn(
                QueueInterface::STATUS,
                Table::TYPE_SMALLINT,
                4,
                [Table::OPTION_NULLABLE => false],
                'Queue, Success, Fail, Syncing, Blocked'
            )->addColumn(
                QueueInterface::PRIORITY,
                Table::TYPE_SMALLINT,
                4,
                [Table::OPTION_NULLABLE => false],
                'depend on magento entity type'
            )->addColumn(
                QueueInterface::ENQUEUE_TIME,
                Table::TYPE_DATETIME,
                null,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                QueueInterface::DEQUEUE_TIME,
                Table::TYPE_DATETIME,
                null,
                [Table::OPTION_NULLABLE => true, Table::OPTION_DEFAULT => Table::TIMESTAMP_UPDATE],
                'Updated Time'
            )->addColumn(
                QueueInterface::MESSAGE,
                Table::TYPE_TEXT,
                255,
                [Table::OPTION_NULLABLE => true],
                'Message error'
            )->addIndex(
                $installer->getIdxName(QueueInterface::TABLE_NAME, [QueueInterface::COMPANY_ID, QueueInterface::ENTITY_ID, QueueInterface::MAGENTO_ENTITY_TYPE, QueueInterface::ACTION], AdapterInterface::INDEX_TYPE_UNIQUE),
                [QueueInterface::COMPANY_ID, QueueInterface::ENTITY_ID, QueueInterface::MAGENTO_ENTITY_TYPE, QueueInterface::ACTION],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Items communicate between Magento and Web connector');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addSalesOrderTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(SalesOrderInterface::TABLE_NAME))
            ->addColumn(
                SalesOrderInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                SalesOrderInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                SalesOrderInterface::MAGENTO_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Order entity_id on Magento'
            )->addColumn(
                SalesOrderInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                SalesOrderInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                SalesOrderInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addColumn(
                SalesOrderInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => Table::TIMESTAMP_INIT],
                'Created Time'
            )->addIndex(
                $installer->getIdxName(SalesOrderInterface::TABLE_NAME, [SalesOrderInterface::COMPANY_ID, SalesOrderInterface::MAGENTO_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [SalesOrderInterface::COMPANY_ID, SalesOrderInterface::MAGENTO_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Sales Order Information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addSalesOrderLineItemTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(SalesOrderLineItemInterface::TABLE_NAME))
            ->addColumn(
                SalesOrderLineItemInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                SalesOrderLineItemInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                SalesOrderLineItemInterface::ORDER_TXN_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Txn ID of Order'
            )->addColumn(
                SalesOrderLineItemInterface::TXN_LINE_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Txn Line ID of Order'
            )->addColumn(
                SalesOrderLineItemInterface::ITEM_LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID of Item'
            )->addColumn(
                SalesOrderLineItemInterface::ITEM_SKU,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Item SKU'
            )->addIndex(
                $installer->getIdxName(SalesOrderLineItemInterface::TABLE_NAME, [SalesOrderLineItemInterface::COMPANY_ID, SalesOrderLineItemInterface::ORDER_TXN_ID, SalesOrderLineItemInterface::TXN_LINE_ID, SalesOrderLineItemInterface::ITEM_LIST_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [SalesOrderLineItemInterface::COMPANY_ID, SalesOrderLineItemInterface::ORDER_TXN_ID, SalesOrderLineItemInterface::ITEM_SKU],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Line items information of Sales Order');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addInvoiceTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(InvoiceInterface::TABLE_NAME))
            ->addColumn(
                InvoiceInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                InvoiceInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                InvoiceInterface::MAGENTO_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Invoice entity_id on Magento'
            )->addColumn(
                InvoiceInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                InvoiceInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                InvoiceInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addColumn(
                InvoiceInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => Table::TIMESTAMP_INIT],
                'Created Time'
            )->addIndex(
                $installer->getIdxName(InvoiceInterface::TABLE_NAME, [InvoiceInterface::COMPANY_ID, InvoiceInterface::MAGENTO_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [InvoiceInterface::COMPANY_ID, InvoiceInterface::MAGENTO_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Invoice Quickbooks Information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addReceivePaymentTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(ReceivePaymentInterface::TABLE_NAME))
            ->addColumn(
                ReceivePaymentInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                ReceivePaymentInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                ReceivePaymentInterface::MAGENTO_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Invoice entity_id on Magento'
            )->addColumn(
                ReceivePaymentInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                ReceivePaymentInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                ReceivePaymentInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addColumn(
                ReceivePaymentInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => Table::TIMESTAMP_INIT],
                'Created Time'
            )->addIndex(
                $installer->getIdxName(ReceivePaymentInterface::TABLE_NAME, [ReceivePaymentInterface::COMPANY_ID, ReceivePaymentInterface::MAGENTO_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [ReceivePaymentInterface::COMPANY_ID, ReceivePaymentInterface::MAGENTO_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Receive Payment Quickbooks Information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addCreditMemoTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(CreditMemoInterface::TABLE_NAME))
            ->addColumn(
                CreditMemoInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                CreditMemoInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                11,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                CreditMemoInterface::MAGENTO_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Invoice entity_id on Magento'
            )->addColumn(
                CreditMemoInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                CreditMemoInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                CreditMemoInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addColumn(
                CreditMemoInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => Table::TIMESTAMP_INIT],
                'Created Time'
            )->addIndex(
                $installer->getIdxName(CreditMemoInterface::TABLE_NAME, [CreditMemoInterface::COMPANY_ID, CreditMemoInterface::MAGENTO_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [CreditMemoInterface::COMPANY_ID, CreditMemoInterface::MAGENTO_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Credit Memo Quickbooks Information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addShippingMethodTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(ShippingMethodInterface::TABLE_NAME))
            ->addColumn(
                ShippingMethodInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                ShippingMethodInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                5,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                ShippingMethodInterface::SHIPPING_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Shipping method code'
            )->addColumn(
                ShippingMethodInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                ShippingMethodInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                ShippingMethodInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addIndex(
                $installer->getIdxName(ShippingMethodInterface::TABLE_NAME, [ShippingMethodInterface::COMPANY_ID, ShippingMethodInterface::SHIPPING_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [ShippingMethodInterface::COMPANY_ID, ShippingMethodInterface::SHIPPING_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Quickbooks Shipping method information');
        $connection->createTable($table);
    }

    /**
     * Install table schema that use in QuickBooks Desktop version 3.0.0
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addPaymentMethodTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection
            ->newTable($installer->getTable(PaymentMethodInterface::TABLE_NAME))
            ->addColumn(
                PaymentMethodInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                11,
                [Table::OPTION_IDENTITY => true, Table::OPTION_UNSIGNED => true, Table::OPTION_NULLABLE => false, Table::OPTION_PRIMARY => true],
                'Entity ID'
            )->addColumn(
                PaymentMethodInterface::COMPANY_ID,
                Table::TYPE_SMALLINT,
                5,
                [Table::OPTION_NULLABLE => false, Table::OPTION_DEFAULT => 0, Table::OPTION_UNSIGNED => true],
                'Company ID'
            )->addColumn(
                PaymentMethodInterface::PAYMENT_METHOD,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Shipping method code'
            )->addColumn(
                PaymentMethodInterface::LIST_ID,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'List ID'
            )->addColumn(
                PaymentMethodInterface::EDIT_SEQUENCE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => false],
                'Edit sequence'
            )->addColumn(
                PaymentMethodInterface::NOTE,
                Table::TYPE_TEXT,
                '255',
                [Table::OPTION_NULLABLE => true],
                'Note'
            )->addIndex(
                $installer->getIdxName(PaymentMethodInterface::TABLE_NAME, [PaymentMethodInterface::COMPANY_ID, PaymentMethodInterface::PAYMENT_METHOD], AdapterInterface::INDEX_TYPE_UNIQUE),
                [PaymentMethodInterface::COMPANY_ID, PaymentMethodInterface::PAYMENT_METHOD],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Quickbooks Payment method information');
        $connection->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @deprecated From version 3.0.0
     *
     */
    private function installSchemaOldVersion(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        // install table ticket
        $installer->startSetup();
        if (!$installer->tableExists('magenest_qbd_ticket')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_qbd_ticket')
            )->addColumn(
                'ticket_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Ticket ID'
            )->addColumn(
                'ticket',
                Table::TYPE_TEXT,
                28,
                ['nullable' => true],
                'Ticket'
            )->addColumn(
                'username',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'User Name'
            )->addColumn(
                'created_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Creatd At'
            )->addColumn(
                'processed',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true],
                'Processed'
            )->addColumn(
                'current',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true],
                'Current'
            )->addColumn(
                'ipaddr',
                Table::TYPE_TEXT,
                30,
                ['nullable' => true],
                'Ip Addr'
            )->addColumn(
                'lasterror_msg',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Laste Error Msg'
            )->setComment(
                'Ticket Table'
            );
            $installer->getConnection()->createTable($table);
        }

        // install table user
        if (!$installer->tableExists('magenest_qbd_user')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_qbd_user')
            )->addColumn(
                'user_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'User ID'
            )->addColumn(
                'username',
                Table::TYPE_TEXT,
                50,
                [
                    'nullable' => false,
                ],
                'Username'
            )->addColumn(
                'password',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Password'
            )->addColumn(
                'status',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Status'
            )->addColumn(
                'expired_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Expired Date'
            )->addColumn(
                'remote_ip',
                Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'Remote Ip '
            );
            $installer->getConnection()->createTable($table);
        }

        // install table queue
        if (!$installer->tableExists('magenest_qbd_queue')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_qbd_queue')
            )->addColumn(
                'queue_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Queue ID'
            )->addColumn(
                'ticket_id',
                Table::TYPE_TEXT,
                11,
                ['nullable' => true],
                'Ticket Id'
            )->addColumn(
                'action_name',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Action Name'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Type'
            )->addColumn(
                'company_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Company ID'
            )->addColumn(
                'enqueue_datetime',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Enqueue Datetime'
            )->addColumn(
                'dequeue_datetime',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Dequeue Datetime'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                3,
                ['nullable' => true],
                'Status'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true],
                'Entity Id'
            )->addColumn(
                'operation',
                Table::TYPE_SMALLINT,
                2,
                ['nullable' => true],
                'Operation'
            )->addColumn(
                'qbd_delete_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true],
                'Qbd Delete Id'
            )->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true],
                'Priority'
            )->addColumn(
                'msg',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Msg'
            )->addColumn(
                'payment',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Payment Method'
            )->addColumn(
                'vendor_name',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Vendor Name'
            );

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('magenest_qbd_company')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_qbd_company')
            )->addColumn(
                'company_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Company ID'
            )->addColumn(
                'company_name',
                Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'Company Name'
            )->addColumn(
                'status',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Status'
            )->addColumn(
                'note',
                Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'Note '
            );
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('magenest_qbd_mapping')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_qbd_mapping')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'ID'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Entity ID'
            )->addColumn(
                'type',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Type'
            )->addColumn(
                'company_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Company ID'
            )->addColumn(
                'list_id',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'List ID'
            )->addColumn(
                'edit_sequence',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Edit Sequence '
            )->addColumn(
                'payment',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Payment Method'
            );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
