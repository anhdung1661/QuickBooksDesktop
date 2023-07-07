<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 07/04/2020 01:13
 */

namespace Magenest\QuickBooksDesktop\WebConnector\Connection;

use Elasticsearch\Common\Exceptions\RuntimeException;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Helper\QuickbooksLogger;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\Collection as QueueCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magenest\QuickBooksDesktop\Model\QueueFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\SessionConnect\CollectionFactory as SessionConnectCollection;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory as SessionConnectModelFactory;
use Magenest\QuickBooksDesktop\WebConnector\AbstractConnection;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\UserConnect;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Authenticate as AuthenticateResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ClientVersion as ClientVersionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\CloseConnection as CloseConnectionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\GetLastError as GetLastErrorResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddSendRequestXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ProductAddReceiveResponseXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Product\ProductAddSendRequestXML;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ItemOtherChargeAdd\SendRequestXML as ShippingItemOtherChargeAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ItemOtherChargeAdd\ReceiveResponseXML as ShippingItemOtherChargeAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd\SendRequestXML as ShipMethodsAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd\ReceiveResponseXML as ShipMethodsAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd\SendRequestXML as ItemDiscountAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd\ReceiveResponseXML as ItemDiscountAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd\SendRequestXML as PaymentMethodAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\PaymentMethod\PaymentMethodAdd\ReceiveResponseXML as PaymentMethodAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd\SendRequestXML as CustomerAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer\CustomerAdd\ReceiveResponseXML as CustomerAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd\SendRequestXML as SalesOrderAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\SalesOrderAdd\ReceiveResponseXML as SalesOrderAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd\SendRequestXML as InvoiceAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd\ReceiveResponseXML as InvoiceAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd\SendRequestXML as ReceivePaymentAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\ReceivePaymentAdd\ReceiveResponseXML as ReceivePaymentAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\CreditMemoAdd\SendRequestXML as CreditMemoAddSendRequest;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\CreditMemoAdd\ReceiveResponseXML as CreditMemoAddReceiveResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\ServerVersion as ServerVersionResponse;
use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceMod\SendRequestXml as InvoiceModSendRequest;
use Magento\Framework\Xml\Parser as ParserXml;

/**
 * Class SyncQueue
 * Synchronize all rows in queue status in queue table
 *
 * @package Magenest\QuickBooksDesktop\WebConnector\Connection
 */
class SyncQueue extends AbstractConnection
{
    /**
     * @var AbstractQueueAddSendRequestXML
     */
    protected $_sendRequestXML;

    /**
     * @var AbstractQueueAddReceiveResponseXML
     */
    protected $_receiveResponseXML;

    /**
     * @var QueueCollection
     */
    private $currentQueue;

    /**
     * @var QueueFactory
     */
    protected $_queueFactory;

    protected $_queueCollection;

    /**
     * @var ProductAddSendRequestXML
     */
    protected $_productSendRequestXML;

    /**
     * @var ProductAddReceiveResponseXML
     */
    protected $_productReceiveResponseXML;

    /**
     * @var ShippingItemOtherChargeAddSendRequest
     */
    protected $_shippingItemSendRequestXML;

    /**
     * @var ShippingItemOtherChargeAddReceiveResponse
     */
    protected $_shippingItemReceiveResponseXML;

    /**
     * @var ShipMethodsAddSendRequest
     */
    protected $_shippingMethodsSendRequestXML;

    /**
     * @var ShipMethodsAddReceiveResponse
     */
    protected $_shippingMethodsReceiveResponseXML;

    /**
     * @var ItemDiscountAddSendRequest
     */
    protected $_itemDiscountSendRequestXML;

    /**
     * @var ItemDiscountAddReceiveResponse
     */
    protected $_itemDiscountReceiveResponseXML;

    /**
     * @var PaymentMethodAddSendRequest
     */
    protected $_paymentMethodSendRequestXML;

    /**
     * @var PaymentMethodAddReceiveResponse
     */
    protected $_paymentMethodReceiveResponseXML;

    /**
     * @var CustomerAddSendRequest
     */
    protected $_customerAddSendRequestXML;

    /**
     * @var CustomerAddReceiveResponse
     */
    protected $_customerAddReceiveResponseXML;

    /**
     * @var SalesOrderAddSendRequest
     */
    protected $_salesOrderAddSendRequestXML;
    /**
     * @var SalesOrderAddReceiveResponse
     */
    protected $_salesOrderAddReceiveResponseXML;

    /**
     * @var InvoiceAddSendRequest
     */
    protected $_invoiceAddSendRequestXML;

    /**
     * @var InvoiceAddReceiveResponse
     */
    protected $_invoiceAddReceiveResponseXML;

    /**
     * @var ReceivePaymentAddSendRequest
     */
    protected $_receivePaymentAddSendRequestXML;

    /**
     * @var ReceivePaymentAddReceiveResponse
     */
    protected $_receivePaymentAddReceiveResponseXML;

    /**
     * @var CreditMemoAddSendRequest
     */
    protected $_creditMemoAddSendRequestXML;

    /**
     * @var CreditMemoAddReceiveResponse
     */
    protected $_creditMemoAddReceiveResponseXML;

    /**
     * @var InvoiceModSendRequest
     */
    protected $_invoiceModSendRequest;

    /**
     * @var ParserXml
     */
    protected $_parserXml;

    /**
     * SyncQueue constructor.
     * @param QueueCollectionFactory $queueCollection
     * @param QueueFactory $queueFactory
     * @param CreditMemoAddSendRequest $creditMemoAddSendRequest
     * @param CreditMemoAddReceiveResponse $creditMemoAddReceiveResponse
     * @param ReceivePaymentAddSendRequest $receivePaymentAddSendRequest
     * @param ReceivePaymentAddReceiveResponse $receivePaymentAddReceiveResponse
     * @param InvoiceAddSendRequest $invoiceAddSendRequest
     * @param InvoiceAddReceiveResponse $invoiceAddReceiveResponse
     * @param SalesOrderAddSendRequest $salesOrderAddSendRequest
     * @param SalesOrderAddReceiveResponse $salesOrderAddReceiveResponse
     * @param CustomerAddSendRequest $customerAddSendRequestXML
     * @param CustomerAddReceiveResponse $customerAddReceiveResponseXML
     * @param PaymentMethodAddSendRequest $paymentMethodSendRequestXML
     * @param PaymentMethodAddReceiveResponse $paymentMethodReceiveResponseXML
     * @param ItemDiscountAddSendRequest $itemDiscountSendRequestXML
     * @param ItemDiscountAddReceiveResponse $itemDiscountReceiveResponseXML
     * @param ShipMethodsAddSendRequest $shipMethodsAddSendRequest
     * @param ShipMethodsAddReceiveResponse $shipMethodsReceiveResponseXML
     * @param ShippingItemOtherChargeAddSendRequest $shippingItemSendRequestXML
     * @param ShippingItemOtherChargeAddReceiveResponse $shippingItemReceiveResponseXML
     * @param ProductAddSendRequestXML $productAddSendRequestXML
     * @param ProductAddReceiveResponseXML $productAddReceiveResponseXML
     * @param ServerVersionResponse $serverVersion
     * @param ClientVersionResponse $clientVersion
     * @param AuthenticateResponse $authenticate
     * @param GetLastErrorResponse $getLastError
     * @param CloseConnectionResponse $closeConnection
     * @param SessionConnectModelFactory $sessionConnectFactory
     * @param SessionConnectCollection $sessionConnectCollection
     * @param QuickbooksLogger $quickbooksLogger
     * @param InvoiceModSendRequest $_invoiceModSendRequest
     * @param ParserXml $_parserXml
     */
    public function __construct(
        QueueCollectionFactory $queueCollection,
        QueueFactory $queueFactory,
        CreditMemoAddSendRequest $creditMemoAddSendRequest,
        CreditMemoAddReceiveResponse $creditMemoAddReceiveResponse,
        ReceivePaymentAddSendRequest $receivePaymentAddSendRequest,
        ReceivePaymentAddReceiveResponse $receivePaymentAddReceiveResponse,
        InvoiceAddSendRequest $invoiceAddSendRequest,
        InvoiceAddReceiveResponse $invoiceAddReceiveResponse,
        SalesOrderAddSendRequest $salesOrderAddSendRequest,
        SalesOrderAddReceiveResponse $salesOrderAddReceiveResponse,
        CustomerAddSendRequest $customerAddSendRequestXML,
        CustomerAddReceiveResponse $customerAddReceiveResponseXML,
        PaymentMethodAddSendRequest $paymentMethodSendRequestXML,
        PaymentMethodAddReceiveResponse $paymentMethodReceiveResponseXML,
        ItemDiscountAddSendRequest $itemDiscountSendRequestXML,
        ItemDiscountAddReceiveResponse $itemDiscountReceiveResponseXML,
        ShipMethodsAddSendRequest $shipMethodsAddSendRequest,
        ShipMethodsAddReceiveResponse $shipMethodsReceiveResponseXML,
        ShippingItemOtherChargeAddSendRequest $shippingItemSendRequestXML,
        ShippingItemOtherChargeAddReceiveResponse $shippingItemReceiveResponseXML,
        ProductAddSendRequestXML $productAddSendRequestXML,
        ProductAddReceiveResponseXML $productAddReceiveResponseXML,
        ServerVersionResponse $serverVersion,
        ClientVersionResponse $clientVersion,
        AuthenticateResponse $authenticate,
        GetLastErrorResponse $getLastError,
        CloseConnectionResponse $closeConnection,
        SessionConnectModelFactory $sessionConnectFactory,
        SessionConnectCollection $sessionConnectCollection,
        QuickbooksLogger $quickbooksLogger,
        InvoiceModSendRequest $_invoiceModSendRequest,
        ParserXml $_parserXml
    ) {
        $this->_queueCollection = $queueCollection;
        $this->_queueFactory = $queueFactory;
        $this->_productSendRequestXML = $productAddSendRequestXML;
        $this->_productReceiveResponseXML = $productAddReceiveResponseXML;
        $this->_shippingItemSendRequestXML = $shippingItemSendRequestXML;
        $this->_shippingItemReceiveResponseXML = $shippingItemReceiveResponseXML;
        $this->_shippingMethodsSendRequestXML = $shipMethodsAddSendRequest;
        $this->_shippingMethodsReceiveResponseXML = $shipMethodsReceiveResponseXML;
        $this->_itemDiscountSendRequestXML = $itemDiscountSendRequestXML;
        $this->_itemDiscountReceiveResponseXML = $itemDiscountReceiveResponseXML;
        $this->_paymentMethodSendRequestXML = $paymentMethodSendRequestXML;
        $this->_paymentMethodReceiveResponseXML = $paymentMethodReceiveResponseXML;
        $this->_customerAddSendRequestXML = $customerAddSendRequestXML;
        $this->_customerAddReceiveResponseXML = $customerAddReceiveResponseXML;
        $this->_salesOrderAddSendRequestXML = $salesOrderAddSendRequest;
        $this->_salesOrderAddReceiveResponseXML = $salesOrderAddReceiveResponse;
        $this->_invoiceAddSendRequestXML = $invoiceAddSendRequest;
        $this->_invoiceAddReceiveResponseXML = $invoiceAddReceiveResponse;
        $this->_receivePaymentAddSendRequestXML = $receivePaymentAddSendRequest;
        $this->_receivePaymentAddReceiveResponseXML = $receivePaymentAddReceiveResponse;
        $this->_creditMemoAddSendRequestXML = $creditMemoAddSendRequest;
        $this->_creditMemoAddReceiveResponseXML = $creditMemoAddReceiveResponse;
        $this->_invoiceModSendRequest = $_invoiceModSendRequest;
        $this->_parserXml = $_parserXml;

        parent::__construct($serverVersion, $clientVersion, $authenticate, $getLastError, $closeConnection, $sessionConnectFactory, $sessionConnectCollection, $quickbooksLogger);
    }

    /**
     * Step 3: Check user connect between M2 and QBD
     * WebMethod: authenticate() has been called by QB Web connector
     *
     * @param UserConnect $userConnect
     * @return AuthenticateResponse
     */
    public function authenticate($userConnect)
    {
        try {
            // check if need exchange data
            $noDataExchange = count($this->getQueueData()->getData()) ? false : true;
            $this->_authentication->processAuth($userConnect, $noDataExchange);
        } catch (\Exception $exception) {
            $this->_quickbooksLogger->critical('Error while Authenticating: ' . $exception->getMessage());
        }

        return $this->_authentication;
    }

    /**
     * @inheritDoc
     */
    protected function getSendRequestXML()
    {
        if ($this->_sendRequestXML == null) {
            $currentQueueData = $this->getQueueData(QueueInterface::STATUS_QUEUE);
            switch ($currentQueueData->getData(QueueInterface::MAGENTO_ENTITY_TYPE)) {
                case QueueInterface::TYPE_PRODUCT:
                    $this->_sendRequestXML = $this->_productSendRequestXML;
                    break;
                case QueueInterface::TYPE_ITEM_SHIPPING:
                    $this->_sendRequestXML = $this->_shippingItemSendRequestXML;
                    break;
                case QueueInterface::TYPE_SHIPPING_METHOD:
                    $this->_sendRequestXML = $this->_shippingMethodsSendRequestXML;
                    break;
                case QueueInterface::TYPE_ITEM_DISCOUNT:
                    $this->_sendRequestXML = $this->_itemDiscountSendRequestXML;
                    break;
                case QueueInterface::TYPE_PAYMENT_METHOD:
                    $this->_sendRequestXML = $this->_paymentMethodSendRequestXML;
                    break;
                case QueueInterface::TYPE_CUSTOMER:
                case QueueInterface::TYPE_GUEST:
                    $this->_sendRequestXML = $this->_customerAddSendRequestXML;
                    break;
                case QueueInterface::TYPE_SALES_ORDER:
                    $this->_sendRequestXML = $this->_salesOrderAddSendRequestXML;
                    break;
                case QueueInterface::TYPE_INVOICE:
                    $this->_sendRequestXML = $this->_invoiceAddSendRequestXML;
                    break;
                case QueueInterface::TYPE_EDIT_INVOICE:
                    $this->_sendRequestXML = $this->_invoiceModSendRequest;
                    break;
                case QueueInterface::TYPE_RECEIVE_PAYMENT:
                    $this->_sendRequestXML = $this->_receivePaymentAddSendRequestXML;
                    break;
                case QueueInterface::TYPE_CREDIT_MEMO:
                    $this->_sendRequestXML = $this->_creditMemoAddSendRequestXML;
                    break;
                default:
                    throw new RuntimeException(__('Error while preparing the send request XML!'));
            }
        }

        return $this->_sendRequestXML;
    }

    /**
     * @inheritDoc
     */
    protected function getReceiveResponseXML($response = null)
    {
        if ($this->_receiveResponseXML == null) {
            if (!empty($response)) {
                $currentQueueData = $this->getCurrentType($response);
            } else {
                $currentQueueData = $this->getQueueData(QueueInterface::STATUS_PROCESSING);
            }
            switch ($currentQueueData->getData(QueueInterface::MAGENTO_ENTITY_TYPE)) {
                case QueueInterface::TYPE_PRODUCT:
                    $this->_receiveResponseXML = $this->_productReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_ITEM_SHIPPING:
                    $this->_receiveResponseXML = $this->_shippingItemReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_SHIPPING_METHOD:
                    $this->_receiveResponseXML = $this->_shippingMethodsReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_ITEM_DISCOUNT:
                    $this->_receiveResponseXML = $this->_itemDiscountReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_PAYMENT_METHOD:
                    $this->_receiveResponseXML = $this->_paymentMethodReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_CUSTOMER:
                case QueueInterface::TYPE_GUEST:
                    $this->_receiveResponseXML = $this->_customerAddReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_SALES_ORDER:
                    $this->_receiveResponseXML = $this->_salesOrderAddReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_INVOICE:
                case QueueInterface::TYPE_EDIT_INVOICE:
                    $this->_receiveResponseXML = $this->_invoiceAddReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_RECEIVE_PAYMENT:
                    $this->_receiveResponseXML = $this->_receivePaymentAddReceiveResponseXML;
                    break;
                case QueueInterface::TYPE_CREDIT_MEMO:
                    $this->_receiveResponseXML = $this->_creditMemoAddReceiveResponseXML;
                    break;
                default:
                    throw new RuntimeException(__('Error while preparing the receive response XML!'));
            }
        }

        return $this->_receiveResponseXML;
    }

    /**
     * @param $xmlData
     * @return QueueCollection|\Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentType($xmlData)
    {
        $this->_parserXml->loadXML($xmlData);
        $listItemTypesRs = $this->_parserXml->xmlToArray()['QBXML']['QBXMLMsgsRs'];
        if (!$listItemTypesRs) {
            $listItemTypesRs = [];
        }
        foreach ($listItemTypesRs as $keyItemTypeRs => $itemTypeRs) {
            if (isset($itemTypeRs['_attribute'])) {
                $requestId = $itemTypeRs['_attribute']['requestID'];
                if ($requestId) {
                    $this->currentQueue = $this->_queueCollection->create()
                        ->addFieldToFilter(QueueInterface::ENTITY_ID, $requestId)
                        ->addFieldToSelect(QueueInterface::MAGENTO_ENTITY_TYPE)
                        ->getFirstItem();
                }
            } else {
                foreach ($itemTypeRs as $itemTypeR) {
                    if (isset($itemTypeR['_attribute'])) {
                        $requestId = $itemTypeR['_attribute']['requestID'];
                        if ($requestId) {
                            $this->currentQueue = $this->_queueCollection->create()
                                ->addFieldToFilter(QueueInterface::ENTITY_ID, $requestId)
                                ->addFieldToSelect(QueueInterface::MAGENTO_ENTITY_TYPE)
                                ->getFirstItem();
                            break;
                        }
                    }
                }
            }
        }

        return $this->currentQueue;
    }

    /**
     * @inheritDoc
     */
    protected function otherActionWhenError($sessionToken, $errorMsg)
    {
        $this->_queueFactory->create()->updateStatusOfProcessingQueue(QueueInterface::STATUS_FAIL, $errorMsg);
    }

    /**
     * get the current queue that is processing
     * @param int $status
     * @return QueueCollection
     */
    protected function getQueueData($status = QueueInterface::STATUS_QUEUE)
    {
        if (!$this->currentQueue) {
            $this->currentQueue = $this->_queueCollection->create()
                ->addFieldToFilter(QueueInterface::STATUS, $status)
                ->setOrder(QueueInterface::PRIORITY, QueueCollection::SORT_ORDER_ASC)
                ->setOrder(QueueInterface::ENTITY_ID, QueueCollection::SORT_ORDER_ASC)
                ->addFieldToSelect(QueueInterface::MAGENTO_ENTITY_TYPE)
                ->getFirstItem();
        }

        return $this->currentQueue;
    }
}
