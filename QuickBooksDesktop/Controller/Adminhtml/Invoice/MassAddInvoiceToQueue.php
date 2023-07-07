<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 12/10/2020 13:44
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Invoice;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassAddInvoiceToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Invoice
 */
class MassAddInvoiceToQueue extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    protected $_queueAction;

    public function __construct(
        CollectionFactory $collectionFactory,
        QueueAction $queueAction,
        Context $context, Filter $filter
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->_queueAction = $queueAction;
    }

    /**
     * @inheritDoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        try {
            $invoiceAdded = $invoiceNotAdded = $receivePaymentAdded = $receivePaymentNotAdded = 0;
            $invoiceIds = $collection->getAllIds();
            switch ($this->getRequest()->getParam('actionType')) {
                case 2:
                    list($invoiceAdded, $invoiceNotAdded) = $this->_queueAction->addInvoicesToQueue($invoiceIds);
                    break;
                case 3:
                    list($receivePaymentAdded, $receivePaymentNotAdded) = $this->_queueAction->addReceivePaymentsToQueue($invoiceIds);
                    break;
                default:
                    list($invoiceAdded, $invoiceNotAdded) = $this->_queueAction->addInvoicesToQueue($invoiceIds);
                    list($receivePaymentAdded, $receivePaymentNotAdded) = $this->_queueAction->addReceivePaymentsToQueue($invoiceIds);
            }

            if ($invoiceAdded) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 invoice(s) have been added to Queue.', $invoiceAdded)
                );
            }
            if ($invoiceNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __('A total of %1 invoice(s) cannot add to Queue. These invoice(s) capably be added before, please check again.', $invoiceNotAdded)
                );
            }

            if ($receivePaymentAdded) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 receive payment(s) have been added to Queue.', $receivePaymentAdded)
                );
            }
            if ($receivePaymentNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __('A total of %1 receive payment(s) cannot add to Queue. These receive payment(s) capably be added before, please check again.', $receivePaymentNotAdded)
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}
