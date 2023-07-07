<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 07/04/2020 13:35
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\ItemFactory as ItemModel;
use Magenest\QuickBooksDesktop\Model\ItemMappingFactory as ItemMappingModel;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory\ItemInventoryAddRes;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory\ProcessItemInventoryResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory\ItemNonInventoryAddRes;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory\ProcessItemNonInventoryResponse;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;

/**
 * Class ProductAddReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product
 */
class ProductAddReceiveResponseXML extends AbstractQueueAddReceiveResponseXML
{
    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var ProcessItemInventoryResponse
     */
    private $_processItemInventoryResponse;

    /**
     * @var ProcessItemNonInventoryResponse
     */
    private $_processItemNonInventoryResponse;

    /**
     * @var ItemModel
     */
    protected $_itemModelFactory;

    /**
     * @var ItemMappingModel
     */
    protected $_itemMappingModelFactory;

    /**
     * AbstractQueueAddReceiveResponseXML constructor.
     *
     * @param ItemMappingModel $itemMappingFactory
     * @param ItemModel $itemModelFactory
     * @param ProcessItemNonInventoryResponse $processItemNonInventoryResponse
     * @param ProcessItemInventoryResponse $processItemInventoryResponse
     * @param ProductRepository $productRepository
     * @param QueueCollection $queueCollection
     * @param QueueModelFactory $queueModel
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        ItemMappingModel $itemMappingFactory,
        ItemModel $itemModelFactory,
        ProcessItemNonInventoryResponse $processItemNonInventoryResponse,
        ProcessItemInventoryResponse $processItemInventoryResponse,
        ProductRepository $productRepository,
        QueueCollection $queueCollection,
        QueueModelFactory $queueModel,
        Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger
    ) {
        parent::__construct($queueCollection, $queueModel,$configuration, $sessionConnectFactory, $parserXml, $qbLogger);
        $this->_productRepository = $productRepository;
        $this->_processItemInventoryResponse = $processItemInventoryResponse;
        $this->_processItemNonInventoryResponse = $processItemNonInventoryResponse;
        $this->_itemModelFactory = $itemModelFactory;
        $this->_itemMappingModelFactory = $itemMappingFactory;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function saveEntityData($listQueue)
    {
        $itemData = [];
        $itemMapping = [];
        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));

            foreach ($this->getListKeyTypeRet() as $typeRet) {
                $this->setDetailName($typeRet);

                $listId = '';
                switch ($typeRet) {
                    case ItemInventoryAddRes::DETAIL_NAME:
                        $itemData = array_merge($itemData, $this->_processItemInventoryResponse->setListItemData($this->getData())->processResponseFromQB());
                        $listId = $this->getData(ItemInventoryAddRes::XML_ITEM_INVENTORY_LIST_ID);
                        break;
                    case ItemNonInventoryAddRes::DETAIL_NAME:
                        $itemData = array_merge($itemData, $this->_processItemNonInventoryResponse->setListItemData($this->getData())->processResponseFromQB());
                        $listId = $this->getData(ItemInventoryAddRes::XML_ITEM_INVENTORY_LIST_ID);
                        break;
                }
                $itemMapping[] = [
                    ItemMappingInterface::M2_PRODUCT_ID => $queue[QueueInterface::MAGENTO_ENTITY_ID],
                    ItemInterface::LIST_ID => $listId
                ];
            }
        }

        // save item to database
        $this->_itemModelFactory->create()->setItemsData($itemData)->saveItems();

        // save item mapping
        $this->_itemMappingModelFactory->create()->setItemMapping($itemMapping)->saveMapping();
    }
}
