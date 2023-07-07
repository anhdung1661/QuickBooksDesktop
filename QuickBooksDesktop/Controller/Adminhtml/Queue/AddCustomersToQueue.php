<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 09/10/2020 14:45
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

/**
 * Class AddCustomersToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue
 */
class AddCustomersToQueue extends Action
{
    const ADMIN_RESOURCE = 'Magenest_QuickBooksDesktop::queue';

    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * AddCustomersToQueue constructor.
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
            list($numberAdded, $numberNotAdded) = $this->_queueAction->addCustomerToQueue();

            $this->messageManager->addSuccessMessage(
                __(sprintf('Totals %s customer(s) have been added to Queue', $numberAdded))
            );
            if ($numberNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __(sprintf('Totals %s customer(s) cannot add to Queue', $numberNotAdded))
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/index');
    }
}