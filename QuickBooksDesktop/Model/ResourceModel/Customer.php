<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 08:52
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Customer
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class Customer extends AbstractQuickbooksEntity
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(CustomerInterface::TABLE_NAME, CustomerInterface::ENTITY_ID);
    }

    /**
     * @param $customerData
     * @return $this
     */
    public function saveCustomers($customerData)
    {
        $this->saveQuickbooksEntity(CustomerInterface::TABLE_NAME, $customerData);

        return $this;
    }

    /**
     * @param $data
     * @return int
     * @throws LocalizedException
     */
    public function updateQuickbooksInformation($data)
    {
        return $this->getConnection()
            ->insertOnDuplicate($this->getMainTable(), $data, [CustomerInterface::LIST_ID, CustomerInterface::EDIT_SEQUENCE, CustomerInterface::NOTE]);
    }
}