<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model\ResourceModel\User;

use Magenest\QuickBooksDesktop\Api\Data\UserInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\QuickBooksDesktop\Model\User as Model;

/**
 * Class Collection
 * @method int getUserId()
 * @method string getPassword()
 * @method boolean getStatus()
 * @method string getExpiredDate()
 * @method string getRemoteIp()
 * @method string getUsername()
 * @package Magenest\QuickBooksOnline\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = UserInterface::ENTITY_ID;
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\User', 'Magenest\QuickBooksDesktop\Model\ResourceModel\User');
    }
}
