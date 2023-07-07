<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 09/10/2020 09:24
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Product;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Controller\Adminhtml\Product\MassDelete;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassAddProductToQueue
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Product
 */
class MassAddProductToQueue extends MassDelete
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * MassAddProductToQueue constructor.
     * @param QueueAction $queueAction
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ProductRepositoryInterface|null $productRepository
     */
    public function __construct(
        QueueAction $queueAction,
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository = null
    ) {
        parent::__construct($context, $productBuilder, $filter, $collectionFactory, $productRepository);
        $this->_queueAction = $queueAction;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            list($productAdded, $productNotAdded) = $this->_queueAction->addProductsToQueue($collection->getAllIds());

            if ($productAdded) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been added to Queue.', $productAdded)
                );
            }
            if ($productNotAdded) {
                $this->messageManager->addNoticeMessage(
                    __('A total of %1 record(s) cannot add to Queue.', $productNotAdded)
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/*/index');
    }
}