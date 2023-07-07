<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Observer\Adminhtml\Invoice;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface as ObserverInterface;

/**
 * Class Create
 * @package Magenest\QuickBooksDesktop\Observer\Invoice
 */
class Create implements ObserverInterface
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
     * Create constructor.
     * @param QuickbooksLogger $quickbooksLogger
     * @param QueueAction $queueHelper
     */
    public function __construct(
        RequestInterface $request,
        QuickbooksLogger $quickbooksLogger,
        QueueAction $queueHelper
    ) {
        $this->_request = $request;
        $this->_queueHelper = $queueHelper;
        $this->_quickbooksLogger = $quickbooksLogger;
    }

    /**
     * Invoice created
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $redirectUrl = $this->_request->getServer('REQUEST_URI') !== null ? $this->_request->getServer('REQUEST_URI') : '';
            if (strpos($redirectUrl, 'qbdesktop/create') === false) {
                $invoice = $observer->getEvent()->getInvoice();
                $this->_queueHelper->addInvoicesToQueue([$invoice->getId()]);
                $this->_queueHelper->addReceivePaymentsToQueue([$invoice->getId()]);
            }
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        }
    }
}
