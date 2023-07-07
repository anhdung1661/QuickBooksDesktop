<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 30/09/2020 15:11
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\CreditMemoInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;

/**
 * Class CreditMemo
 * @package Magenest\QuickBooksDesktop\Model
 */
class CreditMemo extends AbstractQuickbooksEntity
{
    /**
     * @inheritDoc
     */
    protected $_eventPrefix = CreditMemoInterface::TABLE_NAME;

    /**
     * @var array
     */
    protected $_creditMemosData = [];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\CreditMemo');
    }

    /**
     * @param $creditMemosData
     * @return $this
     */
    public function setCreditMemosData($creditMemosData)
    {
        $this->_creditMemosData = ProcessArray::insertColumnToThreeDimensional($creditMemosData, [
            CreditMemoInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if (empty($this->_creditMemosData)) {
            return parent::save();
        }

        $this->getResource()->saveCreditMemos($this->_creditMemosData);
        return $this;
    }
}