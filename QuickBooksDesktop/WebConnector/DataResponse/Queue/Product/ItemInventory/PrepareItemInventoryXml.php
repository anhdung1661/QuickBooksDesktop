<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 07/04/2020 01:59
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\PriceFormat;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\PrepareProductXml;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class AddItemInventorySendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory
 */
class PrepareItemInventoryXml extends PrepareProductXml implements ItemInventoryAddReq, ItemInventoryModReq, InventoryAdjustmentAddReq
{

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        if ($this->_action == QueueInterface::ACTION_MODIFY) {
            return self::XML_ITEM_INVENTORY_MOD;
        }

        return self::XML_ITEM_INVENTORY_ADD;
    }

    /**
     * @inheritDoc
     */
    public function getBodyXml()
    {
        $xml = '<' . $this->getAction() . 'Rq' .$this->getRequestId(). '>';
        $xml .= '<' . $this->getAction() . '>';

        $xml .= $this->getReferenceModify($this->_productData);

        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_NAME, $this->_productData->getSku());
        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_SALES_DESC, $this->_productData->getName());
        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_SALES_PRICE, PriceFormat::formatPrice(abs($this->_productData->getFinalPrice())));
        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_INCOME_ACCOUNT_FULL_NAME, $this->_configuration->getIncomeAccount());
        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_PURCHASE_DESC, $this->_productData->getName());
        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_PURCHASE_COST, PriceFormat::formatPrice(abs($this->_productData->getCost())));
        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_COGS_ACCOUNT_FULL_NAME, $this->_configuration->getCOGSAccount());
        $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_ASSET_ACCOUNT_FULL_NAME, $this->_configuration->getAssetAccount());
        if ($this->_action == QueueInterface::ACTION_ADD) {
            $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_QUANTITY_ON_HAND, $this->getStockItem($this->_productData->getId())->getQty());
        }
        $xml .= '</' . $this->getAction() . '>';
        $xml .= '</' . $this->getAction() . 'Rq>';

        $xml .= $this->updateQtyWhenUpdateProduct($this->_productData);

        return $xml;
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    private function getReferenceModify(ProductInterface $product)
    {
        $xml = '';
        if ($this->_action == QueueInterface::ACTION_MODIFY) {
            $productMapping = $this->_itemMappingCollection->create()
                ->addFieldToFilter(ItemMappingInterface::M2_PRODUCT_ID, $product->getId())
                ->getLastItem();

            $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_MOD_LIST_ID, $productMapping->getData(ItemInterface::LIST_ID));
            $xml .= BuildXML::buildXml(self::XML_ITEM_INVENTORY_MOD_EDIT_SEQUENCE, $productMapping->getData(ItemInterface::EDIT_SEQUENCE));
        }

        return $xml;
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    private function updateQtyWhenUpdateProduct(ProductInterface $product)
    {
        $xml = '';
        $qty = $this->getStockItem($product->getId())->getQty();
        if ($this->_action == QueueInterface::ACTION_MODIFY && $qty) {
            $xml .= '<' . self::XML_INVENTORY_ADJUSTMENT_ADD . 'Rq>';
            $xml .= '<' . self::XML_INVENTORY_ADJUSTMENT_ADD . '>';

            $xml .= BuildXML::buildXml(self::XML_INVENTORY_ADJUSTMENT_ACCOUNT_REF, $this->_configuration->getCOGSAccount());

            $xml .= '<' . self::XML_INVENTORY_ADJUSTMENT_LINE_ADD . '>';
            $xml .= BuildXML::buildXml(self::XML_INVENTORY_ADJUSTMENT_ITEM_REF, $product->getSku());
            if ($this->getStockItem($product->getId())->getQty()) {
                $xml .= BuildXML::buildXml(self::XML_INVENTORY_ADJUSTMENT_QTY_ADJUSTMENT, $this->getStockItem($product->getId())->getQty());
            } else {
                $xml .= '<QuantityAdjustment>';
                $xml .= '<NewQuantity>0</NewQuantity>';
                $xml .= '</QuantityAdjustment>';
            }
            $xml .= '</' . self::XML_INVENTORY_ADJUSTMENT_LINE_ADD . '>';


            $xml .= '</' . self::XML_INVENTORY_ADJUSTMENT_ADD . '>';
            $xml .= '</' . self::XML_INVENTORY_ADJUSTMENT_ADD . 'Rq>';
        }

        return $xml;
    }
}