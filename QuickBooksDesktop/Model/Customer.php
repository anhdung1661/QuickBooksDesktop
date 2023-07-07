<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 08:46
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;

/**
 * Class Customer
 * @package Magenest\QuickBooksDesktop\Model
 */
class Customer extends AbstractQuickbooksEntity
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = CustomerInterface::TABLE_NAME;

    /**
     * @var array
     */
    protected $_customerData;

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\Customer');
    }

    /**
     * @param $customerData
     * @return $this
     */
    public function setCustomerData($customerData)
    {
        $this->_customerData = ProcessArray::insertColumnToThreeDimensional($customerData, [
            CustomerInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);

        return $this;
    }

    /**
     * @return $this|AbstractQuickbooksEntity
     */
    public function save()
    {
        if (!empty($this->_customerData)) {
            $this->getResource()->saveCustomers($this->_customerData);
        }

        return $this;
    }
}