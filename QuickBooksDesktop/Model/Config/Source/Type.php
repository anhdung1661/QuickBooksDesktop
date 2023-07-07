<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;

/**
 * Class Type
 * @package Magenest\QuickBooksDesktop\Model\Config\Source
 */
class Type extends AbstractSource
{
    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            QueueInterface::TYPE_PAYMENT_METHOD => __('PaymentMethod'),
            QueueInterface::TYPE_SHIPPING_METHOD => __('ShippingMethod'),
            QueueInterface::TYPE_CUSTOMER => __('Customer'),
            QueueInterface::TYPE_GUEST => __('Guest'),
            QueueInterface::TYPE_PRODUCT => __('Product'),
            QueueInterface::TYPE_SALES_ORDER => __('SalesOrder'),
            QueueInterface::TYPE_INVOICE => __('Invoice'),
            QueueInterface::TYPE_RECEIVE_PAYMENT => __('ReceivePayment'),
            QueueInterface::TYPE_CREDIT_MEMO => __('CreditMemo'),
            QueueInterface::TYPE_ITEM_SHIPPING => __('Shipping Item'),
            QueueInterface::TYPE_ITEM_DISCOUNT => __('Discount Item'),
            QueueInterface::TYPE_EDIT_INVOICE => __('ApplyCredit')
        ];
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        return self::getOptionArray()[$optionId] ?? null;
    }
}
