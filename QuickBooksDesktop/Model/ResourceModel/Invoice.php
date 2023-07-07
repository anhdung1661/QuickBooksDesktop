<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 29/09/2020 16:21
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\InvoiceInterface;

/**
 * Class Invoice
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class Invoice extends AbstractQuickbooksEntity
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(InvoiceInterface::TABLE_NAME, InvoiceInterface::ENTITY_ID);
    }

    /**
     * @param $invoicesData
     * @return $this
     */
    public function saveInvoices($invoicesData)
    {
        $this->saveQuickbooksEntity(InvoiceInterface::TABLE_NAME, $invoicesData);

        return $this;
    }

    /**
     * @param $creditIds
     * @return array
     */
    public function getInvoiceIdsByCredit($creditIds)
    {
        $select = $this->getConnection()->select()
            ->from(['main' => $this->getTable('sales_invoice')], 'main.entity_id')
            ->joinLeft(
                ['credit' => $this->getTable('sales_creditmemo')],
                "main.order_id = credit.order_id",
                []
            )->where('credit.entity_id IN (?)', $creditIds);
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param $invoiceIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInvoiceIdSynced($invoiceIds)
    {
        return $this->getConnection()->fetchCol(
            $this->getConnection()->select()
                ->from([$this->getMainTable()], 'magento_invoice_id')
                ->where('magento_invoice_id IN (?)', $invoiceIds)
        );
    }
}
