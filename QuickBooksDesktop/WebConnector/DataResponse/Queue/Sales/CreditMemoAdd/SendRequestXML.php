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
 * @time: 30/09/2020 11:15
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\CreditMemoAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\PriceFormat;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping\CollectionFactory as TaxesMappingCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
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
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Tax\ItemFactory;
use Magento\Sales\Model\Order\TaxFactory as OrderTax;
use Magento\Sales\Model\ResourceModel\Order\Tax\ItemFactory as TaxItemResourceFactory;
use Magento\Tax\Model\Calculation\RateFactory;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\CreditMemoAdd
 */
class SendRequestXML extends AbstractSalesAddSendRequestXML implements CreditMemoAddReq
{
    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $_creditMemoRepository;

    /**
     * @var CreditmemoInterface[]
     */
    protected $_creditMemos = [];

    /**
     * @var CreditmemoInterface
     */
    protected $_creditMemo = null;

    /**
     * SendRequestXML constructor.
     * @param CustomerCollection $customerCollection
     * @param CreditmemoRepositoryInterface $creditmemoRepository
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
        CustomerCollection $customerCollection,
        CreditmemoRepositoryInterface $creditmemoRepository,
        TaxItemResourceFactory $orderItemTaxFactory, ItemFactory $taxOrderItem, RateFactory $taxCalculation,
        TaxesMappingCollection $taxesMappingCollection, OrderTax $orderTax, TimezoneInterface $timezone,
        OrderRepositoryInterface $orderRepository, CustomerMappingCollection $customerMappingCollection,
        CountryFactory $countryFactory, SearchCriteriaInterface $searchCriteria, FilterGroup $filterGroup,
        FilterBuilder $filterBuilder, QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory, Configuration $configuration,
        SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($customerCollection, $orderItemTaxFactory, $taxOrderItem, $taxCalculation, $taxesMappingCollection, $orderTax, $timezone, $orderRepository, $customerMappingCollection, $countryFactory, $searchCriteria, $filterGroup, $filterBuilder, $queueCollection, $queueFactory, $configuration, $sessionCollection, $qbLogger);
        $this->_creditMemoRepository = $creditmemoRepository;
    }

    /**
     * @param $creditMemoId
     * @return $this
     * @throws LocalizedException
     */
    public function setCreditMemo($creditMemoId)
    {
        if (!isset($this->_creditMemos[$creditMemoId])) {
            $creditMemo = $this->_creditMemoRepository->get($creditMemoId);
            if (!$creditMemo->getEntityId()) {
                throw new LocalizedException(__('Not found Credit Memo ID #%1', $creditMemoId));
            }

            $this->setOrder($creditMemo->getOrderId());
            $this->_creditMemos[$creditMemoId] = $creditMemo;
        }

        $this->_creditMemo = $this->_creditMemos[$creditMemoId];
        return $this;
    }

    /**
     * @return CreditmemoInterface
     * @throws LocalizedException
     */
    public function getCreditMemo()
    {
        if (empty($this->_creditMemo)) {
            throw new LocalizedException(__('You have to set Credit memo to process first!'));
        }

        return $this->_creditMemo;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function processAQueue($creditMemo)
    {
        $xml = '';
        $this->setCreditMemo($creditMemo[QueueInterface::MAGENTO_ENTITY_ID]);

        $xml .= '<' . self::XML_CREDIT_MEMO_ADD . 'Rq' . $this->getRequestId($creditMemo[QueueInterface::ENTITY_ID]) . '>';
        $xml .= '<' . self::XML_CREDIT_MEMO_ADD . '>';
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
        $xml .= '</' . self::XML_CREDIT_MEMO_ADD . '>';
        $xml .= '</' . self::XML_CREDIT_MEMO_ADD . 'Rq>';

        return $xml;
    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_CREDIT_MEMO;
    }

    /**
     * @inheritDoc
     */
    protected function getTxnDateXml()
    {
        return BuildXML::buildXml(self::XML_TXN_DATE,
            $this->_timezone->date($this->getCreditMemo()->getCreatedAt())->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT)
        );
    }

    /**
     * @inheritDoc
     */
    protected function getRefNumberXml()
    {
        return BuildXML::buildXml(self::XML_REF_NUMBER, $this->getCreditMemo()->getIncrementId());
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
        foreach ($this->getCreditMemo()->getItems() as $creditMemoItem) {
            if ($creditMemoItem->getTaxAmount()) {
                $itemWithTaxXml .= $this->prepareItemXml($creditMemoItem);
                $discountForTaxableItem += $creditMemoItem->getDiscountAmount();
            } else {
                $itemWithoutTaxXml .= $this->prepareItemXml($creditMemoItem);
                $discountForNonTaxableItem += $creditMemoItem->getDiscountAmount();
            }
        }
        $taxLineItemXml = '';
        if ($this->getCreditMemo()->getTaxAmount() > 0) {
            $taxLineItemXml = $this->getTaxLineItem($this->getCreditMemo()->getTaxAmount());
        }

        // taxable items
        $lineItemXml .= $itemWithTaxXml;
        $lineItemXml .= $this->getDiscountLineXml($discountForTaxableItem, true);
        if ($this->getCreditMemo()->getShippingTaxAmount()) {
            $lineItemXml .= $this->getShippingLineXml();
        }

        // non-taxable items
        $lineItemXml .= $itemWithoutTaxXml;
        $lineItemXml .= $this->getDiscountLineXml($discountForNonTaxableItem);
        if (!$this->getCreditMemo()->getShippingTaxAmount()) {
            $lineItemXml .= $this->getShippingLineXml();
        }
        $lineItemXml .= $this->getAdjustmentRefund();
        $lineItemXml .= $this->getAdjustmentFeeXml();
        $lineItemXml .= $this->getAdjustmentRefundTax();
        //tax line item.
        $lineItemXml .= $taxLineItemXml;

        return $lineItemXml;
    }

    /**
     * @param $taxAmount
     * @return string
     * @throws LocalizedException
     */
    protected function getTaxLineItem($taxAmount)
    {
        $qbTaxCode = $this->getQuickbookTax();

        if (!$qbTaxCode) {
            return '';
        }

        $xml = '<' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $qbTaxCode);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $qbTaxCode);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_AMOUNT, PriceFormat::formatPrice($taxAmount));
        $xml .= '</' . self::XML_CREDIT_MEMO_LINE_ADD . '>';

        return $xml;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo\Item $item
     * @return string
     * @throws LocalizedException
     */
    private function prepareItemXml(CreditmemoItemInterface $item)
    {
        if ($item->getOrderItem()->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return '';
        }

        if($item->getOrderItem()->getParentItem() && $item->getOrderItem()->getParentItem()->getProductType() == ConfigurableProduct::TYPE_CODE){
            return '';
        }

        if ($item->getOrderItem()->getProductType() == ConfigurableProduct::TYPE_CODE) {
            if (isset($item->getOrderItem()->getChildrenItems()[0])) {
                $item->setProductId($item->getOrderItem()->getChildrenItems()[0]->getProductId());
            }
        }

        if ($item->getOrderItem()->getProduct() && $item->getOrderItem()->getProduct()->getOptions()) {
            $itemSku = $item->getOrderItem()->getProduct()->getSku();
        } else {
            $itemSku = $item->getSku();
        }

        $taxCode = $item->getTaxAmount() != 0 ? $this->getMappingTaxCode($this->getTaxItemById($this->getOrder()->getEntityId(), $item->getOrderItemId())) : null;

        $xml = '<' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $itemSku);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $item->getSku());
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_QTY, $item->getQty());
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($item->getPrice()));
        $xml .= $this->getSalesTaxCodeRefXml($taxCode);
        $xml .= '</' . self::XML_CREDIT_MEMO_LINE_ADD . '>';

        return $xml;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function getShippingLineXml()
    {
        $xml = '';

        if (($shippingAmount = $this->getCreditMemo()->getShippingAmount()) != 0) {
            $xml .= '<' . self::XML_CREDIT_MEMO_LINE_ADD . '>';

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getShippingItemName());

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $this->getOrder()->getShippingDescription());
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($shippingAmount));

            $taxCode = $this->getCreditMemo()->getShippingTaxAmount() > 0 ? $this->getQuickbookTax() : null;
            $xml .= $this->getSalesTaxCodeRefXml($taxCode);

            $xml .= '</' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
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
            $xml .= '<' . self::XML_CREDIT_MEMO_LINE_ADD . '>';

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getDiscountItemName());

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $this->getOrder()->getDiscountDescription());

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice(abs($discountAmount)));

            $taxCode = ($isTaxable || $this->getCreditMemo()->getDiscountTaxCompensationAmount() > 0) ? $this->getQuickbookTax() : null;
            $xml .= $this->getSalesTaxCodeRefXml($taxCode);

            $xml .= '</' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
        }

        return $xml;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getAdjustmentFeeXml()
    {
        $xml = '';

        if (($adjustmentAmount = $this->getCreditMemo()->getAdjustmentNegative()) != 0) {
            if($adjustmentAmount > 0){
                $adjustmentAmount = 0 - $adjustmentAmount;
            }

            $xml .= '<' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getAdjustmentItemName());
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, 'Adjustment Fee');
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($adjustmentAmount));
            $xml .= $this->getSalesTaxCodeRefXml(null);
            $xml .= '</' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
        }

        return $xml;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getAdjustmentRefundTax()
    {
        $xml = '';

        if (($adjustmentAmount = $this->getCreditMemo()->getAdjustmentTaxRefund()) != 0) {
            $taxName = $this->getQuickbookTax();
            $xml .= '<' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $taxName);
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $taxName);
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_AMOUNT, PriceFormat::formatPrice($adjustmentAmount));
//            $xml .= $this->getSalesTaxCodeRefXml(null);
            $xml .= '</' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
        }

        return $xml;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getAdjustmentRefund()
    {
        $xml = '';

        if (($adjustmentAmount = $this->getCreditMemo()->getAdjustmentPositive()) != 0) {
            $xml .= '<' . self::XML_CREDIT_MEMO_LINE_ADD . '>';

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getAdjustmentItemName());
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, 'Adjustment Refund');
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($adjustmentAmount));
            $xml .= $this->getSalesTaxCodeRefXml(null);

            $xml .= '</' . self::XML_CREDIT_MEMO_LINE_ADD . '>';
        }

        return $xml;
    }

    /**
     * @return string
     */
    private function getAdjustmentItemName()
    {
        return $this->_moduleConfig->getAdjustmentItemName();
    }
}
