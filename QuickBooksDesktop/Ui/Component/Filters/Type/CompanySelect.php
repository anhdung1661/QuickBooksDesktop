<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 06/10/2020 14:37
 */

namespace Magenest\QuickBooksDesktop\Ui\Component\Filters\Type;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\Collection;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\Ui\Component\Filters\Type\Select;

/**
 * Class CompanySelect
 * @package Magenest\QuickBooksDesktop\Ui\Component\Filters\Type
 */
class CompanySelect extends Select
{
    protected function applyFilter()
    {
        parent::applyFilter();

        if (!isset($this->filterData[QueueInterface::COMPANY_ID]) || $this->getName() != QueueInterface::COMPANY_ID) {

            $filter = $this->filterBuilder
                ->setField(\Magenest\QuickBooksDesktop\Ui\Component\Listing\DataProvider\QueueData::DEFAULT_FILTER_COMPANY)
                ->create();

            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }
}