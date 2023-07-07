<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model\ResourceModel\Company;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magento\Framework\DataObject as Company;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksOnline\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = CompanyInterface::ENTITY_ID;
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\Company', 'Magenest\QuickBooksDesktop\Model\ResourceModel\Company');
    }

    /**
     * @return Company
     */
    public function getActiveCompany()
    {
        return $this->addFieldToFilter(CompanyInterface::COMPANY_STATUS_FIELD, CompanyInterface::COMPANY_CONNECTED)->getLastItem();
    }
}
