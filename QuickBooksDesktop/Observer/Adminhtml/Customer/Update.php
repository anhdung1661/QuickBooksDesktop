<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Observer\Adminhtml\Customer;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Update
 *
 * @package Magenest\QuickBooksDesktop\Observer\Customer
 */
class Update implements ObserverInterface
{
    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * @var QuickbooksLogger
     */
    protected $_quickbooksLogger;

    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * Update constructor.
     * @param QuickbooksLogger $quickbooksLogger
     * @param QueueAction $queueAction
     */
    public function __construct(
        RequestInterface $request,
        QuickbooksLogger $quickbooksLogger,
        QueueAction $queueAction
    ) {
        $this->_request = $request;
        $this->_queueAction = $queueAction;
        $this->_quickbooksLogger = $quickbooksLogger;
    }

    /**
     * add customer to queue when admin update customer information
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $redirectUrl = $this->_request->getServer('REQUEST_URI');
            if (strpos($redirectUrl, 'qbdesktop/customer/') === false) {
                $event = $observer->getEvent();
                $this->_queueAction->addCustomerToQueue([$event->getCustomer()->getId()], Queue::AUTO_ENTER_MODIFY);
            }
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }
}
