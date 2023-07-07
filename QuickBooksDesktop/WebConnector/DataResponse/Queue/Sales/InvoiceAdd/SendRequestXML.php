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
 * @time: 25/09/2020 17:17
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Api\Data\SalesOrderLineItemInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\PriceFormat;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Invoice\CollectionFactory as QuickbooksInvoiceCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping\CollectionFactory as TaxesMappingCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\AbstractSalesAddSendRequestXML;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Tax\ItemFactory;
use Magento\Sales\Model\Order\TaxFactory as OrderTax;
use Magento\Sales\Model\ResourceModel\Order\Tax\ItemFactory as TaxItemResourceFactory;
use Magento\Tax\Model\Calculation\RateFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\SalesOrderLineItem\CollectionFactory as SalesOrderLineItemCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CreditMemo;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory as CompanyCollection;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd
 */
class SendRequestXML extends AbstractSalesAddSendRequestXML implements InvoiceAddReq
{
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $_invoiceRepository;

    /**
     * @var InvoiceInterface[]
     */
    protected $_invoices;

    /**
     * @var InvoiceInterface
     */
    private $invoice;

    /**
     * @var SalesOrderLineItemCollection
     */
    protected $_salesOrderLineItemCollection;

    /**
     * @var QuickbooksInvoiceCollection
     */
    protected $_qbInvoiceCollection;

    /**
     * @var CreditMemo
     */
    protected $creditMemo;

    /**
     * @var CompanyCollection
     */
    protected $companyCollection;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

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
        ItemFactory $taxOrderItem, RateFactory $taxCalculation, TaxesMappingCollection $taxesMappingCollection,
        OrderTax $orderTax, TimezoneInterface $timezone, OrderRepositoryInterface $orderRepository,
        CustomerMappingCollection $customerMappingCollection, CountryFactory $countryFactory,
        SearchCriteriaInterface $searchCriteria, FilterGroup $filterGroup, FilterBuilder $filterBuilder,
        QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory, Configuration $configuration, SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($customerCollection, $orderItemTaxFactory, $taxOrderItem, $taxCalculation, $taxesMappingCollection, $orderTax, $timezone, $orderRepository, $customerMappingCollection, $countryFactory, $searchCriteria, $filterGroup, $filterBuilder, $queueCollection, $queueFactory, $configuration, $sessionCollection, $qbLogger);
        $this->_invoiceRepository = $invoiceRepository;
        $this->_salesOrderLineItemCollection = $salesOrderLineItemCollection;
        $this->_qbInvoiceCollection = $qbInvoiceCollection;
        $this->creditMemo = $creditMemo;
        $this->companyCollection = $companyCollection;
        $this->creditmemoRepository = $creditmemoRepository;
    }

    /**
     * @param $invoiceId
     * @return $this
     * @throws LocalizedException
     */
    public function setInvoice($invoiceId)
    {
        if (!isset($this->_invoices[$invoiceId])) {
            $invoice = $this->_invoiceRepository->get($invoiceId);
            if (!$invoice->getEntityId()) {
                throw new LocalizedException(__('Not found invoice ID %1', $invoiceId));
            }

            $this->setOrder($invoice->getOrderId());
            $this->_invoices[$invoiceId] = $invoice;
        }
        $this->invoice = $this->_invoices[$invoiceId];

        return $this;
    }

    /**
     * @return InvoiceInterface
     * @throws LocalizedException
     */
    public function getInvoice()
    {
        if (is_null($this->invoice) || empty($this->invoice)) {
            throw new LocalizedException(__('You have to set invoice to process first'));
        }

        return $this->invoice;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function processAQueue($invoice)
    {
        $xml = '';

        $this->setInvoice($invoice[QueueInterface::MAGENTO_ENTITY_ID]);

        if ($invoice[QueueInterface::ACTION] == QueueInterface::ACTION_MODIFY) {
            return $this->processInvoiceMod($invoice);
        }

        $xml .= '<' . self::XML_SALES_INVOICE_ADD . 'Rq' . $this->getRequestId($invoice[QueueInterface::ENTITY_ID]) . '>';
        $xml .= '<' . self::XML_SALES_INVOICE_ADD . '>';
        $xml .= $this->getCustomerXml();
        $xml .= $this->getTxnDateXml();
        $xml .= $this->getRefNumberXml();
        $xml .= $this->getBillingAddressXml();
        $xml .= $this->getShippingAddressXml();
        $xml .= $this->getSalesRepRefXml();
        // Not fill ship via field
        //$xml .= $this->getShipMethodRefXml();
        $xml .= $this->getItemSalesTaxRefXml();
        $xml .= $this->getMemoXml();// Add "AMAZON" to memo field if customer group is Amazon
        $xml .= $this->getLineItemXml();
        $xml .= '</' . self::XML_SALES_INVOICE_ADD . '>';
        $xml .= '</' . self::XML_SALES_INVOICE_ADD . 'Rq>';

        return $xml;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function processInvoiceMod($invoice)
    {
        $xml = '';

        $txnCreditMemo = $this->getCreditMemo();
        $companyId = $this->companyCollection->create()->getActiveCompany()->getCompanyId();

        $invoiceModel = $this->_qbInvoiceCollection->create()
            ->addFieldToFilter('magento_invoice_id', $invoice['entity_id'])
            ->addFieldToFilter('company_id', $companyId)
            ->getLastItem();

        if (!$invoiceModel->getId() || empty($txnCreditMemo['list_id'])) {
            throw new LocalizedException(__('ListID was not found in the mapping table.'));
        }

        $creditMemoAmount = PriceFormat::formatPrice($this->getCreditMemoAmount($txnCreditMemo['magento_credit_memo_id']));

        $xml .= '<InvoiceModRq ' . $this->getRequestId($invoice[QueueInterface::ENTITY_ID]) . '>';
        $xml .= '<InvoiceMod>';
        $xml .= '<TxnID>' . $invoiceModel->getListId() . '</TxnID>';
        $xml .= '<EditSequence>' . $invoiceModel->getEditSequence() . '</EditSequence>';
        $xml .= '<SetCredit>';
        $xml .= '<CreditTxnID>' . $txnCreditMemo['list_id'] . '</CreditTxnID>';
        $xml .= '<AppliedAmount>' . $creditMemoAmount . '</AppliedAmount>';
        $xml .= '</SetCredit>';
        $xml .= '</InvoiceMod>';
        $xml .= '</InvoiceModRq>';

        return $xml;
    }

    /**
     * @return bool|mixed
     * @throws LocalizedException
     */
    public function getCreditMemo()
    {
        if (!$this->getOrder()) {
            return false;
        }

        $creditMemoCollection = $this->getOrder()->getCreditmemosCollection()->getItems();
        if (!$creditMemoCollection) {
            return false;
        }

        $creditMemoIds = array_keys($creditMemoCollection);

        return $this->creditMemo->getByCreditMemoId($creditMemoIds);
    }

    /**
     * Get credit memo grand total.
     * @param $creditMemoId
     * @return float|null
     */
    public function getCreditMemoAmount($creditMemoId)
    {
        $creditMemo = $this->creditmemoRepository->get($creditMemoId);
        return PriceFormat::formatPrice($creditMemo->getGrandTotal());
    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_INVOICE;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getTxnDateXml()
    {
        return BuildXML::buildXml(self::XML_TXN_DATE,
            $this->_timezone->date($this->getInvoice()->getUpdatedAt())->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT)
        );
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getRefNumberXml()
    {
        return BuildXML::buildXml(self::XML_REF_NUMBER, $this->getInvoice()->getIncrementId());
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function getLineItemXml()
    {
        $lineItemXml = '';
        $itemWithTaxXml = '';
        $itemWithoutTaxXml = '';
        $discountForTaxableItem = 0;
        $discountForNonTaxableItem = 0;
        foreach ($this->getInvoice()->getItems() as $invoiceItem) {
            if ($invoiceItem->getTaxAmount()) {
                $itemWithTaxXml .= $this->prepareItemXml($invoiceItem);
                $discountForTaxableItem += $invoiceItem->getDiscountAmount();
            } else {
                $itemWithoutTaxXml .= $this->prepareItemXml($invoiceItem);
                $discountForNonTaxableItem += $invoiceItem->getDiscountAmount();
            }
        }

        $taxAmount = $this->getOrder()->getTaxAmount();
        if ($taxAmount <= 0) {
            $taxAmount = $this->getInvoice()->getTaxAmount();
        }

        $taxLineItemXml = $this->getTaxLineItem($taxAmount);

        // taxable items
        $lineItemXml .= $itemWithTaxXml;
        $lineItemXml .= $this->getDiscountLineXml($discountForTaxableItem, true);
        if ($this->getInvoice()->getShippingTaxAmount()) {
            $lineItemXml .= $this->getShippingLineXml();
        }

        // non-taxable items
        $lineItemXml .= $itemWithoutTaxXml;
        $lineItemXml .= $this->getDiscountLineXml($discountForNonTaxableItem);
        if (!$this->getInvoice()->getShippingTaxAmount()) {
            $lineItemXml .= $this->getShippingLineXml();
        }

        //tax line item.
        $lineItemXml .= $taxLineItemXml;

        return $lineItemXml;
    }

    /**
     * @param $taxAMount
     * @return string
     * @throws LocalizedException
     */
    protected function getTaxLineItem($taxAMount)
    {
        $qbTaxCode = $this->getQuickbookTax();

        if (!$qbTaxCode) {
            return '';
        }

        $xml = '<' . self::XML_INVOICE_LINE_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $qbTaxCode);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $qbTaxCode);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_AMOUNT, PriceFormat::formatPrice($taxAMount));
        $xml .= '</' . self::XML_INVOICE_LINE_ADD . '>';

        return $xml;
    }

    /**
     * @param InvoiceItemInterface $item
     * @return string
     * @throws LocalizedException
     */
    private function prepareItemXml(InvoiceItemInterface $item)
    {
        if($item->getOrderItem()->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE){
            return '';
        }

        if($item->getOrderItem()->getParentItem() && $item->getOrderItem()->getParentItem()->getProductType() == ConfigurableProduct::TYPE_CODE){
            return '';
        }

        if($item->getOrderItem()->getProductType() == ConfigurableProduct::TYPE_CODE){
            if(isset($item->getOrderItem()->getChildrenItems()[0])){
                $item->setProductId($item->getOrderItem()->getChildrenItems()[0]->getProductId());
            }
        }

        if ($item->getOrderItem()->getProduct() && $item->getOrderItem()->getProduct()->getOptions()) {
            $itemSku = $item->getOrderItem()->getProduct()->getSku();
        } else {
            $itemSku = $item->getSku();
        }

        $lineItem = $this->_salesOrderLineItemCollection->create()
            ->filterByOrderId($this->getInvoice()->getOrderId())
            ->addFieldToFilter(SalesOrderLineItemInterface::ITEM_SKU, $item->getSku())->getLastItem();

        $taxCode = $item->getTaxAmount() != 0 ? $this->getMappingTaxCode($this->getTaxItemById($this->getInvoice()->getOrderId(), $item->getOrderItemId())) : null;

        $xml = '<' . self::XML_INVOICE_LINE_ADD . '>';
        if (!$lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID)) {
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $itemSku);
        }
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $item->getSku());
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_QTY, $item->getQty());
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($item->getPrice()));
        $xml .= $this->getSalesTaxCodeRefXml($taxCode);

        if ($lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID)) {
            $xml .= "<LinkToTxn>";
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LIST_ID, $lineItem->getData(SalesOrderLineItemInterface::ORDER_TXN_ID));
            $xml .= BuildXML::buildXml(self::XML_LINE_ITEM_TXN_ID, $lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID));
            $xml .= "</LinkToTxn>";
        }
        $xml .= '</' . self::XML_INVOICE_LINE_ADD . '>';

        return $xml;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function getShippingLineXml()
    {
        $xml = '';

        if ($this->getInvoice()->getShippingAmount() != 0) {
            $lineItem = $this->_salesOrderLineItemCollection->create()
                ->filterByOrderId($this->getInvoice()->getOrderId())
                ->addFieldToFilter(SalesOrderLineItemInterface::ITEM_SKU, $this->getShippingItemName())->getLastItem();

            $xml .= '<' . self::XML_INVOICE_LINE_ADD . '>';
            if (!$lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID)) {
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getShippingItemName());
            }
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $this->getOrder()->getShippingDescription());
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($this->getInvoice()->getShippingAmount()));
            $taxCode = $this->getInvoice()->getShippingTaxAmount() > 0 ? $this->getQuickbookTax() : null;
            $xml .= $this->getSalesTaxCodeRefXml($taxCode);
            if ($lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID)) {
                $xml .= "<LinkToTxn>";
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LIST_ID, $lineItem->getData(SalesOrderLineItemInterface::ORDER_TXN_ID));
                $xml .= BuildXML::buildXml(self::XML_LINE_ITEM_TXN_ID, $lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID));
                $xml .= "</LinkToTxn>";
            }
            $xml .= '</' . self::XML_INVOICE_LINE_ADD . '>';
        }

        return $xml;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function getDiscountLineXml($discountAmount, $isTaxable = false)
    {
        $xml = '';

        if ($discountAmount != 0) {
            $discountItemName = $isTaxable ? $this->getDiscountItemName() . AbstractQueueAddReceiveResponseXML::TAXABLE_DISCOUNT_ITEM : $this->getDiscountItemName();
            $lineItem = $this->_salesOrderLineItemCollection->create()
                ->filterByOrderId($this->getInvoice()->getOrderId())
                ->addFieldToFilter(SalesOrderLineItemInterface::ITEM_SKU, $discountItemName)->getLastItem();

            $xml .= '<' . self::XML_INVOICE_LINE_ADD . '>';
            if (!$lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID)) {
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getDiscountItemName());
            }
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $this->getInvoice()->getDiscountDescription());
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice(abs($discountAmount)));

            $taxCode = ($isTaxable || $this->getInvoice()->getDiscountTaxCompensationAmount() > 0) ? $this->getQuickbookTax() : null;
            $xml .= $this->getSalesTaxCodeRefXml($taxCode);
            if ($lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID)) {
                $xml .= "<LinkToTxn>";
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LIST_ID, $lineItem->getData(SalesOrderLineItemInterface::ORDER_TXN_ID));
                $xml .= BuildXML::buildXml(self::XML_LINE_ITEM_TXN_ID, $lineItem->getData(SalesOrderLineItemInterface::TXN_LINE_ID));
                $xml .= "</LinkToTxn>";
            }
            $xml .= '</' . self::XML_INVOICE_LINE_ADD . '>';
        }

        return $xml;
    }
}
