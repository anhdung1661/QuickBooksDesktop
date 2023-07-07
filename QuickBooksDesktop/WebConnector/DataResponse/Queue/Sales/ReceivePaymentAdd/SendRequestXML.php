<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 29/09/2020 17:05
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd;

use Magenest\QuickBooksDesktop\Api\Data\InvoiceInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\ReceivePaymentInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\PriceFormat;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory as CompanyCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CreditMemo;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice\CollectionFactory as QuickbooksInvoiceCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrderLineItem\CollectionFactory as SalesOrderLineItemCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping\CollectionFactory as TaxesMappingCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd\SendRequestXML as InvoiceAddSendRequestXML;
use Magenest\QuickBooksDesktop\Helper\PaymentMethodMapping;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Tax\ItemFactory;
use Magento\Sales\Model\Order\TaxFactory as OrderTax;
use Magento\Sales\Model\ResourceModel\Order\Tax\ItemFactory as TaxItemResourceFactory;
use Magento\Tax\Model\Calculation\RateFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\ReceivePayment\CollectionFactory as PaymentCollection;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd
 */
class SendRequestXML extends InvoiceAddSendRequestXML implements ReceivePaymentReq
{
    /**
     * @var PaymentCollection
     */
    protected $receivePaymentCollection;

    /**
     * SendRequestXML constructor.
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param CompanyCollection $companyCollection
     * @param CreditMemo $creditMemo
     * @param CustomerCollection $customerCollection
     * @param QuickbooksInvoiceCollection $qbInvoiceCollection
     * @param SalesOrderLineItemCollection $salesOrderLineItemCollection
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param TaxItemResourceFactory $orderItemTaxFactory
     * @param ItemFactory $taxOrderItem
     * @param RateFactory $taxCalculation
     * @param TaxesMappingCollection $taxesMappingCollection
     * @param OrderTax $orderTax
     * @param TimezoneInterface $timezone
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerMappingCollection $customerMappingCollection
     * @param CountryFactory $countryFactory
     * @param SearchCriteriaInterface $searchCriteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param QueueCollectionFactory $queueCollection
     * @param QueueFactory $queueFactory
     * @param Configuration $configuration
     * @param SessionModel $sessionCollection
     * @param PaymentCollection $receivePaymentCollection
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        CreditmemoRepositoryInterface $creditmemoRepository,
        CompanyCollection $companyCollection,
        CreditMemo $creditMemo,
        CustomerCollection $customerCollection,
        QuickbooksInvoiceCollection $qbInvoiceCollection,
        SalesOrderLineItemCollection $salesOrderLineItemCollection,
        InvoiceRepositoryInterface $invoiceRepository,
        TaxItemResourceFactory $orderItemTaxFactory,
        ItemFactory $taxOrderItem,
        RateFactory $taxCalculation,
        TaxesMappingCollection $taxesMappingCollection,
        OrderTax $orderTax,
        TimezoneInterface $timezone,
        OrderRepositoryInterface $orderRepository,
        CustomerMappingCollection $customerMappingCollection,
        CountryFactory $countryFactory,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory,
        Configuration $configuration,
        SessionModel $sessionCollection,
        PaymentCollection $receivePaymentCollection,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct(
            $creditmemoRepository,
            $companyCollection,
            $creditMemo,
            $customerCollection,
            $qbInvoiceCollection,
            $salesOrderLineItemCollection,
            $invoiceRepository,
            $orderItemTaxFactory,
            $taxOrderItem,
            $taxCalculation,
            $taxesMappingCollection,
            $orderTax,
            $timezone,
            $orderRepository,
            $customerMappingCollection,
            $countryFactory,
            $searchCriteria,
            $filterGroup,
            $filterBuilder,
            $queueCollection,
            $queueFactory,
            $configuration,
            $sessionCollection,
            $qbLogger
        );
        $this->receivePaymentCollection = $receivePaymentCollection;
    }

    /**
     * @inheritDoc
     */
    protected function processAQueue($invoice)
    {
        $xml = '';

        if ($invoice[QueueInterface::ACTION] == QueueInterface::ACTION_DELETE) {
            return $this->getDeletePaymentXml($invoice);
        }

        $this->setInvoice($invoice[QueueInterface::MAGENTO_ENTITY_ID]);

        $xml .= '<' . self::XML_SALES_RECEIVE_PAYMENT_ADD . 'Rq' . $this->getRequestId($invoice[QueueInterface::ENTITY_ID]) . '>';
        $xml .= '<' . self::XML_SALES_RECEIVE_PAYMENT_ADD . '>';
        $xml .= $this->getCustomerXml();
        $xml .= $this->getTxnDateXml();
        $xml .= $this->getRefNumberXml();
        $xml .= $this->getTotalAmountXml();
        $xml .= $this->getPaymentMethodXml();
        $xml .= $this->getMemoXml();// Add "AMAZON" to memo field if customer group is Amazon
        $xml .= $this->getDepositToAccountRefXml();
        $xml .= $this->getLineItemXml();

        $xml .= '</' . self::XML_SALES_RECEIVE_PAYMENT_ADD . '>';
        $xml .= '</' . self::XML_SALES_RECEIVE_PAYMENT_ADD . 'Rq>';

        return $xml;
    }

    /**
     * @param $invoice
     * @return string
     * @throws LocalizedException
     */
    public function getDeletePaymentXml($invoice)
    {
        $xml = '';
        $paymentModel = $this->receivePaymentCollection->create()
            ->addFieldToFilter(ReceivePaymentInterface::MAGENTO_ID, $invoice[QueueInterface::MAGENTO_ENTITY_ID])
            ->addFieldToFilter(QueueInterface::COMPANY_ID, $this->companyCollection->create()->getActiveCompany()->getCompanyId())
            ->getLastItem();
        if (!$paymentModel->getId()) {
            throw new LocalizedException(__('ListID was not found in the mapping table.'));
        }

        $txnId = $paymentModel->getListId();
        $xml .= '<' . self::XML_RECEIVE_PAYMENT_DELETE . ' ' . $this->getRequestId($invoice[QueueInterface::ENTITY_ID]) . '>';
        $xml .= BuildXML::buildXml(self::XML_TXN_DEL_TYPE, 'ReceivePayment');
        $xml .= BuildXML::buildXml(self::XML_TXN_ID, $txnId);
        $xml .= '</' . self::XML_RECEIVE_PAYMENT_DELETE . '>';
        return $xml;
    }

    /**
     * Get deposit account xml.
     * @return string
     * @throws LocalizedException
     */
    public function getDepositToAccountRefXml()
    {
        $xml = '';
        $depositAccount = '';
        $paymentMethod = $this->getOrder()->getPayment()->getMethodInstance()->getCode();
        if ($paymentMethod == 'm2epropayment') {
            $customerGroupId = $this->getOrder()->getCustomerGroupId();
            $amazonCustomerGroupId = $this->_moduleConfig->getAmazonCustomerGroupMapping();
            $ebayCustomerGroupId = $this->_moduleConfig->getEbayCustomerMapping();
            $walmartCustomerGroupId = $this->_moduleConfig->getWalmartCustomerMapping();
            $zoroCustomerGroupId = $this->_moduleConfig->getZoroCustomerMapping();
            if ($customerGroupId == $amazonCustomerGroupId) {
                $depositAccount = '12300 · Amazon Payment';
            } elseif ($customerGroupId == $zoroCustomerGroupId) {
                $depositAccount = 'Zoro Payment';
            } elseif ($customerGroupId == $ebayCustomerGroupId) {
                $depositAccount = 'Ebay Payment';
            } elseif ($customerGroupId == $walmartCustomerGroupId) {
                $depositAccount = 'Walmart Payment';
            }
        }
        if ($paymentMethod && !$depositAccount) {
            if ($mapping = PaymentMethodMapping::getPaymentMethodAndAccountMapping($paymentMethod)) {
                $depositAccount = $mapping[PaymentMethodMapping::QUICK_BOOKS_DEPOSIT_ACCOUNT];
            }
        }
        if ($depositAccount) {
            $xml .= BuildXML::buildXml(self::XML_SALES_RECEIVE_PAYMENT_DEPOSIT_ACCOUNT, $depositAccount);
        }

        return $xml;
    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_RECEIVE_PAYMENT;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getTotalAmountXml()
    {
        return BuildXML::buildXml(self::XML_SALES_RECEIVE_PAYMENT_TOTAL_AMOUNT, PriceFormat::formatPrice($this->getInvoice()->getBaseGrandTotal()));
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPaymentMethodXml()
    {
        $xml = '';
        $paymentMethod = '';
        $method = $this->getOrder()->getPayment()->getMethodInstance()->getCode();
        if ($method == 'm2epropayment') {
            $customerGroupId = $this->getOrder()->getCustomerGroupId();
            $amazonCustomerGroupId = $this->_moduleConfig->getAmazonCustomerGroupMapping();
            $ebayCustomerGroupId = $this->_moduleConfig->getEbayCustomerMapping();
            $walmartCustomerGroupId = $this->_moduleConfig->getWalmartCustomerMapping();
            $zoroCustomerGroupId = $this->_moduleConfig->getZoroCustomerMapping();
            if ($customerGroupId == $amazonCustomerGroupId) {
                $paymentMethod = 'Amazon';
            } elseif ($customerGroupId == $zoroCustomerGroupId) {
                $paymentMethod = 'Zoro';
            } elseif ($customerGroupId == $ebayCustomerGroupId) {
                $paymentMethod = 'Ebay';
            } elseif ($customerGroupId == $walmartCustomerGroupId) {
                $paymentMethod = 'Walmart';
            }
        }
        if ($method && !$paymentMethod) {
            if ($mapping = PaymentMethodMapping::getPaymentMethodAndAccountMapping($method)) {
                $paymentMethod = $mapping[PaymentMethodMapping::QUICK_BOOKS_METHOD];
            }
        }
        if (!empty($paymentMethod)) {
            $xml .= BuildXML::buildXml(self::XML_SALES_RECEIVE_PAYMENT_PAYMENT_METHOD_REF, $paymentMethod);
        }

        return $xml;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function getLineItemXml()
    {
        $xml = '';
        $qbInvoice = $this->_qbInvoiceCollection->create()->addFieldToFilter(InvoiceInterface::MAGENTO_ID, $this->getInvoice()->getEntityId())->getLastItem();
        if (!$qbInvoice->getId()) {
            throw new LocalizedException(__('Not found TXN ID for Invoice. You have to synchronize the Invoice first!'));
        }

        $xml .= '<' . self::XML_SALES_RECEIVE_PAYMENT_APPLIED_TO_TXN_ADD . '>';
        $xml .= '<TxnID useMacro="MACROTYPE">' . $qbInvoice->getData(InvoiceInterface::LIST_ID) . '</TxnID>';
        $xml .= BuildXML::buildXml(self::XML_SALES_RECEIVE_PAYMENT_AMOUNT, PriceFormat::formatPrice($this->getInvoice()->getBaseGrandTotal()));
        $xml .= '</' . self::XML_SALES_RECEIVE_PAYMENT_APPLIED_TO_TXN_ADD . '>';

        return $xml;
    }
}