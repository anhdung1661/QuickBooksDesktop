<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer;


use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd\CustomerAddReq;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerMod\CustomerModReq;
use Magento\Customer\Model\Address;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;

/**
 * Class AbstractCustomerInformation
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer
 */
abstract class AbstractCustomerInformation implements CustomerInformation
{
    /**
     * @var CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var SearchCriteriaInterface
     */
    protected $_searchCriteria;

    /**
     * @var FilterGroup
     */
    protected $_filterGroup;

    /**
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @var CustomerMappingCollection
     */
    protected $_customerMapping;

    protected $_action = QueueInterface::ACTION_ADD;

    /**
     * CustomerPrepareXml constructor.
     * @param CustomerMappingCollection $customerMapping
     * @param SearchCriteriaInterface $searchCriteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        CustomerMappingCollection $customerMapping,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        CountryFactory $countryFactory
    ) {
        $this->_countryFactory = $countryFactory;
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroup = $filterGroup;
        $this->_filterBuilder = $filterBuilder;
        $this->_customerMapping = $customerMapping;
    }

    /**
     * @param OrderAddressInterface $billingAddress
     * @return string
     */
    public function getBillingAddressXml($billingAddress)
    {
        $xml = '';
        if ($billingAddress->getEntityId()) {
            $xml .= '<BillAddress>';
            $xml .= BuildXML::buildXml(self::XML_BILL_ADDRESS_LINE_1, $billingAddress->getName());
            $xml .= BuildXML::buildXml(self::XML_BILL_ADDRESS_LINE_2, $billingAddress->getStreetLine(1));
            $xml .= BuildXML::buildXml(self::XML_BILL_ADDRESS_LINE_3, $billingAddress->getStreetLine(2));
            $xml .= BuildXML::buildXml(self::XML_BILL_ADDRESS_CITY, $billingAddress->getCity());
            $xml .= BuildXML::buildXml(self::XML_BILL_ADDRESS_STATE, $billingAddress->getRegion());
            $xml .= BuildXML::buildXml(self::XML_BILL_ADDRESS_POSTAL_CODE, $billingAddress->getPostcode());
            $xml .= BuildXML::buildXml(self::XML_BILL_ADDRESS_COUNTRY, $this->getCountryNameByCode($billingAddress->getCountryId()));
            $xml .= '</BillAddress>';
        }

        return $xml;
    }

    /**
     * @param Address $shippingAddress
     * @return string
     */
    public function getShippingAddressXml($shippingAddress)
    {
        $xml = '';
        if ($shippingAddress->getEntityId()) {
            $xml .= '<ShipAddress>';
            $xml .= BuildXML::buildXml(self::XML_SHIP_ADDRESS_LINE_1, $shippingAddress->getName());
            $xml .= BuildXML::buildXml(self::XML_SHIP_ADDRESS_LINE_2, $shippingAddress->getStreetLine(1));
            $xml .= BuildXML::buildXml(self::XML_SHIP_ADDRESS_LINE_3, $shippingAddress->getStreetLine(2));
            $xml .= BuildXML::buildXml(self::XML_SHIP_ADDRESS_CITY, $shippingAddress->getCity());
            $xml .= BuildXML::buildXml(self::XML_SHIP_ADDRESS_STATE, $shippingAddress->getRegion());
            $xml .= BuildXML::buildXml(self::XML_SHIP_ADDRESS_POSTAL_CODE, $shippingAddress->getPostcode());
            $xml .= BuildXML::buildXml(self::XML_SHIP_ADDRESS_COUNTRY, $this->getCountryNameByCode($shippingAddress->getCountryId()));
            $xml .= '</ShipAddress>';
        }

        return $xml;
    }

    /**
     * @param $countryCode
     * @return string
     */
    private function getCountryNameByCode($countryCode)
    {
        return $this->_countryFactory->create()->loadByCode($countryCode)->getName();
    }

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
    protected function getAction()
    {
        if ($this->_action == QueueInterface::ACTION_MODIFY) {
            return CustomerModReq::XML_CUSTOMER_MOD;
        }

        return CustomerAddReq::XML_CUSTOMER_ADD;
    }
}