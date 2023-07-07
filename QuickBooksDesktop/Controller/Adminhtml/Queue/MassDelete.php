<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue;

use Magenest\QuickBooksDesktop\Controller\Adminhtml\Queue as AbstractQueue;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModel;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\ResourceConnection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory;

/**
 * Class MassDelete
 *
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class MassDelete extends AbstractQueue
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var CollectionFactory
     */
    protected $companyCollection;

    /**
     * MassDelete constructor.
     * @param QueueModel $queueModel
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param QueueCollectionFactory $queueFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Filter $filter
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $companyCollection
     */
    public function __construct(
        QueueModel $queueModel,
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        QueueCollectionFactory $queueFactory,
        ForwardFactory $resultForwardFactory,
        Filter $filter,
        ResourceConnection $resourceConnection,
        CollectionFactory $companyCollection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->companyCollection = $companyCollection;
        parent::__construct($queueModel, $context, $coreRegistry, $resultPageFactory, $queueFactory, $resultForwardFactory, $filter);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $collections = $this->_filter->getCollection($this->_collectionFactory->create());
            $this->deleteMapping($collections->getAllIds());
            $totals = $this->_queueModel->create()->deleteByQueueIds($collections->getAllIds());
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deteled.', $totals));
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the post(s).'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $queueIds
     */
    public function deleteMapping($queueIds)
    {
        if (!$queueIds) {
            return;
        }

        $activeCompanyId = $this->companyCollection->create()->getActiveCompany()->getCompanyId();
        $productMapping = [];
        $customerMapping = [];
        $salesOrderMapping = [];
        $invoiceMapping = [];
        $receivePaymentMapping = [];
        $creditMemoMapping = [];
        foreach ($queueIds as $queueId) {
            $queueModel = $this->_queueModel->create()->load($queueId);
            $type = $queueModel->getType();
            switch ($type) {
                case QueueInterface::TYPE_PRODUCT:
                    $productMapping[] = $queueModel->getEntityId();
                    break;
                case QueueInterface::TYPE_CUSTOMER:
                case QueueInterface::TYPE_GUEST:
                    $customerMapping[] = $queueModel->getEntityId();
                    break;
                case QueueInterface::TYPE_SALES_ORDER:
                    $salesOrderMapping[] = $queueModel->getEntityId();
                    break;
                case QueueInterface::TYPE_INVOICE:
                    $invoiceMapping[] = $queueModel->getEntityId();
                    break;
                case QueueInterface::TYPE_RECEIVE_PAYMENT:
                    $receivePaymentMapping[] = $queueModel->getEntityId();
                    break;
                case QueueInterface::TYPE_CREDIT_MEMO:
                    $creditMemoMapping[] = $queueModel->getEntityId();
                    break;
                default:
                    break;
            }
        }

        // Delete item mapping
        if (count($productMapping)) {
            $productSql = $this->resourceConnection->getConnection()
                ->select()
                ->from([$this->resourceConnection->getTableName('magenest_qbd__item_mapping')], 'qb_item_id')
                ->where('m2_product_id IN (?)', $productMapping);
            $this->resourceConnection->getConnection()->delete(
                $this->resourceConnection->getTableName('magenest_qbd__item'),
                ['id IN (?)' => $productSql, 'company_id = ?' => $activeCompanyId]
            );
        }

        // Delete customer mapping
        if (count($customerMapping)) {
            $customerSql = $this->resourceConnection->getConnection()
                ->select()
                ->from([$this->resourceConnection->getTableName('magenest_qbd__customer_mapping')], 'qb_customer_id')
                ->where('m2_customer_id IN (?)', $customerMapping);
            $this->resourceConnection->getConnection()->delete(
                $this->resourceConnection->getTableName('magenest_qbd__customer'),
                ['id IN (?)' => $customerSql, 'company_id = ?' => $activeCompanyId]
            );
        }

        // Delete sales order mapping
        if (count($salesOrderMapping)) {
            $this->resourceConnection->getConnection()->delete(
                $this->resourceConnection->getTableName('magenest_qbd__sales_order'),
                ['magento_order_id IN (?)' => $salesOrderMapping, 'company_id = ?' => $activeCompanyId]
            );
        }

        // Delete invoice mapping
        if (count($invoiceMapping)) {
            $this->resourceConnection->getConnection()->delete(
                $this->resourceConnection->getTableName('magenest_qbd__invoice'),
                ['magento_invoice_id IN (?)' => $invoiceMapping, 'company_id = ?' => $activeCompanyId]
            );
        }

        // Delete receive payment mapping
        if (count($receivePaymentMapping)) {
            $this->resourceConnection->getConnection()->delete(
                $this->resourceConnection->getTableName('magenest_qbd__receive_payment'),
                ['magento_invoice_id IN (?)' => $receivePaymentMapping, 'company_id = ?' => $activeCompanyId]
            );
        }

        // Delete credit memo mapping
        if (count($creditMemoMapping)) {
            $this->resourceConnection->getConnection()->delete(
                $this->resourceConnection->getTableName('magenest_qbd__credit_memo'),
                ['magento_credit_memo_id IN (?)' => $creditMemoMapping, 'company_id = ?' => $activeCompanyId]
            );
        }
    }
}
