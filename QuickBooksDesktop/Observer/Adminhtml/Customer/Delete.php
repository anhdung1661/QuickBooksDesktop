<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Observer\Adminhtml\Customer;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Update
 *
 * @package Magenest\QuickBooksDesktop\Observer\Customer
 */
class Delete implements ObserverInterface
{
    /**
     * @var QueueAction
     */
    protected $_queueHelper;

    /**
     * @var QuickbooksLogger
     */
    protected $_quickbooksLogger;

    /**
     * Delete constructor.
     * @param QuickbooksLogger $quickbooksLogger
     * @param QueueAction $_queueHelper
     */
    public function __construct(
        QuickbooksLogger $quickbooksLogger,
        QueueAction $_queueHelper
    ) {
        $this->_queueHelper = $_queueHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $event = $observer->getEvent();
            $this->_queueHelper->delete($event->getCustomer()->getId(), QueueInterface::TYPE_CUSTOMER);
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }
}
