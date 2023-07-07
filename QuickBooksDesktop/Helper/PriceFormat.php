<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * qbd-upgrade extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package qbd-upgrade
 * @time: 30/09/2020 08:42
 */

namespace Magenest\QuickBooksDesktop\Helper;

/**
 * Class PriceFormat
 * @package Magenest\QuickBooksDesktop\Helper
 */
class PriceFormat
{
    /**
     * @param float $priceValue
     * @return float
     */
    public static function formatPrice($priceValue = 0.0)
    {
        return number_format($priceValue, 2, '.', '');
    }
}