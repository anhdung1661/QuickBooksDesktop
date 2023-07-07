<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 09:18
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping;

use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractMappingCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping
 */
class Collection extends AbstractMappingCollection
{
    const TABLE_JOIN_NAME = 'customerQb';

    /**
     * @inheritDoc
     */
    protected $_idFieldName = CustomerMappingInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\CustomerMapping', 'Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getMainTable())->joinLeft(
            [self::TABLE_JOIN_NAME => $this->getTable(CustomerInterface::TABLE_NAME)],
            sprintf(
                '%s.%s = %s.%s',
                $this->getMainTable(),
                CustomerMappingInterface::QB_ID,
                self::TABLE_JOIN_NAME,
                CustomerInterface::ENTITY_ID
            )
        )->where(self::TABLE_JOIN_NAME . '.' . CustomerInterface::COMPANY_ID . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }
}