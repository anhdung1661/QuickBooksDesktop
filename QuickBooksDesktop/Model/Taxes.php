<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\QuickbooksEntityInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax\ItemSalesTaxQueryRes;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Taxes
 * @package Magenest\QuickBooksDesktop\Model
 * @method getTaxCode()
 * @method getTaxValue()
 * @method getListId()
 * @method getEditSequence()
 */
class Taxes extends AbstractQuickbooksEntity
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_qbd_taxes';

    /**
     * List object are responed by Quickbooks
     * @var array
     */
    private $taxData = [];

    /**
     * Initize
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\Taxes');
    }


    /**
     * @param array $data
     * @return AbstractQuickbooksEntity
     */
    public function setTaxData($data = [])
    {
        $this->taxData = $data;

        return $this;
    }

    /**
     * Save tax info
     *
     * @return $this
     * @throws \Exception
     *
     */
    public function save()
    {
        if (empty($this->taxData)) {
            return parent::save();
        }
        $this->getResource()->saveTaxes(ProcessArray::getColValueFromThreeDimensional($this->taxData, [
            TaxesInterface::LIST_ID => ItemSalesTaxQueryRes::LIST_ID,
            TaxesInterface::EDIT_SEQUENCE => ItemSalesTaxQueryRes::EDIT_SEQUENCE,
            TaxesInterface::TAX_CODE => ItemSalesTaxQueryRes::TAX_NAME,
            TaxesInterface::TAX_VALUE => ItemSalesTaxQueryRes::TAX_RATE,
            TaxesInterface::TAX_NOTE => ItemSalesTaxQueryRes::TAX_DES
        ], [
            TaxesInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]));

        return $this;
    }
}
