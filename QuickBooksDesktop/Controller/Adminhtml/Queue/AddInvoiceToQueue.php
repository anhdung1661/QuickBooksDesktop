<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 12/10/2020 13:35
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

/**
 * Class AddInvoiceToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue
 */
class AddInvoiceToQueue extends Action
{
    const ADMIN_RESOURCE = 'Magenest_QuickBooksDesktop::queue';

    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * AddInvoiceToQueue constructor.
     * @param QueueAction $queueAction
     * @param Action\Context $context
     */
    public function __construct(
        QueueAction $queueAction,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->_queueAction = $queueAction;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            list($numberInvoiceAdded, $numberInvoiceNotAdded) = $this->_queueAction->addInvoicesToQueue();
            list($numberReceivePaymentAdded, $numberReceivePaymentNotAdded) = $this->_queueAction->addReceivePaymentsToQueue();

            if ($numberInvoiceAdded) {
                $this->messageManager->addSuccessMessage(
                    __(sprintf('Totals %s invoice(s) have been added to Queue', $numberInvoiceAdded))
                );
            }
            if ($numberInvoiceNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __(sprintf('Totals %s  invoice(s) cannot add to Queue', $numberInvoiceNotAdded))
                );
            }
            if ($numberReceivePaymentAdded) {
                $this->messageManager->addSuccessMessage(
                    __(sprintf('Totals %s receive payment(s) have been added to Queue', $numberReceivePaymentAdded))
                );
            }
            if ($numberReceivePaymentNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __(sprintf('Totals %s  receive payment(s) cannot add to Queue', $numberReceivePaymentNotAdded))
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/index');
    }
}