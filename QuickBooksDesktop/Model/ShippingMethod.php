<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 09:59
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;

/**
 * Class ShippingMethod
 * @package Magenest\QuickBooksDesktop\Model
 */
class ShippingMethod extends AbstractQuickbooksEntity
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = ShippingMethodInterface::TABLE_NAME;

    /**
     * @var array
     */
    protected $_shippingMethodsData;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\ShippingMethod');
    }

    /**
     * @param $shippingMethodsData
     * @return $this
     */
    public function setShippingMethods($shippingMethodsData)
    {
        $this->_shippingMethodsData = ProcessArray::insertColumnToThreeDimensional($shippingMethodsData, [
            ShippingMethodInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);

        return $this;
    }

    /**
     * @param $shippingMethodsData
     * @return $this
     */
    public function setQuickbooksInformation($shippingMethodsData)
    {
        $this->_shippingMethodsData = ProcessArray::insertColumnToThreeDimensional($shippingMethodsData, [
            ShippingMethodInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateQuickbooksInformation()
    {
        if (!empty($this->_shippingMethodsData)) {
            $this->getResource()->updateQuickbooksInformation($this->_shippingMethodsData);
        }

        return $this;
    }

    /**
     * Save object data
     *
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        if ($this->_shippingMethodsData != null) {
            $this->getResource()->saveShippingMethods($this->_shippingMethodsData);
        }

        return $this;
    }
}