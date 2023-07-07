<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model\ResourceModel\Queue;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksOnline\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = QueueInterface::ENTITY_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\Queue', 'Magenest\QuickBooksDesktop\Model\ResourceModel\Queue');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])->where(QueueInterface::COMPANY_ID . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }

    /**
     * @return $this
     */
    public function countRecordsEachType()
    {
        $this->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([QueueInterface::MAGENTO_ENTITY_TYPE, new \Zend_Db_Expr('COUNT(' . QueueInterface::ENTITY_ID . ') as count')])
            ->group(QueueInterface::MAGENTO_ENTITY_TYPE);
        return $this;
    }
}
