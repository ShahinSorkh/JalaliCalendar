<?php
/**
 * Under MIT License.
 */

namespace ShSo\Jalali;

/**
 * Convert english numbers to persian
 * and inverse, and convert arabic letters to persian.
 */
class Converter
{
    /**
     * convert english and arabic numbers to persian.
     *
     * @param string $content string for conversion
     *
     * @return string converted string
     */
    public static function numToPersian($content)
    {
        $temp = str_replace(['٤', '٥', '٦'], ['۴', '۵', '۶'], $content);

        return str_replace(
            ['1', '4', '2', '5', '3', '6', '7', '8', '9', '0'],
            ['۱', '۴', '۲', '۵', '۳', '۶', '۷', '۸', '۹', '۰'],
            $temp
        );
    }

    /**
     * convert persian and arabic numbers to english.
     *
     * @param string $content string for conversion
     *
     * @return string converted string
     */
    public static function numToEnglish($content)
    {
        $temp = self::numToPersian($content);

        return str_replace(
            ['۱', '۴', '۲', '۵', '۳', '۶', '۷', '۸', '۹', '۰'],
            ['1', '4', '2', '5', '3', '6', '7', '8', '9', '0'],
            $temp
        );
    }

    /**
     * convert arabic 'ک', 'ی', 'ه' and numbers to persian.
     *
     * @param string $content     string for conversion
     * @param bool   $persian_num convert numbers to persian?
     *
     * @return string converted string
     */
    public static function arabicToPersian($content, $persian_num = true)
    {
        $temp = self::numToPersian($content);
        $temp = str_replace(
            ['ي', 'ك', 'ة'],
            ['ی', 'ک', 'ه'],
            $temp
        );

        return $persian_num ? $temp : self::numToEnglish($content);
    }
}
