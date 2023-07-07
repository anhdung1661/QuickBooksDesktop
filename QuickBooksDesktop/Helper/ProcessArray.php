<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 17/04/2020 15:04
 */

namespace Magenest\QuickBooksDesktop\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class ProcessArray
 * @package Magenest\QuickBooksDesktop\Helper
 */
class ProcessArray extends AbstractHelper
{
    /**
     * @param array $sourceData
     * @param array $valueCompare
     * @return array
     */
    public static function getRowByValue(array $sourceData, $valueCompare = [])
    {
        foreach ($sourceData as $row) {
            foreach ($valueCompare as $colKey => $colValue) {
                if (isset($row[$colKey]) && $row[$colKey] == $colValue) {
                    return $row;
                }
            }
        }
    }

    /**
     * get some columns value from an array 3 dimensional
     *
     * @param array $sourceData // array 3d
     * @param array $keys // the format of this array is $sourceKey => $colsKey
     * @param array $additionalData // this array will be merged into the child of $sourceData
     * @return array
     */
    public static function getColValueFromThreeDimensional(array $sourceData, array $keys = [], array $additionalData = [])
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        // if $keys doens't follow format $sourceKey => $colsKey
        $keysProcessed = [];
        foreach ($keys as $colsKey => $sourceKey) {
            if (is_string($colsKey)) {
                $keysProcessed[$colsKey] = $sourceKey;
                continue;
            }
            // when sourceKey is number
            $keysProcessed[$sourceKey] = $sourceKey;
        }

        $desArr = [];
        foreach ($sourceData as $sourceChild) {
            $chilArr = [];
            foreach ($keysProcessed as $colsKey => $sourceKey) {
                $chilArr[$colsKey] = $sourceChild[$sourceKey] ?? null;
            }
            $desArr[] = array_merge($chilArr, $additionalData);
        }
        unset($keysProcessed);

        return $desArr;
    }

    /**
     * Insert columns into array 3 dimensional
     * @param array $sourceData
     * @param array $additionalData
     * @return array
     */
    public static function insertColumnToThreeDimensional(array $sourceData, array $additionalData)
    {
        foreach ($additionalData as $key => $value) {
            foreach ($sourceData as $sourceDataKey => $sourceDataValue) {
                $sourceDataValue[$key] = $value;
                $sourceData[$sourceDataKey] = $sourceDataValue;
            }
        }
        return $sourceData;
    }

    /**
     * @param $arr1
     * @param $arr2
     * @param $key // the value of this key in 2 arrays is the same
     * @return array
     */
    public static function mergeArrayThreeD($arr1, $arr2, $key)
    {
        $mergedArr = [];

        foreach ($arr1 as $keyArr1 => $rowArr1) {

            foreach ($arr2 as $keyArr2 => $rowArr2) {
                if ($rowArr2[$key] == $rowArr1[$key]) {
                    $mergedArr[] = array_merge($arr1[$keyArr1], $arr2[$keyArr2]);
                }
            }
        }

        return $mergedArr;
    }
}