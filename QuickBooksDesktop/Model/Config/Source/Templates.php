<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model\Config\Source;

use Magenest\QuickBooksDesktop\Api\Data\UserInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\User\CollectionFactory as UserCollection;

/**
 * Class Templates
 * @package Magenest\QuickBooksDesktop\Model\Config\Source
 */
class Templates implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var User
     */
    protected $_userCollection;

    /**
     * Templates constructor.
     * @param UserCollection $userCollection
     * @param array $data
     */
    public function __construct(
        UserCollection $userCollection,
        array $data = []
    ) {
        $this->_userCollection = $userCollection;
    }

    /**
     * List users
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];
            $userCollection = $this->_userCollection->create()->addFieldToFilter(UserInterface::STATUS_FIELD, UserInterface::Status_VALUE_ACTIVE);
            $this->_options[] = ['value' => '', 'label' => ''];

            foreach ($userCollection as $user) {
                /**
                 * @var User $user
                 */
                $this->_options[] = [
                    'value' => $user->getUserId(),
                    'label' => $user->getUsername()
                ];
            }
        }

        return $this->_options;
    }
}
