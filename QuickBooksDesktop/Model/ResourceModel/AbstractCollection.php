<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 06/04/2020 15:08
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as AbstractCoreCollection;

/**
 * Class AbstractCollection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
abstract class AbstractCollection extends AbstractCoreCollection
{
    /**
     * @return \Zend_Db_Expr
     */
    protected function buildQueryActiveCompany()
    {
        return new \Zend_Db_Expr('SELECT ' .CompanyInterface::ENTITY_ID. ' FROM ' .$this->getTable(CompanyInterface::TABLE_NAME). ' WHERE ' .CompanyInterface::COMPANY_STATUS_FIELD. ' = ' .CompanyInterface::COMPANY_CONNECTED);
    }
}