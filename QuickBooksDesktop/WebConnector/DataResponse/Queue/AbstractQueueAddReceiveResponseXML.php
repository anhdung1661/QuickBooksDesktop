<?php

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\Queue;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModelFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ReceiveResponseXML;
use Magento\Framework\Xml\Parser as ParserXml;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\Model\ItemFactory as ItemModel;
use Magenest\QuickBooksDesktop\Model\ResourceModel\ItemMapping\CollectionFactory as ItemMappingCollection;
use Magenest\QuickBooksDesktop\Model\CustomerFactory as CustomerModel;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
/**
 * Class AbstractQueueAddReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue
 */
abstract class AbstractQueueAddReceiveResponseXML extends ReceiveResponseXML
{
    /**
     * @var ItemModel
     */
    protected $_itemModelFactory;

    /**
     * @var ItemMappingCollection
     */
    protected $_itemMappingCollectionFactory;


    protected $_customerModelFacoty;

    /**
     * @var CustomerMappingCollection
     */
    protected $_customerMappingCollectionFactory;

    const TAXABLE_DISCOUNT_ITEM = ' - Taxable';
    private $detailName;

    /**
     * @var QueueModelFactory
     */
    protected $_queueModelFactory;

    /**
     * @var QueueCollection
     */
    protected $_queueCollection;

    /**
     * AbstractQueueAddReceiveResponseXML constructor.
     *
     * @param QueueCollection $queueCollection
     * @param QueueModelFactory $queueModelFactory
     * @param Configuration $configuration
     * @param SessionConnectFactory $sessionConnectFactory
     * @param ParserXml $parserXml
     * @param QuickbooksLogger $qbLogger
     */
    public function __construct(
        QueueCollection $queueCollection,
        QueueModelFactory $queueModelFactory,
        Configuration $configuration,
        SessionConnectFactory $sessionConnectFactory,
        ParserXml $parserXml,
        QuickbooksLogger $qbLogger,
        ItemMappingCollection $_itemMappingCollectionFactory = null,
        ItemModel $itemModelFactory = null,
        CustomerModel $_customerModelFacoty = null,
        CustomerMappingCollection $_customerMappingCollectionFactory = null
    ) {
        parent::__construct($configuration, $sessionConnectFactory, $parserXml, $qbLogger);
        $this->_queueModelFactory = $queueModelFactory;
        $this->_queueCollection = $queueCollection;
        $this->_itemModelFactory = $itemModelFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ItemModel::class);
        $this->_itemMappingCollectionFactory = $_itemMappingCollectionFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ItemMappingCollection::class);
        $this->_customerModelFacoty = $_customerModelFacoty ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CustomerModel::class);
        $this->_customerMappingCollectionFactory = $_customerMappingCollectionFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CustomerMappingCollection::class);
    }

    protected function processResponseFromQB()
    {
        $listRequestId = $this->getListRequestId();
        $listQueue = $this->getListQueueById($listRequestId);

        // update queue status
        $queueData = [];
        foreach ($listQueue as $queue) {
            $this->setResponseData($this->getResponseByRequestId($queue[QueueInterface::ENTITY_ID]));
            $newQueue = $this->prepareUpdateQueueData($queue, $this->getAttribute());

            if (str_contains($newQueue['msg'], 'out-of-date')){
                if ($newQueue['type'] == QueueInterface::TYPE_PRODUCT){
                    $itemsMapping = $this->_itemMappingCollectionFactory->create()->addFieldToFilter('m2_product_id',$newQueue['entity_id'])->getItems();
                    foreach ($itemsMapping as $item){
                        $itemModel = $this->_itemModelFactory->create();
                        $itemModel->load($item->getId())->delete()->save();
                        $newQueue['status'] = 1;
                        $newQueue['msg'] = "";
                    }
                }elseif ($newQueue['type'] == QueueInterface::TYPE_CUSTOMER){
                    $customerMapping = $this->_customerMappingCollectionFactory->create()->addFieldToFilter('m2_customer_id',$newQueue['entity_id'])->getItems();
                    foreach ($customerMapping as $customer){
                        $customerModel = $this->_customerModelFacoty->create();
                        $customerModel->load($customer->getId())->delete()->save();
                        $newQueue['status'] = 1;
                        $newQueue['msg'] = "";
                    }
                }

            }
            $queueData[] = $newQueue;

        }
        $this->_queueModelFactory->create()->updateMultipleQueue($queueData);

        // save entity
        $this->saveEntityData($listQueue);
    }

    /**
     * @param $listQueue
     * @return mixed
     */
    abstract protected function saveEntityData($listQueue);

    /**
     * @param $queueData
     * @param $responseAttribute
     * @return mixed
     */
    protected function prepareUpdateQueueData($queueData, $responseAttribute)
    {
        if (empty($responseAttribute)) {
            $queueData[QueueInterface::STATUS] = QueueInterface::STATUS_FAIL;
        } else if ($responseAttribute[self::STATUS_CODE] != 0) {
            $queueData[QueueInterface::STATUS] = QueueInterface::STATUS_FAIL;
            $queueData[QueueInterface::MESSAGE] = $responseAttribute[self::STATUS_MESSAGE];
        } else {
            $queueData[QueueInterface::STATUS] = QueueInterface::STATUS_SUCCESS;
            $queueData[QueueInterface::MESSAGE] = '';
        }

        return $queueData;
    }

    /**
     * @param $listId
     * @return array
     */
    protected function getListQueueById($listId)
    {
        $queueCollection = $this->_queueCollection->create();
        if (empty($listId)) {
            $queueCollection = $queueCollection->addFieldToFilter(QueueInterface::STATUS, QueueInterface::STATUS_PROCESSING);
        } else {
            $queueCollection = $queueCollection->addFieldToFilter(QueueInterface::ENTITY_ID, ['in' => $listId]);
        }

        return $queueCollection->getData();
    }
    /**
     * @inheritDoc
     */
    protected function getDetailName()
    {
        return $this->detailName;
    }

    /**
     * @param $detailName
     * @return AbstractQueueAddReceiveResponseXML
     */
    public function setDetailName($detailName)
    {
        $this->detailName = $detailName;
        return $this;
    }

    /**
     * @return int
     */
    protected function getTotalProcess()
    {
        $maxRecordsPerRequest = $this->getMaxRecordsPerRequest();
        $totalRecords = $this->_queueCollection->create()
            ->addFieldToFilter(QueueInterface::STATUS, ['in' => [QueueInterface::STATUS_QUEUE, QueueInterface::STATUS_PROCESSING]])
            ->countRecordsEachType()->getData();
        $totalProcess = 0;
        $customerProcess = 0;
        foreach ($totalRecords as $totalRecordsInEachType) {
            if (in_array($totalRecordsInEachType[QueueInterface::MAGENTO_ENTITY_TYPE], [QueueInterface::TYPE_CUSTOMER, QueueInterface::TYPE_GUEST])) {
                $customerProcess += $totalRecordsInEachType['count'];
            } else {
                $totalProcess += empty($maxRecordsPerRequest) ? 1 : ceil($totalRecordsInEachType['count'] / $maxRecordsPerRequest);
            }
        }
        if ($customerProcess) {
            $totalProcess += empty($maxRecordsPerRequest) ? 1 : ceil($customerProcess / $maxRecordsPerRequest);
        }
        return $totalProcess;
    }

    /**
     * @return int
     */
    protected function getMaxRecordsPerRequest(){
        return $this->_configuration->getMaxRecordsPerAddRequest();
    }
}
