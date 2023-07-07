<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 07/04/2020 13:38
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemInventory\PrepareItemInventoryXml;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ItemNonInventory\PrepareItemNonInventoryXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProductAddSendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product
 */
class ProductAddSendRequestXML extends AbstractQueueAddSendRequestXML
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var ProductInterface[]
     */
    private $listProductData;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var Visibility
     */
    private $productVisibility;

    /**
     * @var PrepareItemInventoryXml
     */
    private $prepareItemInventoryXml;

    /**
     * @var PrepareItemNonInventoryXml
     */
    private $prepareItemNonInventoryXml;

    /**
     * ProductAddSendRequestXML constructor.
     * @param ProductRepository $productRepository
     * @param Visibility $productVisibility
     * @param PrepareItemNonInventoryXml $prepareItemNonInventoryXml
     * @param PrepareItemInventoryXml $prepareItemInventoryXml
     * @param SearchCriteriaInterface $searchCriteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param QueueCollectionFactory $queueCollection
     * @param QueueFactory $queueFactory
     * @param Configuration $configuration
     * @param SessionModel $sessionCollection
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        ProductRepository $productRepository,
        Visibility $productVisibility,
        PrepareItemNonInventoryXml $prepareItemNonInventoryXml,
        PrepareItemInventoryXml $prepareItemInventoryXml,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory,
        Configuration $configuration,
        SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        $this->_productCollectionFactory = $productCollection;
        $this->_productRepository = $productRepository;
        $this->productVisibility = $productVisibility;
        $this->prepareItemInventoryXml = $prepareItemInventoryXml;
        $this->prepareItemNonInventoryXml = $prepareItemNonInventoryXml;
        parent::__construct($searchCriteria, $filterGroup, $filterBuilder, $queueCollection, $queueFactory, $configuration, $sessionCollection, $qbLogger);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function processAQueue($queue)
    {
        $xml = '';
        $product = $this->getListProduct()[$queue[QueueInterface::MAGENTO_ENTITY_ID]] ?? null;
        if (empty($product)) {
            throw new LocalizedException(__('Cannot find this product!'));
        } else {
            switch ($product->getTypeId()) {
                case ProductType::TYPE_SIMPLE:
                case ProductType::TYPE_VIRTUAL:
                case 'giftcard':
                case 'downloadable':
                    $xml .= $this->prepareItemInventoryXml->setRequestId($queue[QueueInterface::ENTITY_ID])
                        ->setAction($queue[QueueInterface::ACTION])
                        ->setProductData($product)->getBodyXml();
                    break;
                default:
                    $xml .= $this->prepareItemNonInventoryXml->setRequestId($queue[QueueInterface::ENTITY_ID])
                        ->setAction($queue[QueueInterface::ACTION])
                        ->setProductData($product)->getBodyXml();
            }
        }
        return $xml;
    }

    /**
     * @return ProductInterface[]
     */
    private function getListProduct()
    {
        if ($this->listProductData == null) {
//            $this->_filterGroup->setFilters([
//                $this->_filterBuilder
//                    ->setField('entity_id')
//                    ->setConditionType('in')
//                    ->setValue(array_column($this->getListEntityInQueue(), QueueInterface::MAGENTO_ENTITY_ID))
//                    ->create(),
//            ]);
//
//            $this->_searchCriteria->setFilterGroups([$this->_filterGroup]);
//            $this->listProductData = $this->_productRepository->getList($this->_searchCriteria)->getItems();

            $productCollection = $this->_productCollectionFactory->create();
            $productCollection->setFlag('has_stock_status_filter', true);
            $productCollection->addAttributeToSelect('*');
            $productCollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $productCollection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
            $productCollection->addFieldToFilter('entity_id', ['in' => array_column($this->getListEntityInQueue(), QueueInterface::MAGENTO_ENTITY_ID)]);
            $this->listProductData = $productCollection->getItems();
        }

        return $this->listProductData;

    }

    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_PRODUCT;
    }
}
