<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 15/04/2020 14:45
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Item save Quickbooks item informantion
 * @package Magenest\QuickBooksDesktop\Model
 */
class Item extends AbstractQuickbooksEntity
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = ItemInterface::TABLE_NAME;

    /**
     * @var array
     */
    private $itemsData = [];

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\Item');
    }

    /**
     * @param $itemsData
     * @return $this
     */
    public function setItemsData($itemsData)
    {
        if (!empty($itemsData)) {
            $this->itemsData = ProcessArray::insertColumnToThreeDimensional($itemsData, [
                ItemInterface::COMPANY_ID => $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID)
            ]);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function saveItems()
    {
        if (empty($this->itemsData)) {
            return parent::save();
        }
        $this->getResource()->saveItems($this->itemsData);

        return $this;
    }
}