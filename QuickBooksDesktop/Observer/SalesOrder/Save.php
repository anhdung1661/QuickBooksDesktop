<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Observer\SalesOrder;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface as ObserverInterface;

/**
 * Class Save order on frontend
 * @package Magenest\QuickBooksDesktop\Observer\SalesOrder
 */
class Save implements ObserverInterface
{
    /**
     * @var QueueAction
     */
    protected $_queueHelper;

    protected $_quickbooksLogger;

    /**
     * Save constructor.
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
     * event place order
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $this->_queueHelper->addOrdersToQueue([$order->getId()]);
            $this->_queueHelper->addGuestsToQueue([$order->getId()]);
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }
}
