<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 13/10/2020 16:12
 */

namespace Magenest\QuickBooksDesktop\Setup\Patch\Data;

use Magenest\QuickBooksDesktop\Api\Data\PaymentMethodInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Setup\Patch\IntegrateData;

/**
 * Class IntegrateQueueTable
 * @package Magenest\QuickBooksDesktop\Setup\Patch\Data
 */
class IntegrateQueueTable extends IntegrateData
{
    public static function getDependencies()
    {
        return [
            IntegrateShippingMethod::class,
            IntegratePaymentMethod::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        if ($connection->isTableExists($this->moduleDataSetup->getTable('magenest_qbd_queue'))) {
            $columns = [
                QueueInterface::COMPANY_ID => 'old_table.company_id',
                QueueInterface::MAGENTO_ENTITY_ID => new \Zend_Db_Expr('CASE old_table.type WHEN "PaymentMethod" THEN payment_method.' . PaymentMethodInterface::ENTITY_ID .
                    ' WHEN "ShipMethod" THEN ship_method.' . ShippingMethodInterface::ENTITY_ID .
                    ' WHEN "Customer" THEN old_table.entity_id' .
                    ' WHEN "Guest" THEN old_table.entity_id' .
                    ' WHEN "Product" THEN old_table.entity_id' .
                    ' WHEN "SalesOrder" THEN old_table.entity_id' .
                    ' WHEN "Invoice" THEN old_table.entity_id' .
                    ' WHEN "ReceivePayment" THEN old_table.entity_id' .
                    ' WHEN "CreditMemo" THEN old_table.entity_id' .
                    ' WHEN "ItemOtherCharge" THEN NULL' .
                    ' WHEN "ItemDiscount" THEN NULL' .
                    ' END'),
                QueueInterface::MAGENTO_ENTITY_TYPE => new \Zend_Db_Expr('CASE old_table.type WHEN "PaymentMethod" THEN ' . QueueInterface::TYPE_PAYMENT_METHOD .
                    ' WHEN "ShipMethod" THEN ' . QueueInterface::TYPE_SHIPPING_METHOD .
                    ' WHEN "Customer" THEN ' . QueueInterface::TYPE_CUSTOMER .
                    ' WHEN "Guest" THEN ' . QueueInterface::TYPE_GUEST .
                    ' WHEN "Product" THEN ' . QueueInterface::TYPE_PRODUCT .
                    ' WHEN "SalesOrder" THEN ' . QueueInterface::TYPE_SALES_ORDER .
                    ' WHEN "Invoice" THEN ' . QueueInterface::TYPE_INVOICE .
                    ' WHEN "ReceivePayment" THEN ' . QueueInterface::TYPE_RECEIVE_PAYMENT .
                    ' WHEN "CreditMemo" THEN ' . QueueInterface::TYPE_CREDIT_MEMO .
                    ' WHEN "ItemOtherCharge" THEN ' . QueueInterface::TYPE_ITEM_SHIPPING .
                    ' WHEN "ItemDiscount" THEN ' . QueueInterface::TYPE_ITEM_DISCOUNT .
                    ' END'),
                QueueInterface::ACTION => 'old_table.operation',
                QueueInterface::STATUS => 'old_table.status',
                QueueInterface::PRIORITY => new \Zend_Db_Expr('CASE old_table.type WHEN "PaymentMethod" THEN ' . QueueInterface::PRIORITY_PAYMENT_METHOD .
                    ' WHEN "ShipMethod" THEN ' . QueueInterface::PRIORITY_SHIPPING_METHOD .
                    ' WHEN "Customer" THEN ' . QueueInterface::PRIORITY_CUSTOMER .
                    ' WHEN "Guest" THEN ' . QueueInterface::PRIORITY_GUEST .
                    ' WHEN "Product" THEN ' . QueueInterface::PRIORITY_PRODUCT .
                    ' WHEN "SalesOrder" THEN ' . QueueInterface::PRIORITY_SALES_ORDER .
                    ' WHEN "Invoice" THEN ' . QueueInterface::PRIORITY_INVOICE .
                    ' WHEN "ReceivePayment" THEN ' . QueueInterface::PRIORITY_RECEIVE_PAYMENT .
                    ' WHEN "CreditMemo" THEN ' . QueueInterface::PRIORITY_CREDIT_MEMO .
                    ' WHEN "ItemOtherCharge" THEN ' . QueueInterface::PRIORITY_ITEM_OTHER_CHARGE .
                    ' WHEN "ItemDiscount" THEN ' . QueueInterface::PRIORITY_ITEM_DISCOUNT .
                    ' END'),
                QueueInterface::ENQUEUE_TIME => 'old_table.enqueue_datetime',
                QueueInterface::DEQUEUE_TIME => 'old_table.dequeue_datetime',
                QueueInterface::MESSAGE => 'old_table.msg'
            ];

            $select = $connection->select();
            $select->from(['old_table' => $this->moduleDataSetup->getTable('magenest_qbd_queue')], $columns)
                ->joinLeft(['ship_method' => $this->moduleDataSetup->getTable(ShippingMethodInterface::TABLE_NAME)],
                    'old_table.payment = ship_method.' . ShippingMethodInterface::SHIPPING_ID . ' AND old_table.company_id = ship_method.' . ShippingMethodInterface::COMPANY_ID,
                    []
                )->joinLeft(['payment_method' => $this->moduleDataSetup->getTable(PaymentMethodInterface::TABLE_NAME)],
                    'old_table.payment = payment_method.' . PaymentMethodInterface::PAYMENT_METHOD . ' AND old_table.company_id = payment_method.' . PaymentMethodInterface::COMPANY_ID,
                    []
                );
            $select->useStraightJoin();
            $insertQuery = $select->insertFromSelect($this->moduleDataSetup->getTable(QueueInterface::TABLE_NAME), array_keys($columns), true);
            $connection->query($insertQuery);
        }

        $connection->endSetup();
    }
}
