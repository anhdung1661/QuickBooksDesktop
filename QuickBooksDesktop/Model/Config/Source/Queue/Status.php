<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model\Config\Source\Queue;

use Magenest\QuickBooksDesktop\Model\Config\Source\Status as StatusSource;

/**
 * Class Status
 * @package Magenest\QuickBooksDesktop\Model\Config\Source
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        foreach (StatusSource::getOptionArray() as $value => $label) {
            $options[] = ['label' => $label, 'value' => $value];
        }
        return $options;
    }
}
