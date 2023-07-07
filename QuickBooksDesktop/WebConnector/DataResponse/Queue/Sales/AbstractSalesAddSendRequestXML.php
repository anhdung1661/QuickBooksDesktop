<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 28/04/2020 09:36
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales;

use Magenest\QuickBooksDesktop\Api\Data\CustomerInterface;
use Magenest\QuickBooksDesktop\Api\Data\CustomerMappingInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magenest\QuickBooksDesktop\Helper\BuildXML;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Helper\PriceFormat;
use Magenest\QuickBooksDesktop\Model\Config\Source\Version;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\CustomerMapping\CollectionFactory as CustomerMappingCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping\CollectionFactory as TaxesMappingCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionModel;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerInformation;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface as TimezoneInterface;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Tax\ItemFactory;
use Magento\Sales\Model\Order\TaxFactory as OrderTax;
use Magento\Sales\Model\ResourceModel\Order\Tax\ItemFactory as TaxItemResourceFactory;
use Magento\Tax\Model\Calculation\RateFactory;

/**
 * Class AbstractSalesAddSendRequestXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales
 */
abstract class AbstractSalesAddSendRequestXML extends AbstractQueueAddSendRequestXML implements CustomerInformation, SalesAddInterface
{
    const XML_SALES_ORDER_REPRESENTATIVE_FULL_NAME = ['tag_name' => ['SalesRepRef', 'FullName'], 'value_length' => 0];

    const XML_ZERO_RATE_DEFAULT = 'ZERO RATE';

    const XML_MEMO_FIELD = ['tag_name' => ['Memo'], 'value_length' => 0];
    const XML_MEMO_VALUE = 'AMAZON';
    const XML_ZORO_MEMO_VALUE = 'ZORO';
    const XML_WALMART_MEMO_VALUE = 'WALMART';
    const XML_EBAY_MEMO_VALUE = 'EBAY';

    /**
     * @var CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Order[]
     */
    protected $_orders;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var CustomerMappingCollection
     */
    protected $_customerMappingCollection;

    protected $_customerCollection;

    /**
     * @var TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var OrderTax
     */
    protected $_orderTaxFactory;

    protected $_taxesMappingCollection;

    protected $_taxCalculation;

    protected $_taxOrderItem;

    protected $qbTaxCode = null;

    protected $_orderItemAppliedTaxes = [];

    /**
     * @var TaxItemResourceFactory
     */
    protected $orderItemTaxFactory;

    /**
     * AbstractSalesAddSendRequestXML constructor.
     * @param CustomerCollection $customerCollection
     * @param TaxItemResourceFactory $orderItemTaxFactory
     * @param ItemFactory $taxOrderItem
     * @param RateFactory $taxCalculation
     * @param TaxesMappingCollection $taxesMappingCollection
     * @param OrderTax $orderTax
     * @param TimezoneInterface $timezone
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerMappingCollection $customerMappingCollection
     * @param CountryFactory $countryFactory
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
        CustomerCollection $customerCollection,
        TaxItemResourceFactory $orderItemTaxFactory,
        ItemFactory $taxOrderItem,
        RateFactory $taxCalculation,
        TaxesMappingCollection $taxesMappingCollection,
        OrderTax $orderTax,
        TimezoneInterface $timezone,
        OrderRepositoryInterface $orderRepository,
        CustomerMappingCollection $customerMappingCollection,
        CountryFactory $countryFactory,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory,
        Configuration $configuration,
        SessionModel $sessionCollection,
        QuickbooksLogger $qbLogger
    ) {
        $this->orderItemTaxFactory = $orderItemTaxFactory;
        $this->_timezone = $timezone;
        $this->_orderRepository = $orderRepository;
        $this->_countryFactory = $countryFactory;
        $this->_customerMappingCollection = $customerMappingCollection;
        $this->_customerCollection = $customerCollection;
        $this->_orderTaxFactory = $orderTax;
        $this->_taxesMappingCollection = $taxesMappingCollection;
        $this->_taxCalculation = $taxCalculation;
        $this->_taxOrderItem = $taxOrderItem;
        parent::__construct($searchCriteria, $filterGroup, $filterBuilder, $queueCollection, $queueFactory, $configuration, $sessionCollection, $qbLogger);
    }

    /**
     * @param $orderId
     * @return AbstractSalesAddSendRequestXML
     * @throws LocalizedException
     */
    public function setOrder($orderId)
    {
        if (!isset($this->_orders[$orderId])) {
            $order = $this->_orderRepository->get($orderId);
            if (!$order->getEntityId()) {
                throw new LocalizedException(__('Not found order ID %1', $orderId));
            }
            $this->_orders[$orderId] = $order;
        }
        $this->order = $this->_orders[$orderId];

        return $this;
    }

    /**
     * @return Order
     * @throws LocalizedException
     */
    public function getOrder()
    {
        if (is_null($this->order) || empty($this->order)) {
            throw new LocalizedException(__('You have to set order to process first'));
        }

        return $this->order;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getCustomerXml()
    {
        $customer = $this->getOrder()->getCustomer();
        if ($this->getOrder()->getCustomerId() && $customer == null) {
            $customer = $this->_customerCollection->create()->addFieldToFilter('entity_id', $this->getOrder()->getCustomerId())->getFirstItem();
        }

        $xml = '';
        $customerMappingCollection = $this->_customerMappingCollection->create();
            // customer wasn't mapped
        if ($customer != null && $customer->getId()) {
            // get customer mapping data
            $customerMappingData = $customerMappingCollection->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_ID, $customer->getId())
                ->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_TYPE, CustomerMappingInterface::M2_ENTITY_TYPE_CUSTOMER)->getLastItem()->getData();
        } else {
            // get guest mapping data
            $customerMappingData = $customerMappingCollection->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_ID, $this->order->getId())
                ->addFieldToFilter(CustomerMappingInterface::M2_ENTITY_TYPE, CustomerMappingInterface::M2_ENTITY_TYPE_GUEST)->getLastItem()->getData();
        }

        if (!empty($customerMappingData)) {
            // this customer was mapped
            $xml .= BuildXML::buildXml(self::CUSTOMER_REF_LIST_ID, $customerMappingData[CustomerInterface::LIST_ID]);
        } else {
            $fullName = $this->getOrder()->getCustomerName();
            if ($this->_moduleConfig->isAutoInsertIdToLastName()) {
                $fullName .=  ' ' . $this->getOrder()->getId();
            }
            $xml .= BuildXML::buildXml(self::CUSTOMER_REF_FULL_NAME, $fullName);
        }

        return $xml;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getTxnDateXml()
    {
        return BuildXML::buildXml(
            self::XML_TXN_DATE,
            $this->_timezone->date($this->getOrder()->getCreatedAt())->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT)
        );
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getRefNumberXml()
    {
        return BuildXML::buildXml(self::XML_REF_NUMBER, $this->getOrder()->getIncrementId());
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getBillingAddressXml()
    {
        $billingAddress = $this->getOrder()->getBillingAddress();

        $xml = '';
        if ($billingAddress) {
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
     * @return string
     * @throws LocalizedException
     */
    public function getShippingAddressXml()
    {
        $shippingAddress = $this->getOrder()->getShippingAddress();

        $xml = '';
        if ($shippingAddress) {
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
     * @return string
     * @throws LocalizedException
     */
    protected function getShipMethodRefXml()
    {
        if (!empty($shipMethod = $this->getOrder()->getShippingMethod())) {
            $shipMethod = strtok($shipMethod, '_');
            return BuildXML::buildXml(self::XML_SHIP_METHOD_REF, $shipMethod);
        }

        return '';
    }

    /**
     * SO tax
     *
     * @return string
     * @throws LocalizedException
     */
    protected function getItemSalesTaxRefXml()
    {
        return BuildXML::buildXml(self::XML_ITEM_SALES_TAX_REF, self::XML_ZERO_RATE_DEFAULT);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getOrderTaxCode()
    {
        return $this->_orderTaxFactory->create()->getCollection()
            ->addFieldToFilter("order_id", $this->getOrder()->getId())
            ->getLastItem()->getCode();
    }

    /**
     * get Magento tax and retrun Quickbooks tax mapped
     *
     * @return string
     * @throws LocalizedException
     */
    protected function getQuickbookTax()
    {
        $orderId = $this->getOrder()->getId();
        if (!isset($this->qbTaxCode[$orderId])) {
            $magentoTaxCode = $this->getOrderTaxCode();

            if (empty($magentoTaxCode)) {
                $magentoTaxCode = $this->getTaxByValue();
            }

            $this->qbTaxCode[$orderId] = $this->getMappingTaxCode($magentoTaxCode);
        }

        return $this->qbTaxCode[$orderId];
    }

    /**
     * get tax of order item
     *
     * @param $orderId
     * @param $itemId
     * @return string
     */
    protected function getTaxItemById($orderId, $itemId)
    {
        if (empty($this->_orderItemAppliedTaxes)) {
            $this->_orderItemAppliedTaxes = $this->orderItemTaxFactory->create()->getTaxItemsByOrderId($orderId);
        }

        foreach ($this->_orderItemAppliedTaxes as $itemAppliedTax) {
            if (isset($itemAppliedTax['item_id']) && $itemAppliedTax['item_id'] == $itemId) {
                return $itemAppliedTax['code'];
            }
        }

        return '';
    }

    /**
     * @param null $code
     * @return string
     */
    protected function getSalesTaxCodeRefXml($code = null)
    {
        $version = $this->_moduleConfig->getConfigData(Configuration::XML_PATH_QUICKBOOKS_VERSION);

        if ($version == Version::VERSION_US) {
            $xml = BuildXML::buildXml(self::XML_LINE_ITEM_TAX, 'Non');
        } else {
            $xml = BuildXML::buildXml(self::XML_LINE_ITEM_TAX, 'E');
        }

        return $xml;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getTaxByValue()
    {
        $allMagentoTaxes = $this->_taxCalculation->create()->getCollection()->getItems();
        $magentoTaxCode = '';
        foreach ($allMagentoTaxes as $magentoTax) {
            $taxApplied = $magentoTax->getRate() * $this->getOrder()->getBaseSubtotal() / 100;
            if (in_array($this->getOrder()->getTaxAmount(), [$taxApplied, PriceFormat::formatPrice($taxApplied)])) {
                $magentoTaxCode = $magentoTax->getTaxCode();
                break;
            }
        }

        return $magentoTaxCode;
    }

    /**
     * @param null $taxCode
     * @return string
     */
    protected function getMappingTaxCode($taxCode = null)
    {
        $qbTaxCode = '';
        if ($taxCode) {
            $taxMapping = $this->_taxesMappingCollection->create()->filterByTaxInfo('magento_code', ['eq' => $taxCode]);
            $qbTaxCode = $taxMapping->getLastItem()->getData(TaxesInterface::TAX_CODE);
        }

        return $qbTaxCode;
    }

    /**
     * @return string
     */
    abstract protected function getLineItemXml();

    /**
     * @return string
     */
    abstract protected function getShippingLineXml();

    /**
     * @return string
     */
    abstract protected function getDiscountLineXml($discountAmount, $isTaxable = false);

    /**
     * @return string
     */
    protected function getShippingItemName()
    {
        return $this->_moduleConfig->getShippingItemName();
    }

    /**
     * @return string
     */
    protected function getDiscountItemName()
    {
        return $this->_moduleConfig->getDiscountItemName();
    }

    /**
     * @param $countryCode
     * @return string
     */
    private function getCountryNameByCode($countryCode)
    {
        return $this->_countryFactory->create()->loadByCode($countryCode)->getName();
    }

    protected function getTxnLineId()
    {

    }


    /**
     * Get sales representative xml.
     * @return string
     * @throws LocalizedException
     */
    protected function getSalesRepRefXml()
    {
        $amazonSalesRepresentativeName = $this->_moduleConfig->getAmazonRepresentativeName();
        $zoroSalesRepresentativeName = $this->_moduleConfig->getZoroRepresentativeName();
        $ebaySalesRepresentativeName = $this->_moduleConfig->getEbayRepresentativeName();
        $walmartSalesRepresentativeName = $this->_moduleConfig->getWalmartRepresentativeName();
        $xml = '';
        if ($amazonSalesRepresentativeName && $zoroSalesRepresentativeName) {
            $amazonCustomerGroupId = $this->_moduleConfig->getAmazonCustomerGroupMapping();
            $zoroCustomerGroupId = $this->_moduleConfig->getZoroCustomerMapping();
            $ebayCustomerGroupId = $this->_moduleConfig->getEbayCustomerMapping();
            $walmartCustomerGroupId = $this->_moduleConfig->getWalmartCustomerMapping();
            $customerGroupId = $this->getOrder()->getCustomerGroupId();
            if ($customerGroupId == $amazonCustomerGroupId) {
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_REPRESENTATIVE_FULL_NAME, $amazonSalesRepresentativeName);
            } elseif ($customerGroupId == $zoroCustomerGroupId) {
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_REPRESENTATIVE_FULL_NAME, $zoroSalesRepresentativeName);
            } elseif ($customerGroupId == $ebayCustomerGroupId) {
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_REPRESENTATIVE_FULL_NAME, $ebaySalesRepresentativeName);
            } elseif ($customerGroupId == $walmartCustomerGroupId) {
                $xml .= BuildXML::buildXml(self::XML_SALES_ORDER_REPRESENTATIVE_FULL_NAME, $walmartSalesRepresentativeName);
            }
        }

        return $xml;
    }

    /**
     * Add AMAZON to memo field
     * @return string
     * @throws LocalizedException
     */
    public function getMemoXml()
    {
        $xml = '';
        $customerGroupId = $this->getOrder()->getCustomerGroupId();
        $amazonCustomerGroupId = $this->_moduleConfig->getAmazonCustomerGroupMapping();
        $paymentMethod = $this->getOrder()->getPayment()->getMethodInstance()->getCode();
        $ebayCustomerGroupId = $this->_moduleConfig->getEbayCustomerMapping();
        $walmartCustomerGroupId = $this->_moduleConfig->getWalmartCustomerMapping();
        $ebaySalesRepresentativeName = $this->_moduleConfig->getEbayRepresentativeName();
        $walmartSalesRepresentativeName = $this->_moduleConfig->getWalmartRepresentativeName();
        if ($customerGroupId == $amazonCustomerGroupId) {
            $xml .= BuildXML::buildXml(self::XML_MEMO_FIELD, self::XML_MEMO_VALUE);
        } elseif (in_array($paymentMethod, ['zoropayment', 'cashondelivery1'])) {
            $xml .= BuildXML::buildXml(self::XML_MEMO_FIELD, self::XML_ZORO_MEMO_VALUE);
        } elseif ($customerGroupId == $ebayCustomerGroupId) {
            $xml .= BuildXML::buildXml(self::XML_MEMO_FIELD, self::XML_EBAY_MEMO_VALUE);
        } elseif ($customerGroupId == $walmartCustomerGroupId) {
            $xml .= BuildXML::buildXml(self::XML_MEMO_FIELD, self::XML_WALMART_MEMO_VALUE);
        }

        return $xml;
    }
}
