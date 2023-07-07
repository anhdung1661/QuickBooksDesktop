<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer;


use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface as QBCustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\ProcessArray;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\Collection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerMod\CustomerModReq;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\SendRequestXML;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use function GuzzleHttp\Promise\queue;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

/**
 * Class CustomerPrepareXml
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer
 */
class CustomerPrepareXml extends AbstractCustomerInformation
{
    /**
     * @var Customer
     */
    private $listCustomer;

    /**
     * @var array
     */
    private $listQueue;

    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var Configuration
     */
    protected $configHelper;

    /**
     * @var CollectionFactory
     */
    protected $customerCollection;

    /**
     * CustomerPrepareXml constructor.
     * @param CustomerMappingCollection $customerMapping
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param CustomerRepository $customerRepository
     * @param SearchCriteriaInterface $searchCriteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param CountryFactory $countryFactory
     * @param Configuration $configHelper
     * @param CollectionFactory $customerCollection
     */
    public function __construct(
        CustomerMappingCollection $customerMapping,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        CustomerRepository $customerRepository,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        CountryFactory $countryFactory,
        Configuration $configHelper,
        CollectionFactory $customerCollection
    ) {
        parent::__construct($customerMapping, $searchCriteria, $filterGroup, $filterBuilder, $countryFactory);
        $this->_customerRepository = $customerRepository;
        $this->_addressFactory = $addressFactory;
        $this->configHelper = $configHelper;
        $this->customerCollection = $customerCollection;
    }

    /**
     * @param $listQueue
     * @return $this
     * @throws LocalizedException
     */
    public function setCustomer($listQueue)
    {
        $this->listQueue = $listQueue;

//        $this->_filterGroup->setFilters([
//            $this->_filterBuilder
//                ->setField('entity_id')
//                ->setConditionType('in')
//                ->setValue(array_column($listQueue, QueueInterface::MAGENTO_ENTITY_ID))
//                ->create(),
//        ]);
//        $this->_searchCriteria->setFilterGroups([$this->_filterGroup]);
//        $this->listCustomer = $this->_customerRepository->getList($this->_searchCriteria)->getItems();
        $collection = $this->customerCollection->create()
            ->addAttributeToFilter('entity_id', ['in' => array_column($listQueue, QueueInterface::MAGENTO_ENTITY_ID)]);
        $collection->getSelect()->group('e.entity_id');
        $this->listCustomer = $collection->getItems();

        return $this;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $xml = '';
        /**
         * @var CustomerInterface $customer
         */
        foreach ($this->listCustomer as $customer) {
            $billingAddress = $this->_addressFactory->create()->load($customer->getDefaultBilling());
            $queue = ProcessArray::getRowByValue($this->listQueue, [QueueInterface::MAGENTO_ENTITY_ID => $customer->getId()]);

            $xml .= '<' . $this->getAction() . 'Rq' . SendRequestXML::getRequestId($queue[QueueInterface::ENTITY_ID]) . '>';
            $xml .= '<' . $this->getAction() . '>';

            $xml .= $this->getReferenceModify($customer);

            $xml .= BuildXML::buildXml(self::XML_CUSTOMER_NAME, $this->getCustomerName($customer));
            $xml .= BuildXML::buildXml(self::XML_CUSTOMER_COMPANY_NAME, $billingAddress->getCompany());
            $xml .= BuildXML::buildXml(self::XML_CUSTOMER_FIRST_NAME, $customer->getFirstname());
            $xml .= BuildXML::buildXml(self::XML_CUSTOMER_LAST_NAME, $customer->getLastname());
            // billing address
            $xml .= $this->getBillingAddressXml($billingAddress);
            // shipping address
            $xml .= $this->getShippingAddressXml($this->_addressFactory->create()->load($customer->getDefaultShipping()));
            $xml .= BuildXML::buildXml(self::XML_CUSTOMER_PHONE, $billingAddress->getTelephone());
            $xml .= BuildXML::buildXml(self::XML_CUSTOMER_EMAIL, $customer->getEmail());

            $xml .= '</' . $this->getAction() . '>';
            $xml .= '</' . $this->getAction() . 'Rq>';
        }

        return $xml;
    }

    /**
     * @param $customer
     * @return string
     */
    private function getCustomerName($customer)
    {
        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        if ($this->configHelper->isAutoInsertIdToLastName()) {
            $customerName .= ' ' . $customer->getId();
        }
        return $customerName;
    }

    /**
     * @param $customer
     * @return string
     */
    private function getReferenceModify($customer)
    {
        $xml = '';
        if ($this->_action == QueueInterface::ACTION_MODIFY) {
            $customerMapping = $this->_customerMapping->create()
                ->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_ID, $customer->getId())
                ->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_TYPE, CustomerMappingInterface::M2_ENTITY_TYPE_CUSTOMER)
                ->getFirstItem();

            $xml .= BuildXML::buildXml(CustomerModReq::XML_CUSTOMER_LIST_ID, $customerMapping->getData(QBCustomerInterface::LIST_ID));
            $xml .= BuildXML::buildXml(CustomerModReq::XML_CUSTOMER_EDIT_SEQUENCE, $customerMapping->getData(QBCustomerInterface::EDIT_SEQUENCE));
        }

        return $xml;
    }
}
