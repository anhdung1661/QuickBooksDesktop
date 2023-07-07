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
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Edit
 * @package Magenest\QuickBooksDesktop\Observer\Customer
 */
class Edit implements ObserverInterface
{
    /**
     * @var QueueAction
     */
    protected $_queueHelper;

    protected $_quickbooksLogger;

    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * Edit constructor.
     * @param QuickbooksLogger $quickbooksLogger
     * @param QueueAction $createQueue
     */
    public function __construct(
        RequestInterface $request,
        QuickbooksLogger $quickbooksLogger,
        QueueAction $createQueue
    ) {
        $this->_request = $request;
        $this->_queueHelper = $createQueue;
        $this->_quickbooksLogger = $quickbooksLogger;
    }

    /**
     * Customer edit information address
     *
     * @param Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        try {
            $redirectUrl = $this->_request->getServer('REQUEST_URI');
            if (strpos($redirectUrl, 'qbdesktop/customer/') === false) {
                $event = $observer->getEvent();
                $customer = $event->getCustomerAddress()->getCustomer();
                $this->_queueHelper->addCustomerToQueue([$customer->getId()], Queue::AUTO_ENTER_MODIFY);
            }
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }
}
