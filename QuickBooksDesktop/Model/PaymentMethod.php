<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 15:54
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\PaymentMethodInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;

/**
 * Class PaymentMethod
 * @package Magenest\QuickBooksDesktop\Model
 */
class PaymentMethod extends AbstractQuickbooksEntity
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = PaymentMethodInterface::TABLE_NAME;

    protected $_paymentMethodsData;

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\PaymentMethod');
    }

    /**
     * @param $paymentMethodsData
     * @return $this
     */
    public function setPaymentMethodsData($paymentMethodsData)
    {
        $this->_paymentMethodsData = ProcessArray::insertColumnToThreeDimensional($paymentMethodsData, [
            PaymentMethodInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    public function updateQuickbooksInformation()
    {
        if (!empty($this->_paymentMethodsData)) {
            $this->getResource()->updateQuickbooksInformation($this->_paymentMethodsData);
        }
        return $this;
    }

    /**
     * @return $this|AbstractQuickbooksEntity
     */
    public function save()
    {
        if (!empty($this->_paymentMethodsData)) {
            $this->getResource()->savePaymentMethods($this->_paymentMethodsData);
        }

        return $this;
    }
}