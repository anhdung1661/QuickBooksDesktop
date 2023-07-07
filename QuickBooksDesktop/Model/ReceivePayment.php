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
 * @time: 30/09/2020 10:26
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\ReceivePaymentInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;

/**
 * Class ReceivePayment
 * @package Magenest\QuickBooksDesktop\Model
 */
class ReceivePayment extends AbstractQuickbooksEntity
{
    protected $_eventPrefix = ReceivePaymentInterface::TABLE_NAME;

    private $receivePaymentsData = [];

    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\ReceivePayment');
    }

    /**
     * @param $receivePaymentsData
     * @return $this
     */
    public function setReceivePaymentsData($receivePaymentsData)
    {
        $this->receivePaymentsData = ProcessArray::insertColumnToThreeDimensional($receivePaymentsData, [
            ReceivePaymentInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);

        return $this;
    }

    /**
     * @return $this|ReceivePayment
     * @throws \Exception
     */
    public function save()
    {
        if (empty($this->receivePaymentsData)) {
            return parent::save();
        }

        $this->getResource()->saveReceivePayments($this->receivePaymentsData);

        return $this;
    }
}