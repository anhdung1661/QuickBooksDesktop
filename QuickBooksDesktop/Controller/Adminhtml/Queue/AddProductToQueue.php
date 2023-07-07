<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue;

use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;

/**
 * Class AddProductToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue
 */
class AddProductToQueue extends Action
{
    const ADMIN_RESOURCE = 'Magenest_QuickBooksDesktop::queue';

    /**
     * @var QueueAction
     */
    protected $_queueHelper;

    /**
     * SyncProduct constructor.
     * @param ProductCollection $productCollection
     * @param Configuration $moduleConfig
     * @param Action\Context $context
     * @param QueueAction $createQueue
     */
    public function __construct(
        Action\Context $context,
        QueueAction $createQueue
    )
    {
        parent::__construct($context);
        $this->_queueHelper = $createQueue;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            list($numberAdded, $numberNotAdded) = $this->_queueHelper->addProductsToQueue();

            $this->messageManager->addSuccessMessage(
                __(sprintf('Totals %s product have been added to Queue', $numberAdded))
            );
            if ($numberNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __(sprintf('Totals %s product cannot add to Queue', $numberNotAdded))
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
