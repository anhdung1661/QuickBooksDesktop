<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 09/10/2020 16:41
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Order;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassAddOrderToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Order
 */
class MassAddOrderToQueue extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction implements HttpPostActionInterface
{
    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * MassAddOrderToQueue constructor.
     * @param QueueAction $queueAction
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(
        QueueAction $queueAction,
        CollectionFactory $collectionFactory,
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
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $orderAdded = $orderNotAdded = $guestAdded = $guestNotAdded = 0;
            switch ($this->getRequest()->getParam('actionType')) {
                case 2:
                    list($orderAdded, $orderNotAdded) = $this->_queueAction->addOrdersToQueue($collection->getAllIds());
                    break;
                case 3:
                    list($guestAdded, $guestNotAdded) = $this->_queueAction->addGuestsToQueue($collection->getAllIds());
                    break;
                default:
                    list($orderAdded, $orderNotAdded) = $this->_queueAction->addOrdersToQueue($collection->getAllIds());
                    list($guestAdded, $guestNotAdded) = $this->_queueAction->addGuestsToQueue($collection->getAllIds());
            }

            if ($orderAdded) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 order(s) have been added to Queue.', $orderAdded)
                );
            }
            if ($orderNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __('A total of %1 order(s) cannot add to Queue. These orders capably be added before, please check again.', $orderNotAdded)
                );
            }

            if ($guestAdded) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 customer/guest(s) have been added to Queue.', $guestAdded)
                );
            }
            if ($guestNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __('A total of %1 customer/guest(s) cannot add to Queue. These customer/guest(s) capably be added before, please check again.', $guestNotAdded)
                );
            }

        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}