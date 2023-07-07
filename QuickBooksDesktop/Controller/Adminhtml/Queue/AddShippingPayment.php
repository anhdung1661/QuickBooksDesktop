<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 16/10/2020 08:38
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

/**
 * Class AddShippingPayment
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue
 */
class AddShippingPayment extends Action
{
    const ADMIN_RESOURCE = 'Magenest_QuickBooksDesktop::queue';

    protected $_queueAction;

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
            $shippingAdded = $this->_queueAction->addShippingMethod()->saveData();
            $paymentAdded = $this->_queueAction->addPaymentMethod()->saveData();
            $this->_queueAction->addShippingItem()->saveData();
            $this->_queueAction->addDiscountItem()->saveData();

            if ($shippingAdded) {
                $this->messageManager->addSuccessMessage(
                    __(sprintf('Totals %s Shipping method(s) have been added to Queue', $shippingAdded))
                );
            }
            if ($paymentAdded) {
                $this->messageManager->addSuccessMessage(
                    __(sprintf('Totals %s Payment method(s) have been added to Queue', $paymentAdded))
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}