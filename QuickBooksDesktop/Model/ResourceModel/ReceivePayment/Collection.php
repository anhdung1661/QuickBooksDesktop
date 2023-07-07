<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 30/09/2020 10:33
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel\ReceivePayment;

use Magenest\QuickBooksDesktop\Api\Data\ReceivePaymentInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\AbstractQuickbooksEntityCollection;

/**
 * Class Collection
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel\ReceivePayment
 */
class Collection extends AbstractQuickbooksEntityCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = ReceivePaymentInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ReceivePayment', 'Magenest\QuickBooksDesktop\Model\ResourceModel\ReceivePayment');
    }

    /**
     * @inheritDoc
     */
    protected function getCompanyIdFieldName()
    {
        return ReceivePaymentInterface::COMPANY_ID;
    }
}