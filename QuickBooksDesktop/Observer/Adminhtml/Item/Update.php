<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Observer\Adminhtml\Item;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Update
 *
 * @package Magenest\QuickBooksDesktop\Observer\Item
 */
class Update implements ObserverInterface
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
     * @var RequestInterface
     */
    protected $request;

    /**
     * Update constructor.
     * @param QuickbooksLogger $quickbooksLogger
     * @param QueueAction $_queueHelper
     */
    public function __construct(
        RequestInterface $request,
        QuickbooksLogger $quickbooksLogger,
        QueueAction $_queueHelper
    ) {
        $this->_request = $request;
        $this->_queueHelper = $_queueHelper;
        $this->_quickbooksLogger = $quickbooksLogger;
    }

    /**
     * Admin save a Product
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $redirectUrl = $this->_request->getServer('REQUEST_URI');
            if (strpos($redirectUrl, 'qbdesktop/price/update') === false && strpos($redirectUrl, 'qbdesktop/product/') === false) {
                $product = $observer->getEvent()->getProduct();
                $this->_queueHelper->addProductsToQueue([$product->getId()], Queue::AUTO_ENTER_MODIFY);
            }
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }
}
