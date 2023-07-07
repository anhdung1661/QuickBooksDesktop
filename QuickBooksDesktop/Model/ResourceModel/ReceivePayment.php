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
 * @time: 30/09/2020 10:28
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\ReceivePaymentInterface;

/**
 * Class ReceivePayment
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class ReceivePayment extends AbstractQuickbooksEntity
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ReceivePaymentInterface::TABLE_NAME, ReceivePaymentInterface::ENTITY_ID);
    }

    /**
     * @param $receivePaymentsData
     * @return $this
     */
    public function saveReceivePayments($receivePaymentsData)
    {
        $this->saveQuickbooksEntity(ReceivePaymentInterface::TABLE_NAME, $receivePaymentsData);

        return $this;
    }

    /**
     * @param $invoiceIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPaymentIdsSynced($invoiceIds)
    {
        $sql = $this->getConnection()
            ->select()->from([$this->getMainTable()], 'magento_invoice_id')
            ->where('magento_invoice_id IN (?)', $invoiceIds);
        return $this->getConnection()->fetchCol($sql);
    }
}