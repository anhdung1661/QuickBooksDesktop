<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 06/10/2020 13:25
 */

namespace Magenest\QuickBooksDesktop\Model\Config\Source\Queue;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory as CompanyCollection;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class ListCompany
 * @package Magenest\QuickBooksDesktop\Model\Config\Source\Queue
 */
class ListCompany implements ArrayInterface
{
    /**
     * @var CompanyCollection
     */
    protected $_companyCollection;

    /**
     * ListCompany constructor.
     * @param CompanyCollection $companyCollection
     */
    public function __construct(
        CompanyCollection $companyCollection
    ) {
        $this->_companyCollection = $companyCollection;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $listCompany = $this->_companyCollection->create();

        $companies = [];
        foreach ($listCompany as $company) {
            $inActive = $company->getData(CompanyInterface::COMPANY_STATUS_FIELD) == CompanyInterface::COMPANY_CONNECTED ? __(' (is connecting)') : '';
            $companies[] = [
                'value' => $company->getData(CompanyInterface::ENTITY_ID),
                'label' => $company->getData(CompanyInterface::COMPANY_NAME_FIELD) . $inActive
            ];
        }

        return $companies;
    }
}