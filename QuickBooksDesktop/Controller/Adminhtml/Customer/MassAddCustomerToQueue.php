<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 09/10/2020 15:43
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Customer;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassAddCustomerToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Customer
 */
class MassAddCustomerToQueue extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * MassAddCustomerToQueue constructor.
     * @param QueueAction $queueAction
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        QueueAction $queueAction,
        Context $context, Filter $filter, CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->_queueAction = $queueAction;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function massAction(AbstractCollection $collection)
    {
        try {
            list($customerAdded, $customerNotAdded) = $this->_queueAction->addCustomerToQueue($collection->getAllIds());

            if ($customerAdded) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been added to Queue.', $customerAdded)
                );
            }
            if ($customerNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __('A total of %1 record(s) cannot add to Queue.', $customerNotAdded)
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/index/index');

        return $resultRedirect;
    }
}
