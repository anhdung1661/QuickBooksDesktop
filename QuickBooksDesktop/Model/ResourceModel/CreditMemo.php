<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 30/09/2020 15:45
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\CreditMemoInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory;

/**
 * Class CreditMemo
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class CreditMemo extends AbstractQuickbooksEntity
{
    protected $companyCollection;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(CreditMemoInterface::TABLE_NAME, CreditMemoInterface::ENTITY_ID);
    }

    public function __construct(
        CollectionFactory $companyCollection,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->companyCollection = $companyCollection;
        parent::__construct($context, $connectionName);
    }

    /**
     * @param $creditMemosData
     * @return $this
     */
    public function saveCreditMemos($creditMemosData)
    {
        $this->saveQuickbooksEntity(CreditMemoInterface::TABLE_NAME, $creditMemosData);

        return $this;
    }

    /**
     * Get txn credit memo by credit id.
     * @param $creditMemoIds
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCreditMemoId($creditMemoIds)
    {
        $select = $this->getConnection()->select()
            ->from([$this->getMainTable()])
            ->where('magento_credit_memo_id IN (?)', $creditMemoIds)
            ->where('company_id = ?', $this->companyCollection->create()->getActiveCompany()->getCompanyId());
        return $this->getConnection()->fetchRow($select);
    }
}
