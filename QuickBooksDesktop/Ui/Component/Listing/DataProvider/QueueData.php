<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 02/10/2020 16:25
 */

namespace Magenest\QuickBooksDesktop\Ui\Component\Listing\DataProvider;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;

/**
 * Class QueueData
 * @package Magenest\QuickBooksDesktop\Ui\Component\Listing\DataProvider
 */
class QueueData extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const DEFAULT_FILTER_COMPANY = 'default_company';

    /**
     * @var \Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\Grid\CollectionFactory
     */
    protected $_gridCollection;

    /**
     * QueueData constructor.
     * @param \Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\Grid\CollectionFactory $gridCollection
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\Grid\CollectionFactory $gridCollection,
        $name, $primaryFieldName, $requestFieldName, array $meta = [], array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->_gridCollection = $gridCollection;
    }

    /**
     * @inheritdoc
     */
    public function getCollection()
    {
        if ($this->collection == null) {
            $this->collection = $this->_gridCollection->create();
        }
        return $this->collection;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'increment_id') {
            $filter->setField(new \Zend_Db_Expr('CONCAT_WS("", order_table.increment_id, invoice_table.increment_id, creditmemo_table.increment_id)'));
        } else if (!in_array($filter->getField(), [self::DEFAULT_FILTER_COMPANY, 'company_id'])) {
            $filter->setField(new \Zend_Db_Expr('source_table.' . $filter->getField()));
        }
        if (!in_array($filter->getField(), [self::DEFAULT_FILTER_COMPANY, 'company_id'])) {
            parent::addFilter($filter);
        }

        // filter by company. set default filter by company thich is in active if there is no filter or company filter is not applied
        if ($filter->getField() == 'company_id') {
            $filter->setField(new \Zend_Db_Expr('source_table.company_id'));
        } else if ($filter->getField() == self::DEFAULT_FILTER_COMPANY || $filter->getField() != 'company_id') {
            $filter->setField(new \Zend_Db_Expr('source_table.company_id'));
            $filter->setValue(new \Zend_Db_Expr('(SELECT ' . CompanyInterface::ENTITY_ID . ' FROM ' . $this->getCollection()->getTable(CompanyInterface::TABLE_NAME) . ' WHERE ' . CompanyInterface::COMPANY_STATUS_FIELD . ' = ' . CompanyInterface::COMPANY_CONNECTED . ')'));
        }
        parent::addFilter($filter);
    }
}