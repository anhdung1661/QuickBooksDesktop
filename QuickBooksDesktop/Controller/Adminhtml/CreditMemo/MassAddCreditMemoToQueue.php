<?php


namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\CreditMemo;


use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassAddCreditMemoToQueue extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    protected $_queueAction;

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
        try {
            list($creditMemoAdded, $creditMemoNotAdded) = $this->_queueAction->addCreditMemosToQueue($collection->getAllIds());
            if ($creditMemoAdded) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 credit memo(s) have been added to Queue.', $creditMemoAdded)
                );
            }
            if ($creditMemoNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __('A total of %1 credit memo(s) cannot add to Queue. These credit memo(s) capably be added before, please check again.', $creditMemoNotAdded)
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