<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 03/03/2020 17:14
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax;

use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\AbstractQuerySendRequestXML;

/**
 * Class QuerySendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax
 */
class QuerySendRequestXML extends AbstractQuerySendRequestXML implements ItemSalesTaxQueryReq
{

    /**
     * @inheritDoc
     */
    protected function getOnError()
    {
        return self::ATTR_CONTINUE_ON_ERROR;
    }

    /**
     * @inheritDoc
     */
    protected function getFirstXml()
    {
        $xml = '<' . self::XML_ITEM_SALES_TAX_QUERY . 'Rq' . $this->getRequestId(1) . '>';
        $xml .= BuildXML::buildXml(self::XML_ACTIVE_STATUS, self::VALUE_ACTIVE_STATUS_ALL);
        $xml .= '</' . self::XML_ITEM_SALES_TAX_QUERY . 'Rq>';

        return $xml;
    }

    /**
     * @inheritDoc
     * @return string
     */
    protected function getQueryXml()
    {
        $xml = '<' . self::XML_ITEM_SALES_TAX_QUERY . 'Rq' . $this->getRequestId(1) . $this->getIterator() . '>';

        $xml .= $this->getMaxReturn();
        $xml .= BuildXML::buildXml(self::XML_ACTIVE_STATUS, self::VALUE_ACTIVE_STATUS_ALL);
//        $xml .= $this->simpleXML(self::XML_LIST_ID, self::XML_INCLUDE_RET_ELEMENT);
//        $xml .= $this->simpleXML(self::XML_EDIT_SEQUENCE, self::XML_INCLUDE_RET_ELEMENT);
//        $xml .= $this->simpleXML(self::XML_NAME, self::XML_INCLUDE_RET_ELEMENT);
        $xml .= '</' . self::XML_ITEM_SALES_TAX_QUERY . 'Rq>';

        return $xml;
    }

    /**
     * Get max number of element will return
     * This value setting in Module configuration
     *
     * @return string
     */
    protected function getMaxReturn()
    {
        if ($this->isDividedRequest()) {
            return BuildXML::buildXml(self::XML_MAX_RETURN, 1);
        }
        return '';
    }

    /**
     * This query session
     * @return bool
     */
    protected function isDividedRequest()
    {
        return parent::isDividedRequest();
    }
}