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
 * @time: 28/04/2020 09:42
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\PriceFormat;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\AbstractSalesAddSendRequestXML;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd
 */
class SendRequestXML extends AbstractSalesAddSendRequestXML implements SalesOrderAddReq
{
    const AMAZON_CUSTOMER_GROUP_ID = 5;

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function processAQueue($order)
    {
        $xml = '';
        $this->setOrder($order[QueueInterface::MAGENTO_ENTITY_ID]);
        $xml .= '<' . self::XML_SALES_ORDER_ADD . 'Rq' . $this->getRequestId($order[QueueInterface::ENTITY_ID]) . '>';
        $xml .= '<' . self::XML_SALES_ORDER_ADD . '>';
        $xml .= $this->getCustomerXml();
        $xml .= $this->getTxnDateXml();
        $xml .= $this->getRefNumberXml();
        $xml .= $this->getBillingAddressXml();
        $xml .= $this->getShippingAddressXml();
        $xml .= $this->getSalesRepRefXml();
        $xml .= $this->getItemSalesTaxRefXml();
        $xml .= $this->getMemoXml();// Add "AMAZON" to memo field if customer group is Amazon
        // Not fill ship via field
        //$xml .= $this->getShipMethodRefXml();
        $xml .= $this->getLineItemXml();
        $xml .= '</' . self::XML_SALES_ORDER_ADD . '>';
        $xml .= '</' . self::XML_SALES_ORDER_ADD . 'Rq>';

        return $xml;
    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_SALES_ORDER;
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
        foreach ($this->getOrder()->getItems() as $orderItem) {
            if ($orderItem->getTaxAmount()) {
                $itemWithTaxXml .= $this->prepareItemXml($orderItem);
                $discountForTaxableItem += $orderItem->getDiscountAmount();
            } else {
                $itemWithoutTaxXml .= $this->prepareItemXml($orderItem);
                $discountForNonTaxableItem += $orderItem->getDiscountAmount();
            }
        }

        $taxLineItemXml = $this->getTaxLineItem($this->getOrder()->getTaxAmount());

        // taxable items
        $lineItemXml .= $itemWithTaxXml;
        $lineItemXml .= $this->getDiscountLineXml($discountForTaxableItem, true);
        if ($this->getOrder()->getShippingTaxAmount()) {
            $lineItemXml .= $this->getShippingLineXml();
        }

        // non-taxable items
        $lineItemXml .= $itemWithoutTaxXml;
        $lineItemXml .= $this->getDiscountLineXml($discountForNonTaxableItem);
        if (!$this->getOrder()->getShippingTaxAmount()) {
            $lineItemXml .= $this->getShippingLineXml();
        }
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

        $xml = '<' . self::XML_SALES_ORDER_LINE_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $qbTaxCode);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $qbTaxCode);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_AMOUNT, PriceFormat::formatPrice($taxAmount));
        $xml .= '</' . self::XML_SALES_ORDER_LINE_ADD . '>';

        return $xml;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     * @throws LocalizedException
     */
    private function prepareItemXml(\Magento\Sales\Model\Order\Item $item)
    {
        if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return '';
        }

        if($item->getParentItem() && $item->getParentItem()->getProductType() == ConfigurableProduct::TYPE_CODE){
            return '';
        }

        if ($item->getProductType() == ConfigurableProduct::TYPE_CODE) {
            if (isset($item->getChildrenItems()[0])) {
                $item->setProductId($item->getChildrenItems()[0]->getProductId());
            }
        }

        if ($item->getProduct() && $item->getProduct()->getOptions()) {
            $itemSku = $item->getProduct()->getSku();
        } else {
            $itemSku = $item->getSku();
        }

        $taxCode = $item->getTaxAmount() != 0 ? $this->getMappingTaxCode($this->getTaxItemById($item->getOrderId(), $item->getItemId())) : null;

        $xml = '<' . self::XML_SALES_ORDER_LINE_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $itemSku);
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $item->getSku());
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_QTY, $item->getQtyOrdered());
        $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($item->getPrice()));
        $xml .= $this->getSalesTaxCodeRefXml($taxCode);
        $xml .= '</' . self::XML_SALES_ORDER_LINE_ADD . '>';

        return $xml;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function getShippingLineXml()
    {
        $xml = '';

        if ($this->getOrder()->getShippingAmount() > 0) {
            $xml .= '<' . self::XML_SALES_ORDER_LINE_ADD . '>';

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getShippingItemName());

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $this->getOrder()->getShippingDescription());
            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice($this->getOrder()->getShippingAmount()));

            $taxCode = $this->getOrder()->getShippingTaxAmount() > 0 ? $this->getQuickbookTax() : null;
            $xml .= $this->getSalesTaxCodeRefXml($taxCode);

            $xml .= '</' . self::XML_SALES_ORDER_LINE_ADD . '>';
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
            $xml .= '<' . self::XML_SALES_ORDER_LINE_ADD . '>';

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_REF, $this->getDiscountItemName());

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_DESC, $this->getOrder()->getDiscountDescription());

            $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_LINE_ITEM_RATE, PriceFormat::formatPrice(abs($discountAmount)));

            $taxCode = ($isTaxable || $this->getOrder()->getDiscountTaxCompensationAmount() > 0) ? $this->getQuickbookTax() : null;
            $xml .= $this->getSalesTaxCodeRefXml($taxCode);

            $xml .= '</' . self::XML_SALES_ORDER_LINE_ADD . '>';
        }

        return $xml;
    }
}
