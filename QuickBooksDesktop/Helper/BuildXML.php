<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 11/04/2020 11:12
 */

namespace Magenest\QuickBooksDesktop\Helper;

/**
 * Class BuildXML
 * @package Magenest\QuickBooksDesktop\Helper
 */
class BuildXML extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param string|array $tags // array of tag name if the length not define or ['tag_name' => array_tag_name, 'value_length' => number]
     * @param string $value
     * @return string with format <tag1><tag2>...value...</tag2></tag1>
     */
    public static function buildXml($tags, $value)
    {
        $tagLength = $tags['value_length'] ?? 0;
        $tags = $tags['tag_name'] ?? $tags;
        $tags = is_string($tags) ? [$tags] : $tags;

        $xml = '';
        if ($value !== '' && $value != null) {
            foreach ($tags as $tag) {
                $xml .= "<$tag>";
            }
            $xml .= self::validateXML($value, $tagLength);
            $tags = array_reverse($tags);
            foreach ($tags as $tag) {
                $xml .= "</$tag>";
            }
        }
        return $xml;
    }

    /**
     * limit string length, remove special characters
     *
     * @param $value
     * @param int $length
     * @return false|string
     */
    private static function validateXML($value, $length = 0)
    {
        $isSubStr = $length != 0 && strlen($value) > $length;

        if ($isSubStr) {
            $value = substr($value, 0, $length);
        }

        $value = self::convertVNtoEN($value);
        $value = self::convertRUStoEN($value);
        $value = self::replaceSpecialChracter($value);
        $value = self::removeSpecialCharacter($value);

        return $value;
    }

    /**
     * @param $str
     * @return string|string[]|null
     */
    private static function convertVNtoEN($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);

        return $str;
    }

    /**
     * @param $str
     * @return string|string[]
     */
    private static function convertRUStoEN($str)
    {
        $cyr = [
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
            'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
            'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'
        ];
        $lat = [
            'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p',
            'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sht', 'a', 'i', 'y', 'e', 'yu', 'ya',
            'A', 'B', 'V', 'G', 'D', 'E', 'Io', 'Zh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P',
            'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Sht', 'A', 'I', 'Y', 'e', 'Yu', 'Ya'
        ];
        return str_replace($cyr, $lat, $str);
    }

    /**
     * @param $str
     * @return string|string[]|null
     */
    private static function removeSpecialCharacter($str)
    {
        $str = preg_replace("/(„|†|‡|‰|‹|‘|’|“|”|™|›|¡|¢|£|¤|¥|¦|§|¨|©|ª|«|¬|­|®|¯|°|±|²|³|´|µ|¶|¸|⁰|)/", ' ', $str);
        $str = preg_replace("/(¹|º|»|¼|½|¾|¿|À|Á|Â|Ã|Ä|Å|Æ|Ç|È|É|Ê|Ë|Ì|Í|Î|Ï|Ð|Ñ|Ò|Ó|Ô|Õ|Ö|×|Ø|Ù|Ú|Û|Ü|Ý|Þ)/", ' ', $str);
        $str = preg_replace("/(ß|à|á|â|ã|ä|å|æ|ç|è|é|ê|ë|ì|í|î|ï|ð|ñ|ò|ó|ô|õ|ö|÷|ø|ù|ú|û|ü|ý|þ|ÿ|ƒ|Α|Β|Γ|Δ)/", ' ', $str);
        $str = preg_replace("/(Ε|Ζ|Η|Θ|Ι|Κ|Λ|Μ|Ν|Ξ|Ο|Π|Ρ|Σ|Τ|Υ|Φ|Χ|Ψ|Ω|α|β|γ|δ|ε|ζ|η|θ|ι|κ|λ|μ|ν|ξ|ο|π|ρ|ς)/", ' ', $str);
        $str = preg_replace("/(σ|τ|υ|φ|χ|ψ|ω|ϑ|ϒ|ϖ|•|…|′|″|‾|ℑ|℘|ℜ|ℵ|←|↑|→|↓|↔|↵|⇐|⇑|⇒|⇓|⇔|∀|∂|∃|∅|∇|∈|∉|∋)/", ' ', $str);
        $str = preg_replace("/(∏|∑|−|∗|√|∝|∞|∠|∧|∨|∩|∪|∫|∴|∼|≅|≈|≠|≡|≤|≥|⊂|⊃|⊄|⊆|⊇|⊕|⊗|⊥|⋅|⌈|⌉|⌊|⌋|〈|〉|◊|♠|♣|♥|♦)/", ' ', $str);
        $str = preg_replace("/[\s]+/mu", ' ', $str);

        return $str;
    }

    /**
     * @param $value
     * @return string|string[]
     */
    private static function replaceSpecialChracter($value)
    {
        return str_replace(
            ['&', '”', '\'', '<', '>', '"', '·', ':', '–', '‘', '’', '•', '^'],
            ['&#38;', '&#34;', '&#39;', '&lt;', '&gt;', '&#34;', '&#183;', '&#58;', '&#8211;', '&#8216;', '&#8217;', '&#8226;', '&#94;'],
            $value
        );
    }
}
