<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 27/03/2020 16:56
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class TaxesMapping
 * @package Magenest\QuickBooksDesktop\Model
 */
class TaxesMapping extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = TaxesMappingInterface::TABLE_NAME;

    private $multiRowsData;

    public function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping');
    }

    /**
     * @param array $listRows
     * @return $this
     */
    public function addMultiRowsData(array $listRows)
    {
        foreach ($listRows as $rowData) {
            if (!empty($rowData[TaxesMappingInterface::QUICKBOOKS_ENTITY_ID])) {
                $this->multiRowsData[] = [
                    TaxesMappingInterface::MAGENTO_ID => $rowData[TaxesMappingInterface::MAGENTO_ID],
                    TaxesMappingInterface::QUICKBOOKS_ENTITY_ID => $rowData[TaxesMappingInterface::QUICKBOOKS_ENTITY_ID]
                ];
            }
        }

        return $this;
    }

    /**
     * @return $this|AbstractModel
     * @throws \Exception
     */
    public function save()
    {
        if (empty($this->multiRowsData)) {
            parent::save();
        } else {
            $this->getResource()->saveMultiRows($this->multiRowsData);
        }
        return $this;
    }
}