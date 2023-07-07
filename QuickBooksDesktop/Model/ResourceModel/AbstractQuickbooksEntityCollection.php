<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 18/03/2020 13:57
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\QuickbooksEntityInterface;

/**
 * Class AbstractQuickbooksEntityCollection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
abstract class AbstractQuickbooksEntityCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getMainTable())
            ->where( $this->getCompanyIdFieldName() . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }

    /**
     * @return string
     */
    protected function getCompanyIdFieldName()
    {
        return QuickbooksEntityInterface::COMPANY_ID;
    }

    /**
     * @inheritDoc
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns($this->getResource()->getIdFieldName(), $this->getMainTable());
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}