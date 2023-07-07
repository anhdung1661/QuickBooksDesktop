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
class PlaceOrder implements ObserverInterface
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
            $orderId = $observer->getEvent()->getOrderIds()[0];
            $order = $observer->getEvent()->getOrder();
            $productIds = $this->getProductIds($order);
            $this->_queueHelper->addOrdersToQueue([$orderId]);
            $this->_queueHelper->addGuestsToQueue([$orderId]);
            $this->_queueHelper->addProductsToQueue($productIds);
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }

    /**
     * Get product ids from order.
     * @param $order
     * @return array
     */
    public function getProductIds($order)
    {
        $productIds = [];
        foreach ($order->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }
        return array_unique($productIds);
    }
}
