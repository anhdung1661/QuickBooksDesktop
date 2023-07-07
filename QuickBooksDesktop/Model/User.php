<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class User
 * @package Magenest\QuickBooksDesktop\Model
 * @method int getUserId()
 * @method string getPassword()
 * @method boolean getStatus()
 * @method string getExpiredDate()
 * @method string getRemoteIp()
 * @method string getUsername()
 * @method boolean setStatus(string $status)
 */
class User extends AbstractModel
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'magenest_qbd_user';

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\User');
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function isActiveUser($username, $password)
    {
        return $this->getResource()->isActiveUser($username, hash('md5', $password));
    }
}
