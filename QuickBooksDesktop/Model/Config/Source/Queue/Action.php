<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model\Config\Source\Queue;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;
/**
 * Class Action
 * @package Magenest\QuickBooksDesktop\Model\Config\Source
 */
class Action implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $listAction = [];
        foreach (\Magenest\QuickBooksDesktop\Model\Config\Source\Operation::getOptionArray() as $value => $label) {
            $listAction[] = ['value' => $value, 'label' => $label];
        }
        return $listAction;
    }
}
