<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model\Config\Source;

/**
 * Class Version
 * @package Magenest\QuickBooksDesktop\Model\Config\Source
 */
class Company implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magenest\QuickBooksDesktop\Model\ResourceModel\Company\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * Company constructor.
     * @param \Magenest\QuickBooksDesktop\Model\ResourceModel\Company\Collection $collection
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        \Magenest\QuickBooksDesktop\Model\ResourceModel\Company\Collection $collection,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->collection = $collection;
        $this->configWriter = $configWriter;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [['value' => '', 'label' => __('No company selected')]];
        $listCompany = $this->collection;
        foreach ($listCompany as $company) {
            $result[] = ['value' => $company->getCompanyName(), 'label' => __($company->getCompanyName())];
        }
        return $result;
    }
}
