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
 * @time: 29/09/2020 16:15
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\InvoiceInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;

/**
 * Class Invoice
 * @package Magenest\QuickBooksDesktop\Model
 */
class Invoice extends AbstractQuickbooksEntity
{
    /**
     * @inheritDoc
     */
    protected $_eventPrefix = InvoiceInterface::TABLE_NAME;

    /**
     * @var array
     */
    protected $_invoicesData = [];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice');
    }

    /**
     * @param $invoicesData
     * @return $this
     */
    public function setInvoicesData($invoicesData)
    {
        $this->_invoicesData = ProcessArray::insertColumnToThreeDimensional($invoicesData, [
            InvoiceInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if (empty($this->_invoicesData)) {
            return parent::save();
        }

        $this->getResource()->saveInvoices($this->_invoicesData);
        return $this;
    }
}