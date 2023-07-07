<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 15/04/2020 15:34
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ItemMapping
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class ItemMapping extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ItemMappingInterface::TABLE_NAME, ItemMappingInterface::ENTITY_ID);
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