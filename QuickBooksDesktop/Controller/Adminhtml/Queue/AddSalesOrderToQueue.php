<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 09/10/2020 16:25
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

/**
 * Class AddSalesOrderToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue
 */
class AddSalesOrderToQueue extends Action
{
    const ADMIN_RESOURCE = 'Magenest_QuickBooksDesktop::queue';

    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * AddSalesOrderToQueue constructor.
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
            list($numberAdded, $numberNotAdded) = $this->_queueAction->addOrdersToQueue();
            list($numberCustomerAdded, $numberCustomerNotAdded) = $this->_queueAction->addGuestsToQueue();

            if ($numberAdded) {
                $this->messageManager->addSuccessMessage(
                    __(sprintf('Totals %s order(s) have been added to Queue', $numberAdded))
                );
            }
            if ($numberNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __(sprintf('Totals %s  order(s) cannot add to Queue.', $numberNotAdded))
                );
            }

            if ($numberCustomerAdded) {
                $this->messageManager->addSuccessMessage(
                    __(sprintf('Totals %s Customer/Guest(s) have been added to Queue', $numberCustomerAdded))
                );
            }
            if ($numberCustomerNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __(sprintf('Totals %s  Customer/Guest(s) cannot add to Queue.', $numberCustomerNotAdded))
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/index');
    }
}