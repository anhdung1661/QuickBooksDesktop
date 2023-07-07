<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer;


use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface as QBCustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\Model\QueueFactory as QueueModel;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd\CustomerAddReq;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerMod\CustomerModReq;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\SendRequestXML;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magenest\QuickBooksDesktop\Helper\Configuration;

/**
 * Class GuestPrepareXml
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer
 */
class GuestPrepareXml extends AbstractCustomerInformation
{
    /**
     * @var Order
     */
    private $listOrder;

    /**
     * @var array
     */
    private $listQueue;

    /**
     * @var OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var QueueModel
     */
    protected $_queueModel;

    /**
     * @var Configuration
     */
    protected $configHelper;

    /**
     * GuestPrepareXml constructor.
     * @param QueueModel $queueModel
     * @param CustomerMappingCollection $customerMapping
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaInterface $searchCriteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param CountryFactory $countryFactory
     * @param Configuration $configHelper
     */
    public function __construct(
        QueueModel $queueModel,
        CustomerMappingCollection $customerMapping,
        OrderRepository $orderRepository,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        CountryFactory $countryFactory,
        Configuration $configHelper
    ) {
        parent::__construct($customerMapping, $searchCriteria, $filterGroup, $filterBuilder, $countryFactory);
        $this->configHelper = $configHelper;
        $this->_orderRepository = $orderRepository;
        $this->_queueModel = $queueModel;
    }

    /**
     * @param $listQueue
     * @return $this
     */
    public function setOrderData($listQueue)
    {
        $this->listQueue = $listQueue;
        $this->_filterGroup->setFilters([
            $this->_filterBuilder
                ->setField('entity_id')
                ->setConditionType('in')
                ->setValue(array_column($listQueue, QueueInterface::MAGENTO_ENTITY_ID))
                ->create(),
        ]);
        $this->_searchCriteria->setFilterGroups([$this->_filterGroup]);
        $this->listOrder = $this->_orderRepository->getList($this->_searchCriteria)->getItems();

        return $this;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $xml = '';

        $errors = [];
        foreach ($this->listQueue as $queue) {
            /**
             * @var OrderInterface $order
             */
            $order = $this->listOrder[$queue[QueueInterface::MAGENTO_ENTITY_ID]] ?? null;
            if ($order instanceof OrderInterface) {
                $billingAddress = $order->getBillingAddress();

                $xml .= '<' . $this->getAction() . 'Rq' . SendRequestXML::getRequestId($queue[QueueInterface::ENTITY_ID]) . '>';
                $xml .= '<' .  $this->getAction() . '>';

                $xml .= $this->getReferenceModify($order->getEntityId());
                $customerName = $order->getCustomerName();
                if ($this->configHelper->isAutoInsertIdToLastName()) $customerName .=  ' ' . $order->getIncrementId();
                $xml .= BuildXML::buildXml(self::XML_CUSTOMER_NAME, $customerName);
                $xml .= BuildXML::buildXml(self::XML_CUSTOMER_COMPANY_NAME, $billingAddress->getCompany());
                $xml .= BuildXML::buildXml(self::XML_CUSTOMER_FIRST_NAME, $order->getCustomerFirstname());
                $xml .= BuildXML::buildXml(self::XML_CUSTOMER_LAST_NAME, $order->getCustomerLastname());
                // billing address
                $xml .= $this->getBillingAddressXml($billingAddress);
                // shipping address
                $xml .= $this->getShippingAddressXml($order->getShippingAddress());
                $xml .= BuildXML::buildXml(self::XML_CUSTOMER_PHONE, $billingAddress->getTelephone());
                $xml .= BuildXML::buildXml(self::XML_CUSTOMER_EMAIL, $order->getCustomerEmail());

                $xml .= '</' .  $this->getAction() . '>';
                $xml .= '</' .  $this->getAction() . 'Rq>';
            } else {
                $errorsQueue = array_merge($queue, [QueueInterface::MESSAGE => 'This record no longer exist!']);
                $errorsQueue[QueueInterface::STATUS] = QueueInterface::STATUS_PROCESSING;
                $errors[] = $errorsQueue;
            }
        }

        if (!empty($errors)) {
            $this->_queueModel->create()->updateMultipleQueue($errors);
        }

        return $xml;
    }

    /**
     * @param $orderId
     * @return string
     */
    private function getReferenceModify($orderId)
    {
        $xml = '';
        if ($this->_action == QueueInterface::ACTION_MODIFY) {
            $customerMapping = $this->_customerMapping->create()
                ->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_ID, $orderId)
                ->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_TYPE, CustomerMappingInterface::M2_ENTITY_TYPE_GUEST)
                ->getFirstItem();

            $xml .= BuildXML::buildXml(CustomerModReq::XML_CUSTOMER_LIST_ID, $customerMapping->getData(QBCustomerInterface::LIST_ID));
            $xml .= BuildXML::buildXml(CustomerModReq::XML_CUSTOMER_EDIT_SEQUENCE, $customerMapping->getData(QBCustomerInterface::EDIT_SEQUENCE));
        }

        return $xml;
    }
}
