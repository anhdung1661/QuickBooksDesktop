<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 14:09
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;

/**
 * Class SendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd
 */
class SendRequestXML extends AbstractQueueAddSendRequestXML implements ItemDiscountAddReq
{

    /**
     * @inheritDoc
     */
    protected function processAQueue($itemDiscount)
    {
        $xml = '';
        $xml .= '<' . self::XML_ITEM_DISCOUNT_ADD . 'Rq' . $this->getRequestId($itemDiscount[QueueInterface::ENTITY_ID]) . '>';
        $xml .= '<' . self::XML_ITEM_DISCOUNT_ADD . '>';
        $xml .= BuildXML::buildXml(self::XML_ITEM_DISCOUNT_NAME, $this->_moduleConfig->getDiscountItemName());
        $xml .= BuildXML::buildXml(self::XML_ITEM_DISCOUNT_SALES_TAX_CODE_REF_FULL_NAME, 'Non');
        $xml .= BuildXML::buildXml(self::XML_ITEM_DISCOUNT_INCOME_ACCOUNT_FULL_NAME, $this->_moduleConfig->getDiscountIncomeAccount());
        $xml .= '</' . self::XML_ITEM_DISCOUNT_ADD . '>';
        $xml .= '</' . self::XML_ITEM_DISCOUNT_ADD . 'Rq>';
        return $xml;
    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_ITEM_DISCOUNT;
    }
}