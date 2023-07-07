<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\QuickBooksDesktop\Model\Company as Model;

/**
 * Class User
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class Company extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CompanyInterface::TABLE_NAME, CompanyInterface::ENTITY_ID);
    }

    /**
     * @return int number of company updated
     * @throws \Exception
     */
    public function disableAllCompany()
    {
        try {
            return $this->getConnection()->update(
                $this->getMainTable(),
                [
                    CompanyInterface::COMPANY_STATUS_FIELD => CompanyInterface::COMPANY_DISCONNECTED
                ]
            );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * @param $companyName
     * @return int
     * @throws \Exception
     */
    public function saveCompany($companyName)
    {
        try {
            $connection = $this->getConnection();

            return $connection->insertOnDuplicate(
                $this->getMainTable(),
                [
                    CompanyInterface::COMPANY_NAME_FIELD => $companyName,
                    CompanyInterface::COMPANY_STATUS_FIELD => CompanyInterface::COMPANY_CONNECTED
                ], [
                    CompanyInterface::COMPANY_STATUS_FIELD => CompanyInterface::COMPANY_CONNECTED
                ]
            );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
    }
}
