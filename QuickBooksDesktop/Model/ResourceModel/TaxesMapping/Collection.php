<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 27/03/2020 17:05
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractMappingCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping
 */
class Collection extends AbstractMappingCollection
{
    const TABLE_JOIN_NAME = 'taxesQb';
    const MAGENTO_TAX_TABLE_NAME = 'magento_core_tax';

    /**
     * @var string
     */
    protected $_idFieldName = TaxesMappingInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\TaxesMapping', 'Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getMainTable())->joinLeft(
            [self::TABLE_JOIN_NAME => $this->getTable(TaxesInterface::TABLE_NAME)],
            sprintf(
                '%s.%s = %s.%s',
                $this->getMainTable(),
                TaxesMappingInterface::QUICKBOOKS_ENTITY_ID,
                self::TABLE_JOIN_NAME,
                TaxesInterface::ENTITY_ID
            )
        )->joinLeft(
            [self::MAGENTO_TAX_TABLE_NAME => $this->getTable('tax_calculation_rate')],
            sprintf(
                '%s.%s = %s.%s',
                $this->getMainTable(),
                TaxesMappingInterface::MAGENTO_ID,
                self::MAGENTO_TAX_TABLE_NAME,
                'tax_calculation_rate_id'
            ),
            ['magento_code' => 'code']
        )->where(self::TABLE_JOIN_NAME . '.' .TaxesInterface::COMPANY_ID . ' = (?) ', $this->buildQueryActiveCompany());

        return $this;
    }

    /**
     * @param $field
     * @param null $condition
     * @return Collection
     */
    public function filterByTaxInfo($field, $condition = null)
    {
        $taxesColumn = [TaxesInterface::TAX_CODE, TaxesInterface::TAX_VALUE, TaxesInterface::LIST_ID, TaxesInterface::EDIT_SEQUENCE, TaxesInterface::TAX_NOTE, TaxesInterface::ENTITY_ID];

        $magentoColumn = ['code', 'magento_code'];

        if (!is_array($field)) {
            $condition = [$field => $condition];
            $field = [$field => $field];
        }

        foreach ($field as $key => $value) {
            if (in_array($value, $taxesColumn)) {
                $field[$key] = self::TABLE_JOIN_NAME . '.' . $value;
            }

            if (in_array($value, $magentoColumn)) {
                $field[$key] = self::MAGENTO_TAX_TABLE_NAME . '.code';
            }
        }

        return $this->addFieldToFilter($field, $condition);
    }

}