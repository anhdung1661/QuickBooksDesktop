<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProcessXmlReponse
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product
 */
abstract class ProcessXmlResponse
{
    /**
     * @var array
     */
    protected $_itemsData;

    /**
     * @return array
     */
    abstract public function processResponseFromQB();

    /**
     * set item data that returned by Quickbooks
     *
     * @param $itemData
     * @return ProcessXmlResponse
     */
    public function setListItemData($itemData)
    {
        $this->_itemsData = $itemData;

        return $this;
    }
}