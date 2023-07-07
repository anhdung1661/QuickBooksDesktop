<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Observer\Customer;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Register
 *
 * @package Magenest\QuickBooksDesktop\Observer\Customer
 */
class Register implements ObserverInterface
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
     * Register constructor.
     * @param QuickbooksLogger $quickbooksLogger
     * @param QueueAction $queueHelper
     */
    public function __construct(
        QuickbooksLogger $quickbooksLogger,
        QueueAction $queueHelper
    ) {
        $this->_queueHelper = $queueHelper;
        $this->_quickbooksLogger = $quickbooksLogger;
    }

    /**
     * Customer add account
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        try {
            $event = $observer->getEvent();
            $customer = $event->getCustomer();
            $this->_queueHelper->addCustomerToQueue([$customer->getId()]);
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }
}
