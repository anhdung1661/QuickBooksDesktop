<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 11/04/2020 11:24
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\ResourceModel\ItemMapping\CollectionFactory as ItemMappingCollection;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class PrepareProductXml
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product
 */
abstract class PrepareProductXml
{
    /**
     * @var string
     */
    protected $_requestID;

    /**
     * @var int
     */
    protected $_action = QueueInterface::ACTION_ADD;

    /**
     * @var ProductInterface|mixed
     */
    protected $_productData;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var Configuration
     */
    protected $_configuration;

    /**
     * @var ItemMappingCollection
     */
    protected $_itemMappingCollection;

    /**
     * PrepareProductXml constructor.
     * @param ItemMappingCollection $itemMapping
     * @param StockRegistryInterface $stockRegistry
     * @param Configuration $configuration
     */
    public function __construct(
        ItemMappingCollection $itemMapping,
        StockRegistryInterface $stockRegistry,
        Configuration $configuration
    ) {
        $this->_configuration = $configuration;
        $this->stockRegistry = $stockRegistry;
        $this->_itemMappingCollection = $itemMapping;
    }

    /**
     * @param ProductInterface $product
     * @return PrepareProductXml
     */
    public function setProductData(ProductInterface $product)
    {
        $this->_productData = $product;

        return $this;
    }

    /**
     * @return string
     */
    abstract public function getAction();

    /**
     * @param int $action
     * @return $this
     */
    public function setAction($action = QueueInterface::ACTION_ADD)
    {
        $this->_action = $action;

        return $this;
    }

    /**
     * @return string
     */
    abstract public function getBodyXml();

    /**
     * @return $this
     */
    public function setRequestId($requestID)
    {
        $this->_requestID = $requestID;
        return $this;
    }

    /**
     * @return string
     */
    protected function getRequestId()
    {
        if ($this->_requestID == null) {
            return '';
        }
        return ' requestID="' . $this->_requestID . '"';
    }

    /**
     * @param $productId
     * @return StockItemInterface
     */
    protected function getStockItem($productId)
    {
        return $this->stockRegistry->getStockItem($productId);
    }
}