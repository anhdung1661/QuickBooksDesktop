<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 11/04/2020 13:39
 */

namespace Magenest\QuickBooksDesktop\Helper;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class Configuration
 * @package Magenest\QuickBooksDesktop\Helper
 */
class Configuration extends AbstractHelper
{
    const MODULE_NAME = 'Magenest_QuickBooksDesktop';
    const XML_PATH_QUICKBOOKS_VERSION = 'qbdesktop/qbd_setting/quickbook_version';
    const XML_PATH_USER_NAME = 'qbdesktop/qbd_setting/user_name';
    const XML_PATH_AUTO_RUN_EVERY_MIN = 'qbdesktop/qbd_setting/auto_run_every_min';

    const XML_PATH_MAX_RECORDS_PER_TAX_QUERY_REQUEST = 'qbdesktop/qbd_mapping_settings/qbd_mapping_tax/number_tax';

    const XML_PATH_SYNC_CUSTOMER_DATE_FROM = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_customer/date_customer';

    const XML_PATH_SYNC_CUSTOMER_AUTO_INSERT_ID_TO_LASTNAME = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_customer/insert_id_to_lastname';

    const XML_PATH_SYNC_ORDER_DATE_FROM = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/date_sales_order';

    const XML_PATH_SYNC_INVOICE_DATE_FROM = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_invoice/date_invoice';

    const XML_PATH_SYNC_CREDIT_MEMO_DATE_FROM = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_credit_memo/date_credit_memo';

    const XML_PATH_MAX_RECORDS_PER_ADD_REQUEST = 'qbdesktop/qbd_synchronize_settings/max_record_per_request';

    const XML_PATH_ENABLE_DEBUG_MODE = 'qbdesktop/qbd_debug_settings/enable_debug_mode';

    /**
     * Sales Settings
     */
    const XML_PATH_DISCOUNT_ITEM_NAME = 'qbdesktop/qbd_synchronize_settings/qbd_sales_settings/discount_item_name';

    const XML_PATH_SHIPPING_ITEM_NAME = 'qbdesktop/qbd_synchronize_settings/qbd_sales_settings/shipping_item_name';

    const XML_PATH_ADJUSTMENT_ITEM_NAME = 'qbdesktop/qbd_synchronize_settings/qbd_sales_settings/adjustment_item_name';

    const XML_PATH_ALLOW_SYNC_ORDER = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/enable_sync_order';

    const XML_PATH_ALLOW_SYNC_INVOICE = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_invoice/enable_sync_invoice';

    const XML_PATH_ALLOW_SYNC_CREDIT_MEMO = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_credit_memo/enable_sync_credit_memo';

    const XML_PATH_AMAZON_REPRESENTATIVE = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/amazon_representative';

    const XML_PATH_AMAZON_CUSTOMER_GROUP = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/amazon_customer_group_map';

    const XML_PATH_ZORO_REPRESENTATIVE = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/zoro_representative';

    const XML_PATH_ZORO_CUSTOMER_GROUP = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/zoro_customer_group_map';

    const XML_PATH_EBAY_REPRESENTATIVE = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/ebay_representative';

    const XML_PATH_EBAY_CUSTOMER_GROUP = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/ebay_customer_group_map';

    const XML_PATH_WALMART_REPRESENTATIVE = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/walmart_representative';

    const XML_PATH_WALMART_CUSTOMER_GROUP = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_order/walmart_customer_group_map';

    /**
     * Product configuration
     */
    const XML_PATH_SYNC_PRODUCT_DATE_FROM = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_product/date_product';

    const XML_PATH_PRODUCT_INCOME_ACCOUNT = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_product/income';
    const XML_PATH_PRODUCT_EXPENSE_ACCOUNT = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_product/expense';
    const XML_PATH_PRODUCT_COGS_ACCOUNT = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_product/cogs';
    const XML_PATH_PRODUCT_ASSET_ACCOUNT = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_product/asset';
    const XML_PATH_OTHER_INCOME_ACCOUNT = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_product/other_income';
    const XML_PATH_DISCOUNT_INCOME_ACCOUNT = 'qbdesktop/qbd_synchronize_settings/qbd_synchronize_product/discount_income';

    const PATH_COMPANY = "qbdesktop/qbd_setting/quickbook_company";
    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var TypeListInterface
     */
    protected $typeList;

    /**
     * @param TypeListInterface $typeList
     * @param Context $context
     * @param WriterInterface $configWriter
     */
    public function __construct(TypeListInterface $typeList, Context $context, WriterInterface $configWriter)
    {
        parent::__construct($context);
        $this->configWriter = $configWriter;
        $this->typeList = $typeList;
    }

    /**
     * @return string
     */
    public function getUserConnect()
    {
        return $this->getConfigData(self::XML_PATH_USER_NAME);
    }

    /**
     * @return int
     */
    public function getTimeAutoRun()
    {
        return $this->getConfigData(self::XML_PATH_AUTO_RUN_EVERY_MIN);
    }

    /**
     * @return \DateTime
     */
    public function getCustomerDateFrom()
    {
        return $this->getConfigData(self::XML_PATH_SYNC_CUSTOMER_DATE_FROM);
    }

    /**
     * @return \DateTime
     */
    public function getProductDateFrom()
    {
        return $this->getConfigData(self::XML_PATH_SYNC_PRODUCT_DATE_FROM);
    }

    /**
     * return list_id in Quickbooks
     * @return string
     */
    public function getIncomeAccount()
    {
        return $this->getConfigData(self::XML_PATH_PRODUCT_INCOME_ACCOUNT);
    }

    /**
     * return list_id in Quickbooks
     * @return string
     */
    public function getExpenseAccount()
    {
        return $this->getConfigData(self::XML_PATH_PRODUCT_EXPENSE_ACCOUNT);
    }

    /**
     * return list_id in Quickbooks
     * @return string
     */
    public function getCOGSAccount()
    {
        return $this->getConfigData(self::XML_PATH_PRODUCT_COGS_ACCOUNT);
    }

    /**
     * return list_id in Quickbooks
     * @return string
     */
    public function getAssetAccount()
    {
        return $this->getConfigData(self::XML_PATH_PRODUCT_ASSET_ACCOUNT);
    }

    /**
     * @return string
     */
    public function getOtherChargeIncomeAccount()
    {
        return $this->getConfigData(self::XML_PATH_OTHER_INCOME_ACCOUNT);
    }

    /**
     * @return string
     */
    public function getDiscountIncomeAccount()
    {
        return $this->getConfigData(self::XML_PATH_DISCOUNT_INCOME_ACCOUNT);
    }

    /**
     * @return \DateTime
     */
    public function getOrderDateFrom()
    {
        return $this->getConfigData(self::XML_PATH_SYNC_ORDER_DATE_FROM);
    }

    /**
     * @return \DateTime
     */
    public function getInvoiceDateFrom()
    {
        return $this->getConfigData(self::XML_PATH_SYNC_INVOICE_DATE_FROM);
    }

    /**
     * @return \DateTime
     */
    public function getCreditMemoDateFrom()
    {
        return $this->getConfigData(self::XML_PATH_SYNC_CREDIT_MEMO_DATE_FROM);
    }

    /**
     * @return bool
     */
    public function allowSyncOrder()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ALLOW_SYNC_ORDER, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return bool
     */
    public function allowSyncInvoice()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ALLOW_SYNC_INVOICE, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return bool
     */
    public function allowSyncCreditMemo()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ALLOW_SYNC_CREDIT_MEMO, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return bool
     */
    public function isEnableDebugMode()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_DEBUG_MODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getMaxRecordsPerAddRequest()
    {
        $maxRequest = (int)$this->getConfigData(self::XML_PATH_MAX_RECORDS_PER_ADD_REQUEST);
        return (empty($maxRequest) || $maxRequest < 0) ? 0 : $maxRequest;
    }

    /**
     * get number of records that will return when querying tax in each request
     *
     * @return int
     */
    public function getNumRecordsPerTaxRequest()
    {
        $maxRequest = (int)$this->getConfigData(self::XML_PATH_MAX_RECORDS_PER_TAX_QUERY_REQUEST);
        return (empty($maxRequest) || $maxRequest < 0) ? 1 : $maxRequest;
    }

    /**
     * @return string
     */
    public function getDiscountItemName()
    {
        return $this->getConfigData(self::XML_PATH_DISCOUNT_ITEM_NAME);
    }

    /**
     * @return string
     */
    public function getShippingItemName()
    {
        return $this->getConfigData(self::XML_PATH_SHIPPING_ITEM_NAME);
    }

    /**
     * @return string
     */
    public function getAdjustmentItemName()
    {
        return $this->getConfigData(self::XML_PATH_ADJUSTMENT_ITEM_NAME);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigData($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_WEBSITES);
    }

    /**
     * @param $path
     * @param $value
     */
    public function setConfigData($path, $value)
    {
        $this->configWriter->save($path, $value);
        $this->typeList->invalidate(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
    }

    /**
     * Check whether is auto insert id to lastname of customer.
     * @return mixed
     */
    public function isAutoInsertIdToLastName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SYNC_CUSTOMER_AUTO_INSERT_ID_TO_LASTNAME);
    }

    /**
     * Get amazon sales representative name.
     * @return mixed
     */
    public function getAmazonRepresentativeName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AMAZON_REPRESENTATIVE);
    }

    /**
     * Get zoro sales representative name.
     * @return mixed
     */
    public function getZoroRepresentativeName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ZORO_REPRESENTATIVE);
    }

    /**
     * Get amazon customer group.
     * @return mixed
     */
    public function getAmazonCustomerGroupMapping()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AMAZON_CUSTOMER_GROUP);
    }

    /**
     * Get zoro customer group.
     * @return mixed
     */
    public function getZoroCustomerMapping()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ZORO_CUSTOMER_GROUP);
    }

    /**
     * @return mixed
     */
    public function getEbayRepresentativeName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EBAY_REPRESENTATIVE);
    }

    /**
     * @return mixed
     */
    public function getEbayCustomerMapping()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EBAY_CUSTOMER_GROUP);
    }

    /**
     * @return mixed
     */
    public function getWalmartRepresentativeName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WALMART_REPRESENTATIVE);
    }

    /**
     * @return mixed
     */
    public function getWalmartCustomerMapping()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WALMART_CUSTOMER_GROUP);
    }
}
