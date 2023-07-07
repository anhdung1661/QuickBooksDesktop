<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace   Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\UserInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class User
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class User extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(UserInterface::TABLE_NAME, UserInterface::ENTITY_ID);
    }

    /**
     * @param $user_name
     * @param $password
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveUser($user_name, $password)
    {
        $connection = $this->getConnection();

        $binds = ['user_name' => $user_name, 'password' => $password];

        $select = $connection->select()
            ->from($this->getMainTable(), [UserInterface::ENTITY_ID])
            ->where(UserInterface::USERNAME_FIELD . ' = :user_name')
            ->where(UserInterface::PASSWORD_FIELD . ' = :password')
            ->where(UserInterface::STATUS_FIELD . ' = 1');

        return $connection->fetchCol($select, $binds) ? true : false;
    }
}
