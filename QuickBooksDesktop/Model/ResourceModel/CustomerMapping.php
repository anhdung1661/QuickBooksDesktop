<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 09:17
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CustomerMapping
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class CustomerMapping extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(CustomerMappingInterface::TABLE_NAME, CustomerMappingInterface::ENTITY_ID);
    }

    /**
     * @param array $rowsData
     * @return int
     * @throws LocalizedException
     */
    public function saveMultiRows(array $rowsData)
    {
        return $this->getConnection()->insertOnDuplicate(
            $this->getMainTable(),
            $rowsData,
            [CustomerMappingInterface::M2_ENTITY_ID, CustomerMappingInterface::M2_ENTITY_TYPE]
        );
    }
}