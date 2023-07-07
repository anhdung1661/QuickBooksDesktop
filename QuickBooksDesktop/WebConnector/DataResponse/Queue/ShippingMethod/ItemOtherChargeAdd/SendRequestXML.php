<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 18/04/2020 09:53
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ItemOtherChargeAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ItemOtherChargeAdd
 */
class SendRequestXML extends AbstractQueueAddSendRequestXML implements ItemOtherChargeAddReq
{

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_ITEM_SHIPPING;
    }

    /**
     * @inheritDoc
     */
    protected function processAQueue($otherItem)
    {
        $xml = '';
        // Shipping item
        $xml .= '<' . self::XML_ITEM_OTHER_CHARGE_ADD . 'Rq' . $this->getRequestId($otherItem[QueueInterface::ENTITY_ID]) . '>';
        $xml .= '<' . self::XML_ITEM_OTHER_CHARGE_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_ITEM_OTHER_CHARGE_NAME, $this->_moduleConfig->getShippingItemName());
        $xml .= BuildXML::buildXml(self::SALES_TAX_CODE_REF_FULL_NAME, 'Non');
        $xml .= BuildXML::buildXml(self::SALES_OR_PURCHASE_ACCOUNT_REF_FULL_NAME, $this->_moduleConfig->getOtherChargeIncomeAccount());
        $xml .= '</' . self::XML_ITEM_OTHER_CHARGE_ADD . '>';
        $xml .= '</' . self::XML_ITEM_OTHER_CHARGE_ADD . 'Rq>';

        // Adjustment Item
        $xml .= '<' . self::XML_ITEM_OTHER_CHARGE_ADD . 'Rq>';
        $xml .= '<' . self::XML_ITEM_OTHER_CHARGE_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_ITEM_OTHER_CHARGE_NAME, $this->_moduleConfig->getAdjustmentItemName());
        $xml .= BuildXML::buildXml(self::SALES_TAX_CODE_REF_FULL_NAME, 'Non');
        $xml .= BuildXML::buildXml(self::SALES_OR_PURCHASE_ACCOUNT_REF_FULL_NAME, $this->_moduleConfig->getOtherChargeIncomeAccount());
        $xml .= '</' . self::XML_ITEM_OTHER_CHARGE_ADD . '>';
        $xml .= '</' . self::XML_ITEM_OTHER_CHARGE_ADD . 'Rq>';
        return $xml;
    }
}