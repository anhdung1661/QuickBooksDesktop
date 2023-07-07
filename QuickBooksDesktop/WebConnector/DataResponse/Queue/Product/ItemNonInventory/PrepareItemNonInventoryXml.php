<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 20/10/2020 09:17
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\PrepareProductXml;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class PrepareItemNonInventoryXml
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory
 */
class PrepareItemNonInventoryXml extends PrepareProductXml implements ItemNonInventoryAddReq, ItemNonInventoryModReq
{

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        if ($this->_action == QueueInterface::ACTION_MODIFY) {
            return self::XML_ITEM_NON_INVENTORY_MOD;
        }

        return self::XML_ITEM_NON_INVENTORY_ADD;
    }

    /**
     * @inheritDoc
     */
    public function getBodyXml()
    {
        $xml = '<' . $this->getAction() . 'Rq' .$this->getRequestId(). '>';
        $xml .= '<' . $this->getAction() . '>';

        $xml .= $this->getReferenceModify($this->_productData);

        $xml .= BuildXML::buildXml(self::XML_ITEM_NON_INVENTORY_NAME, $this->_productData->getSku());

        $xml .= '<' . $this->getSalesOrPurchaseAction() . '>';
        $xml .= BuildXML::buildXml(self::XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_DESC, $this->_productData->getName());
        $xml .= BuildXML::buildXml(self::XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_PRICE, $this->_productData->getFinalPrice());
        $xml .= BuildXML::buildXml(self::XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_ACCOUNT_REF, $this->_configuration->getExpenseAccount());
        $xml .= '</' . $this->getSalesOrPurchaseAction() . '>';

        $xml .= '</' . $this->getAction() . '>';
        $xml .= '</' . $this->getAction() . 'Rq>';

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
                ->getFirstItem();

            $xml .= BuildXML::buildXml(self::XML_ITEM_NON_INVENTORY_MOD_LIST_ID, $productMapping->getData(ItemInterface::LIST_ID));
            $xml .= BuildXML::buildXml(self::XML_ITEM_NON_INVENTORY_MOD_EDIT_SEQUENCE, $productMapping->getData(ItemInterface::EDIT_SEQUENCE));
        }

        return $xml;
    }

    /**
     * @return string
     */
    private function getSalesOrPurchaseAction()
    {
        if ($this->_action == QueueInterface::ACTION_MODIFY) {
            return self::XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE_MOD;
        }

        return self::XML_ITEM_NON_INVENTORY_SALES_OR_PURCHASE;
    }
}