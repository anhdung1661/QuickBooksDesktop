<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Block\System\Config\Form\Field\Export;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Model\Config\Source\Queue\TypeQuery;

/**
 * Button create qwc file connect Company
 *
 * @package Magenest\QuickBooksDesktop\Block\System\Config\Form\Field\Export
 */
class QWCCompany extends QWCButton
{
    /**
     * @return array
     */
    protected function getButtonData()
    {
        $isConnectedCompany = $this->_companyCollection->addFieldToFilter(CompanyInterface::COMPANY_STATUS_FIELD, CompanyInterface::COMPANY_CONNECTED)->getData();

        if (count($isConnectedCompany)) {
            return [
                'id' => 'disconnect_qwc_company',
                'label' => __('Disconnect Company'),
            ];
        }
        return [
            'id' => 'create_qwc_company',
            'label' => __('Query Company'),
        ];
    }

    /**
     * @return int|TypeQuery
     */
    protected function getTypeQuery()
    {
        $isConnectedCompany = $this->_companyCollection->addFieldToFilter(CompanyInterface::COMPANY_STATUS_FIELD, CompanyInterface::COMPANY_CONNECTED)->getData();

        if (count($isConnectedCompany)) {
            return TypeQuery::QUERY_DISCONNECT;
        }

        return TypeQuery::QUERY_COMPANY;
    }

    /**
     * @return bool
     */
    protected function isDisabled()
    {
        $config = $this->_moduleConfig->getUserConnect();

        if (!$config) {
            return true;
        }

        return false;
    }
}
