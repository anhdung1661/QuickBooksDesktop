<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 15/04/2020 15:26
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\ItemInterface;
use Magenest\QuickBooksDesktop\Api\Data\ItemMappingInterface;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Item\CollectionFactory as ItemCollection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class ItemMapping
 * @package Magenest\QuickBooksDesktop\Model
 */
class ItemMapping extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = ItemMappingInterface::TABLE_NAME;

    /**
     * @var array
     */
    private $itemsMappingData = [];

    /**
     * @var ItemCollection
     */
    private $itemCollection;

    /**
     * ItemMapping constructor.
     * @param ItemCollection $itemCollection
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        ItemCollection $itemCollection,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->itemCollection = $itemCollection;
    }

    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\ItemMapping');
    }

    /**
     * @param $itemsData
     * @return ItemMapping
     */
    public function setItemMapping($itemsData)
    {
        if (!empty($itemsData)) {
            $listId = array_column($itemsData, ItemInterface::LIST_ID);
            $listItem = $this->itemCollection->create()
                ->addFieldToFilter(ItemInterface::LIST_ID, ['in' => $listId])
                ->addFieldToSelect([ItemInterface::ENTITY_ID, ItemInterface::LIST_ID])
                ->getData();

            $this->itemsMappingData = ProcessArray::getColValueFromThreeDimensional(ProcessArray::mergeArrayThreeD($listItem, $itemsData, ItemInterface::LIST_ID), [
                ItemMappingInterface::QB_ITEM_ID => ItemInterface::ENTITY_ID,
                ItemMappingInterface::M2_PRODUCT_ID => ItemMappingInterface::M2_PRODUCT_ID
            ]);
        }

        return $this;
    }

    /**
     * @return ItemMapping
     * @throws LocalizedException
     */
    public function saveMapping()
    {
        if (empty($this->itemsMappingData)) {
            return parent::save();
        }
        $this->getResource()->saveMultiRows($this->itemsMappingData);
        return $this;
    }
}