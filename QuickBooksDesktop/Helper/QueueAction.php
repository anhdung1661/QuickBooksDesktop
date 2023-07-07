<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 07/10/2020 09:55
 */

namespace Magenest\QuickBooksDesktop\Helper;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\PaymentMethodInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magenest\QuickBooksDesktop\Model\PaymentMethodFactory as PaymentMethodModel;
use Magenest\QuickBooksDesktop\Model\Queue;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModel;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory as CompanyCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\PaymentMethod\CollectionFactory as PaymentMethodCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\ShippingMethod\CollectionFactory as ShippingMethodCollection;
use Magenest\QuickBooksDesktop\Model\ShippingMethod;
use Magenest\QuickBooksDesktop\Model\ShippingMethodFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollection;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditMemoCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice;
use Magenest\QuickBooksDesktop\Model\ResourceModel\ReceivePayment;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

/**
 * Class QueueAction
 * @package Magenest\QuickBooksDesktop\Helper
 */
class QueueAction extends AbstractHelper
{
    const PRODUCT_TYPE_NOT_SUPPORT = ['configuration'];
    /**
     * @var array
     */
    private $queueData = [];

    /**
     * @var QueueModel
     */
    protected $_queueModelFactory;

    /**
     * @var Queue
     */
    protected $_queueModel = null;

    /**
     * @var QueueCollection
     */
    protected $_queueCollection;

    /**
     * @var Configuration
     */
    protected $_moduleConfig;

    /**
     * @var QuickbooksLogger
     */
    protected $_quickbooksLogger;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productCollection;

    /**
     * @var CustomerCollection
     */
    protected $_customerCollection;

    /**
     * @var OrderCollection
     */
    protected $_orderCollection;

    /**
     * @var InvoiceCollection
     */
    protected $_invoiceCollection;

    /**
     * @var CreditMemoCollection
     */
    protected $_creditMemoCollection;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shippingConfig;

    /**
     * @var ShippingMethodFactory
     */
    protected $_shippingMethodModel;

    /**
     * @var ShippingMethodCollection
     */
    protected $_shippingMethodCollection;

    /**
     * @var CompanyCollection
     */
    protected $_companyCollection;

    /**
     * @var PaymentMethodModel
     */
    protected $_paymentMethodModel;

    /**
     * @var PaymentMethodCollection
     */
    protected $_paymentMethodCollection;

    /**
     * @var Invoice
     */
    protected $invoiceResource;

    /**
     * @var ReceivePayment
     */
    protected $receivePayment;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * QueueAction constructor.
     * @param PaymentMethodCollection $paymentMethodCollection
     * @param PaymentMethodModel $paymentMethod
     * @param CompanyCollection $companyCollection
     * @param ShippingMethodCollection $shippingMethodCollection
     * @param ShippingMethodFactory $shippingMethodModel
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param CreditMemoCollection $creditMemoCollection
     * @param InvoiceCollection $invoiceCollection
     * @param OrderCollection $orderCollection
     * @param CustomerCollection $customerCollection
     * @param ProductCollection $productCollection
     * @param Configuration $moduleConfig
     * @param QueueCollection $queueCollection
     * @param QueueModel $queueModel
     * @param QuickbooksLogger $quickbooksLogger
     * @param Invoice $invoiceResource
     * @param ReceivePayment $receivePayment
     * @param Context $context
     */
    public function __construct(
        PaymentMethodCollection $paymentMethodCollection,
        PaymentMethodModel $paymentMethod,
        CompanyCollection $companyCollection,
        ShippingMethodCollection $shippingMethodCollection,
        ShippingMethodFactory $shippingMethodModel,
        \Magento\Shipping\Model\Config $shippingConfig,
        CreditMemoCollection $creditMemoCollection,
        InvoiceCollection $invoiceCollection,
        OrderCollection $orderCollection,
        CustomerCollection $customerCollection,
        ProductCollection $productCollection,
        Configuration $moduleConfig,
        QueueCollection $queueCollection,
        QueueModel $queueModel,
        QuickbooksLogger $quickbooksLogger,
        Invoice $invoiceResource,
        ReceivePayment $receivePayment,
        Context $context,
        CreditmemoRepositoryInterface $creditmemoRepository,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        parent::__construct($context);
        $this->_queueModelFactory = $queueModel;
        $this->_queueCollection = $queueCollection;
        $this->_productCollection = $productCollection;
        $this->_moduleConfig = $moduleConfig;
        $this->_quickbooksLogger = $quickbooksLogger;
        $this->_customerCollection = $customerCollection;
        $this->_orderCollection = $orderCollection;
        $this->_invoiceCollection = $invoiceCollection;
        $this->_creditMemoCollection = $creditMemoCollection;
        $this->_shippingConfig = $shippingConfig;
        $this->_shippingMethodModel = $shippingMethodModel;
        $this->_shippingMethodCollection = $shippingMethodCollection;
        $this->_companyCollection = $companyCollection;
        $this->_paymentMethodModel = $paymentMethod;
        $this->_paymentMethodCollection = $paymentMethodCollection;
        $this->invoiceResource = $invoiceResource;
        $this->receivePayment = $receivePayment;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @throws \Exception
     */
    public function addShippingMethod()
    {
        $this->companyIsConnecting();
        $shippingMethodList = $this->_shippingConfig->getAllCarriers();

        // prepare shipping method data
        $shippingData = [];
        foreach ($shippingMethodList as $code => $data) {
            if ($data['id'] != 'ups' && $data['id'] != 'dhl') {
                $shippingData[] = [
                    ShippingMethodInterface::SHIPPING_ID => $data['id']
                ];
            }
        }
        $this->_shippingMethodModel->create()->setShippingMethods($shippingData)->save();

        // add shipping method to Queue
        $allShippingMethods = $this->_shippingMethodCollection->create()->getAllIds();

        $companyId = $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID);
        $queueData = [];
        foreach ($allShippingMethods as $shippingId) {
            $queueData[] = [
                QueueInterface::MAGENTO_ENTITY_ID => $shippingId,
                QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_SHIPPING_METHOD,
                QueueInterface::STATUS => QueueInterface::STATUS_QUEUE,
                QueueInterface::ACTION => QueueInterface::ACTION_ADD,
                QueueInterface::PRIORITY => QueueInterface::PRIORITY_SHIPPING_METHOD,
                QueueInterface::COMPANY_ID => $companyId
            ];
        }

        $this->queueData = $queueData;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function addPaymentMethod()
    {
        $this->companyIsConnecting();
        $paymentMethodsList = $this->_moduleConfig->scopeConfig->getValue('payment');
        $paymentData = [];
        foreach ($paymentMethodsList as $code => $data) {
            $paymentData[] = [
                PaymentMethodInterface::PAYMENT_METHOD => $code
            ];
        }
        $this->_paymentMethodModel->create()->setPaymentMethodsData($paymentData)->save();

        $allPaymentMethods = $this->_paymentMethodCollection->create()->getAllIds();

        $companyId = $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID);
        $queueData = [];
        foreach ($allPaymentMethods as $shippingId) {
            $queueData[] = [
                QueueInterface::MAGENTO_ENTITY_ID => $shippingId,
                QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_PAYMENT_METHOD,
                QueueInterface::STATUS => QueueInterface::STATUS_QUEUE,
                QueueInterface::ACTION => QueueInterface::ACTION_ADD,
                QueueInterface::PRIORITY => QueueInterface::PRIORITY_PAYMENT_METHOD,
                QueueInterface::COMPANY_ID => $companyId
            ];
        }

        $this->queueData = $queueData;

        return $this;
    }

    /**
     * Add an virtual item that use to add a line in SO items for shipping fee
     * shipping item will create 2 items in Quickbooks: one for Shipping fee, one for Adjustment fee
     *
     * @return $this
     * @throws LocalizedException
     */
    public function addShippingItem()
    {
        $this->companyIsConnecting();
        $companyId = $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID);
        $queueData = [
            QueueInterface::MAGENTO_ENTITY_ID => 0,
            QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_ITEM_SHIPPING,
            QueueInterface::STATUS => QueueInterface::STATUS_QUEUE,
            QueueInterface::ACTION => QueueInterface::ACTION_ADD,
            QueueInterface::PRIORITY => QueueInterface::PRIORITY_ITEM_OTHER_CHARGE,
            QueueInterface::COMPANY_ID => $companyId
        ];
        $this->queueData = $queueData;

        return $this;
    }

    /**
     * Add an virtual item that use to add a line in SO items for Discount amount
     *
     * @return $this
     * @throws LocalizedException
     */
    public function addDiscountItem()
    {
        $this->companyIsConnecting();
        $companyId = $this->_companyCollection->create()->getActiveCompany()->getData(CompanyInterface::ENTITY_ID);
        $queueData[] = [
            QueueInterface::MAGENTO_ENTITY_ID => 0,
            QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_ITEM_DISCOUNT,
            QueueInterface::STATUS => QueueInterface::STATUS_QUEUE,
            QueueInterface::ACTION => QueueInterface::ACTION_ADD,
            QueueInterface::PRIORITY => QueueInterface::PRIORITY_ITEM_OTHER_CHARGE,
            QueueInterface::COMPANY_ID => $companyId
        ];
        $this->queueData = $queueData;

        return $this;
    }

    /**
     * @param array $productIds
     * @param int $autoEnterModify
     * @return array
     * @throws LocalizedException
     */
    public function addProductsToQueue($productIds = [], $autoEnterModify = 0)
    {
        $this->companyIsConnecting();
        $numberItemAdded = 0;
        $numberItemNotAdded = 0;
        try {
            $allProductIds = $this->getProducts($productIds)->getAllIds();
            if (!empty($productIds)) {
                $listIdsNotAdded = array_diff($allProductIds, $productIds);
                $productIds = array_diff($allProductIds, $listIdsNotAdded);
            } else {
                $listIdsNotAdded = [];
                $productIds = $allProductIds;
            }
            $queueData = [];
            foreach ($productIds as $productId) {
                $queueData[] = [
                    QueueInterface::MAGENTO_ENTITY_ID => $productId,
                    QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_PRODUCT
                ];
            }
            $divideQueueData = array_chunk($queueData, 10000);

            foreach ($divideQueueData as $setOfChildData) {
                $numberItemAdded += $this->saveProductsToQueue($setOfChildData, $autoEnterModify);
                $numberItemNotAdded += count($setOfChildData) - $numberItemAdded;
            }
            $numberItemNotAdded += count($listIdsNotAdded);

        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        } finally {
            return [$numberItemAdded, $numberItemNotAdded];
        }
    }

    /**
     * @param array $customerIds
     * @param int $autoEnterModify
     * @return array
     * @throws LocalizedException
     */
    public function addCustomerToQueue($customerIds = [], $autoEnterModify = 0)
    {
        $this->companyIsConnecting();
        $numberItemAdded = 0;
        $numberItemNotAdded = 0;
        try {
            $customerIds = $this->getCustomers($customerIds)->getAllIds();

            $queueData = [];
            foreach ($customerIds as $customerId) {
                $queueData[] = [
                    QueueInterface::MAGENTO_ENTITY_ID => $customerId,
                    QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_CUSTOMER
                ];
            }
            $divideQueueData = array_chunk($queueData, 10000);

            foreach ($divideQueueData as $setOfChildData) {
                $numberItemAdded += $this->saveCustomersToQueue($setOfChildData, $autoEnterModify);
                $numberItemNotAdded += count($setOfChildData) - $numberItemAdded;
            }

        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        } finally {
            return [$numberItemAdded, $numberItemNotAdded];
        }
    }

    /**
     * @param array $orderIds
     * @return array
     * @throws LocalizedException
     */
    public function addGuestsToQueue($orderIds = [])
    {
        $this->companyIsConnecting();
        $numberItemAdded = 0;
        $numberItemNotAdded = 0;
        try {
            $allOrders = $this->getOrders($orderIds);

            $queueData = [];
            /**
             * @var Order $order
             */
            foreach ($allOrders as $order) {
                if ($order->getCustomerId()) {
                    $queueData[] = [
                        QueueInterface::MAGENTO_ENTITY_ID => $order->getCustomerId(),
                        QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_CUSTOMER
                    ];
                } else {
                    $queueData[] = [
                        QueueInterface::MAGENTO_ENTITY_ID => $order->getId(),
                        QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_GUEST
                    ];
                }
            }

            $divideQueueData = array_chunk($queueData, 5000);

            foreach ($divideQueueData as $setOfChildData) {
                $numberItemAdded += $this->saveCustomersToQueue($setOfChildData);
                $numberItemNotAdded += count($setOfChildData) - $numberItemAdded;
            }

        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        } finally {
            return [$numberItemAdded, $numberItemNotAdded];
        }
    }

    /**
     * @param array $orderIds
     * @return array
     * @throws LocalizedException
     */
    public function addOrdersToQueue($orderIds = [])
    {
        $this->companyIsConnecting();
        $numberItemAdded = 0;
        $numberItemNotAdded = 0;
        try {
            if ($this->_moduleConfig->allowSyncOrder()) {
                $orderIds = $this->getOrders($orderIds)->getAllIds();

                $queueData = [];
                foreach ($orderIds as $orderId) {
                    $queueData[] = [
                        QueueInterface::MAGENTO_ENTITY_ID => $orderId,
                        QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_SALES_ORDER
                    ];
                }

                $divideQueueData = array_chunk($queueData, 5000);

                foreach ($divideQueueData as $setOfChildData) {
                    $numberItemAdded += $this->saveSalesOrdersToQueue($setOfChildData);
                    $numberItemNotAdded += count($setOfChildData) - $numberItemAdded;
                }
            }

        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        } finally {
            return [$numberItemAdded, $numberItemNotAdded];
        }
    }

    /**
     * @param array $invoiceIds
     * @param int $autoEnterModify
     * @return array
     * @throws LocalizedException
     */
    public function addInvoicesToQueue($invoiceIds = [], $autoEnterModify = 0)
    {
        $this->companyIsConnecting();
        $numberItemAdded = 0;
        $numberItemNotAdded = 0;
        try {
            if ($this->_moduleConfig->allowSyncInvoice()) {
                $invoices = $this->getInvoices($invoiceIds);

                $queueData = [];
                $type = QueueInterface::TYPE_INVOICE;
                if ($autoEnterModify) {
                    $type = QueueInterface::TYPE_EDIT_INVOICE;
                }
                /**
                 * @var Order\Invoice $invoice
                 */
                foreach ($invoices as $invoice) {
                    $queueData[] = [
                        QueueInterface::MAGENTO_ENTITY_ID => $invoice->getId(),
                        QueueInterface::MAGENTO_ENTITY_TYPE => $type
                    ];
                }

                $divideQueueData = array_chunk($queueData, 7000);

                foreach ($divideQueueData as $setOfChildData) {
                    $numberItemAdded += $this->saveInvoicesToQueue($setOfChildData, $autoEnterModify);
                    $numberItemNotAdded += count($setOfChildData) - $numberItemAdded;
                }
            }
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        } finally {
            return [$numberItemAdded, $numberItemNotAdded];
        }
    }

    /**
     * @param array $invoiceIds
     * @return array
     * @throws LocalizedException
     */
    public function addReceivePaymentsToQueue($invoiceIds = [], $autoEnterModify = 0)
    {
        $this->companyIsConnecting();
        $numberItemAdded = 0;
        $numberItemNotAdded = 0;
        try {
            if ($this->_moduleConfig->allowSyncInvoice()) {

                $invoices = $this->getInvoices($invoiceIds);

                $queueData = [];
                foreach ($invoices as $invoice) {
                    if ($invoice->getState() == \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
                        $queueData[] = [
                            QueueInterface::MAGENTO_ENTITY_ID => $invoice->getId(),
                            QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_RECEIVE_PAYMENT
                        ];
                    }
                }

                $divideQueueData = array_chunk($queueData, 7000);

                foreach ($divideQueueData as $setOfChildData) {
                    $numberItemAdded += $this->saveReceivePaymentsToQueue($setOfChildData, $autoEnterModify);
                    $numberItemNotAdded += count($setOfChildData) - $numberItemAdded;
                }
            }
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        } finally {
            return [$numberItemAdded, $numberItemNotAdded];
        }
    }

    /**
     * @param array $creditMemoIds
     * @return array
     * @throws LocalizedException
     */
    public function addCreditMemosToQueue($creditMemoIds = [])
    {
        $this->companyIsConnecting();
        $numberItemAdded = 0;
        $numberItemNotAdded = 0;
        try {
            if ($this->_moduleConfig->allowSyncCreditMemo()) {
                $creditMemos = $this->getCreditMemos($creditMemoIds);

                $queueData = [];
                foreach ($creditMemos as $creditMemo) {
                    $queueData[] = [
                        QueueInterface::MAGENTO_ENTITY_ID => $creditMemo->getId(),
                        QueueInterface::MAGENTO_ENTITY_TYPE => QueueInterface::TYPE_CREDIT_MEMO
                    ];
                }
                $divideQueueData = array_chunk($queueData, 10000);

                foreach ($divideQueueData as $setOfChildData) {
                    $numberItemAdded += $this->saveCreditMemosToQueue($setOfChildData);
                    $numberItemNotAdded += count($setOfChildData) - $numberItemAdded;
                    $this->handleQueue(array_column($setOfChildData, QueueInterface::MAGENTO_ENTITY_ID));
                }
            }
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical($exception->getMessage());
        } finally {
            return [$numberItemAdded, $numberItemNotAdded];
        }
    }

    /**
     * @param $creditMemoIds
     * @throws LocalizedException
     */
    public function handleQueue($creditMemoIds)
    {
        if (!empty($creditMemoIds)) {
            //$creditMemoIds = $this->getFullCredit($creditMemoIds);
            $invoiceIds = $this->invoiceResource->getInvoiceIdsByCredit($creditMemoIds);
            if (count($invoiceIds)) {
                // Queue: delete receive payment synced successfully.
//                $paymentIdSynced = $this->receivePayment->getPaymentIdsSynced($invoiceIds);
//                if (count($paymentIdSynced)) {
//
//                } else {
//                    $receivePaymentNeedDelete = array_diff($invoiceIds, $paymentIdSynced);
//                    $this->_queueModel->getResource()->deleteByEntityIds($receivePaymentNeedDelete);
//                }
                $this->addReceivePaymentsToQueue($invoiceIds, QueueInterface::ACTION_DELETE);
                // Add invoice modify to queue, used link to credit memo.
                $this->addInvoicesToQueue($invoiceIds, 1);
            }
        }
    }

    /**
     * @param $creditMemoIds
     * @return array
     */
    public function getFullCredit($creditMemoIds)
    {
        $result = [];
        foreach ($creditMemoIds as $creditMemoId) {
            $creditMemo = $this->creditmemoRepository->get($creditMemoId);
            $invoiceId = $creditMemo->getInvoiceId();
            $invoice = $this->invoiceRepository->get($invoiceId);
            if ($creditMemo->getGrandTotal() == $invoice->getGrandTotal()) {
                $result[] = $creditMemoId;
            }
        }
        return $result;
    }

    /**
     * @param $entityId
     * @param $type
     * @throws \Exception
     */
    public function delete($entityId, $type)
    {
        $this->getQueueModel()->deleteRow($entityId, $type);
    }

    /**
     * @param $itemsData
     * @param int $autoEnterModify
     * @return int
     */
    protected function saveProductsToQueue($itemsData, $autoEnterModify = 0)
    {
        return $this->getQueueModel()->insertTemporaryTable($itemsData)->saveProducts($autoEnterModify);
    }

    /**
     * @param $itemsData
     * @param int $autoEnterModify
     * @return int
     */
    protected function saveCustomersToQueue($itemsData, $autoEnterModify = 0)
    {
        return $this->getQueueModel()->insertTemporaryTable($itemsData)->saveCustomers($autoEnterModify);
    }

    /**
     * @param $itemsData
     * @return int
     */
    protected function saveSalesOrdersToQueue($itemsData)
    {
        return $this->getQueueModel()->insertTemporaryTable($itemsData)->saveOrders();
    }

    /**
     * @param $itemsData
     * @param int $autoEnterModify
     * @return int
     */
    protected function saveInvoicesToQueue($itemsData, $autoEnterModify = 0)
    {
        return $this->getQueueModel()->insertTemporaryTable($itemsData)->saveInvoices($autoEnterModify);
    }

    /**
     * @param $itemsData
     * @param $autoEnterModify
     * @return int
     */
    protected function saveReceivePaymentsToQueue($itemsData, $autoEnterModify)
    {
        return $this->getQueueModel()->insertTemporaryTable($itemsData)->saveReceivePayments($autoEnterModify);
    }

    /**
     * @param $itemsData
     * @return int
     */
    protected function saveCreditMemosToQueue($itemsData)
    {
        return $this->getQueueModel()->insertTemporaryTable($itemsData)->saveCreditMemos();
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function saveData()
    {
        if (empty($this->queueData)) {
            throw new LocalizedException(__('You have to set Queue data before save!'));
        }

        return $this->getQueueModel()->insertMultipleQueue($this->queueData);
    }

    /**
     * @return Queue
     */
    private function getQueueModel()
    {
        if (is_null($this->_queueModel)) {
            $this->_queueModel = $this->_queueModelFactory->create();
        }
        return $this->_queueModel;
    }

    /**
     * @param array $productIds
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProducts($productIds = [])
    {
        $date = $this->_moduleConfig->getProductDateFrom();
        $items = $this->_productCollection->create();
        $items->addAttributeToFilter(ProductInterface::TYPE_ID, ['nin' => self::PRODUCT_TYPE_NOT_SUPPORT]);
        if (empty($productIds)) {
            if ($date) {
                $items->addFieldToFilter('created_at', ['gteq' => $date]);
            }
        } else {
            $items->addAttributeToFilter('entity_id', ['in' => $productIds]);
        }

        return $items;
    }

    /**
     * @param array $customerIds
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     * @throws LocalizedException
     */
    protected function getCustomers($customerIds = [])
    {
        $dateFrom = $this->_moduleConfig->getCustomerDateFrom();
        $customers = $this->_customerCollection->create();
        if (empty($customerIds)) {
            if (!empty($dateFrom)) {
                $customers->addFieldToFilter('created_at', ['gteq' => $dateFrom]);
            }
        } else {
            $customers->addAttributeToFilter('entity_id', ['in' => $customerIds]);
        }

        return $customers;
    }

    /**
     * @param array $ordersIds
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function getOrders($ordersIds = [])
    {
        $dateFrom = $this->_moduleConfig->getOrderDateFrom();
        $orders = $this->_orderCollection->create()->addAttributeToSelect('*')->addFieldToSelect('*');
        if (empty($ordersIds)) {
            if (!empty($dateFrom)) {
                $orders->addFieldToFilter('created_at', ['gteq' => $dateFrom]);
            }
        } else {
            $orders->addAttributeToFilter('entity_id', ['in' => $ordersIds]);
        }

        return $orders;
    }

    /**
     * @param array $invoiceIds
     * @return \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection
     */
    protected function getInvoices($invoiceIds = [])
    {
        $dateFrom = $this->_moduleConfig->getInvoiceDateFrom();
        $invoices = $this->_invoiceCollection->create();
        if (empty($invoiceIds)) {
            if (!empty($dateFrom)) {
                $invoices->addFieldToFilter('created_at', ['gteq' => $dateFrom]);
            }
        } else {
            $invoices->addAttributeToFilter('entity_id', ['in' => $invoiceIds]);
        }

        return $invoices;
    }

    /**
     * @param array $creditMemoIds
     * @return \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection
     */
    protected function getCreditMemos($creditMemoIds = [])
    {
        $dateFrom = $this->_moduleConfig->getCreditMemoDateFrom();
        $creditMemos = $this->_creditMemoCollection->create();
        if (empty($creditMemoIds)) {
            if (!empty($dateFrom)) {
                $creditMemos->addFieldToFilter('created_at', ['gteq' => $dateFrom]);
            }
        } else {
            $creditMemos->addAttributeToFilter('entity_id', ['in' => $creditMemoIds]);
        }

        return $creditMemos;
    }

    /**
     * @throws LocalizedException
     */
    protected function companyIsConnecting()
    {
        $company = $this->_companyCollection->create()->getActiveCompany();
        if (!$company->getData(CompanyInterface::ENTITY_ID)) {
            throw new LocalizedException(__('QuickBooks error: You aren\'t connecting to any company now. Please connect company first'));
        }
    }
}
