<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax;


use Magenest\QuickBooksDesktop\WebConnector\DataResponse\QWCXML;

/**
 * Interface QueryTaxReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax
 */
interface ItemSalesTaxQueryReq
{
    /**#@+
     * Constants defined by QBWC
     */
    const XML_ITEM_SALES_TAX_QUERY = 'ItemSalesTaxQuery';

    const XML_LIST_ID = 'ListID';

    const XML_EDIT_SEQUENCE = 'EditSequence';

    const XML_NAME = 'Name';

    const XML_INCLUDE_RET_ELEMENT = 'IncludeRetElement';
}