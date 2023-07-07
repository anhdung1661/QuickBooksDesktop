<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 27/03/2020 17:02
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class TaxesMapping
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class TaxesMapping extends AbstractDb
{
    /**
     * Init
     */
    protected function _construct()
    {
        $this->_init(TaxesMappingInterface::TABLE_NAME, TaxesMappingInterface::ENTITY_ID);
    }

    /**
     * @param array $rowsData
     * @return int
     * @throws LocalizedException
     */
    public function saveMultiRows(array $rowsData)
    {
        return $this->getConnection()->insertArray(
            $this->getMainTable(),
            array_keys(reset($rowsData)),
            $rowsData,
            AdapterInterface::REPLACE
        );
    }
}