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
class Version implements \Magento\Framework\Option\ArrayInterface
{

    const VERSION_CANADA = 'CA';
    const VERSION_US = 'US';
    const VERSION_UK = 'UK';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VERSION_CANADA, 'label' => __('Canada') ],
            ['value' => self::VERSION_US, 'label' => __('United States')],
            ['value' => self::VERSION_UK, 'label' => __('United Kingdom')],
        ];
    }
}
