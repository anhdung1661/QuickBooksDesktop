<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model\ResourceModel\SessionConnect;

use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
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
    protected $_idFieldName = SessionConnectInterface::ENTITY_ID;
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\SessionConnect', 'Magenest\QuickBooksDesktop\Model\ResourceModel\SessionConnect');
    }
}
