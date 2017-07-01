<?php
/**
 * Under MIT License
 */

namespace Jalali;

/**
 * Convert english numbers to persian
 * and inverse, and convert arabic letters to persian
 */
class Convertor {

    /**
     * convert english and arabic numbers to persian
     *
     * @param  string  $content  string for convertion
     *
     * @return string  converted string
     */
    public static function numToPersian($content) {
        $temp = str_replace(array('٤', '٥', '٦'), array('۴', '۵', '۶'), $content);
        return str_replace(
            array('1', '4', '2', '5', '3', '6', '7', '8', '9', '0'),
            array('۱', '۴', '۲', '۵', '۳', '۶', '۷', '۸', '۹', '۰'),
            $temp
        );
    }

    /**
     * convert persian and arabic numbers to english
     *
     * @param  string  $content  string for convertion
     *
     * @return string  converted string
     */
    public static function numToEnglish($content) {
        $temp = self::numToPersian($content);
    return str_replace(
            array('۱', '۴', '۲', '۵', '۳', '۶', '۷', '۸', '۹', '۰'),
            array('1', '4', '2', '5', '3', '6', '7', '8', '9', '0'),
            $temp
        );
    }

    /**
     * convert arabic 'ک', 'ی', 'ه' and numbers to persian.
     *
     * @param  string  $content  string for convertion
     * @param  bool  $persian_num  convert numbers to persian?
     *
     * @return string  converted string
     */
    public static function arabicToPersian($content, $persian_num = true) {
        $temp = self::numToPersian($content);
        $temp = str_replace(
            array('ي', 'ك', 'ة'),
            array('ی', 'ک', 'ه'),
            $temp
        );
        return $persian_num ? $temp:self::numToEnglish($content);
    }

}
