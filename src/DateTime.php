<?php
/*
 * 2016-2017 ShahinSorkh <sorkh.shahin@hotmail.com>
 * 2014 Zakrot Web Solutions
 * 2009-2013 Vahid Sohrablou (IranPHP.org)
 * 2000 Roozbeh Pournader and Mohammad Tou'si
 *
 * Under MIT license
 *
 * This has gotten from jalali wordpress plugin
 *
 */

namespace ShSo\Jalali;

/**
 * Jalali equivalent of native php date/time functions.
 */
class DateTime
{
    private static $_famonth_name = [ // keys are in the right order
        '', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];
    private static $_enmonth_name = [
        '', 'Farvardin', 'Ordibehesht', 'Khordad', 'Tir', 'Mordad', 'Shahrivar',
        'Mehr', 'Aban', 'Azar', 'Dey', 'Bahman', 'Esfand',
    ];
    private static $_famonth_short_name = [ // keys are in the right order
        '', 'فرو', 'ارد', 'خرد', 'تیر', 'مرد', 'شهر',
        'مهر', 'آبا', 'آذر', 'دی', 'بهم', 'اسفن',
    ];
    private static $_enmonth_short_name = [
        '', 'Far', 'Ord', 'Kho', 'Tir', 'Mor', 'Sha',
        'Meh', 'Aba', 'Aza', 'Dey', 'Bah', 'Esf',
    ];
    private static $_jdays_in_month = [0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    private static $_gdays_in_month = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    private static $_faweek_short_name = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
    private static $_faweek_name = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه'];
    private static $_enweek_short_name = ['Sat', 'Sun', 'Mon', 'Teu', 'Wed', 'Thu', 'Fri'];
    private static $_enweek_name = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    /**
     * The format of the outputted date string (jalali equivalent of php date() function).
     *
     * @link http://php.net/en/manual/function.date.php
     *
     * @param string     $format    For example 'Y-m-d H:i:s'
     * @param int|string $timestamp [optional] Null for current time<br />
     *                              formatted datetime to use as strtotime arg<br />
     *                              int for custom unix timestamp
     * @param array      $opt       [optional] array(<br />
     *                              'timezone' => Passed directly to 'new DateTimeZone($timezone)',
     *                              you can use 'local' to avoid overriding (Default 'local'),<br />
     *                              'in_persian' => Convert numbers to persian? (Default true)<br />)
     *
     * @return string Returns a formatted datetime string.
     */
    public static function date($format, $timestamp = null, $opt = ['timezone' => 'local', 'in_persian' => true])
    {
        $opt = [
            'timezone'   => isset($opt) && is_array($opt) && array_key_exists('timezone', $opt) ? $opt['timezone'] : 'local',
            'in_persian' => isset($opt) && is_array($opt) && array_key_exists('in_persian', $opt) ? $opt['in_persian'] : true,
        ];
        // initialize $timestamp
        if (!$timestamp) {
            $timestamp = time();
        } elseif (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        } elseif (!is_int($timestamp)) {
            $timestamp = (int) $timestamp;
        }
        // initialize $timezone
        $timezone = $opt['timezone'];
        if ($timezone === 'local' || $timezone === false) {
            //do noting
        } elseif (is_numeric($timezone)) {
            $timestamp += (int) $timezone;
        } elseif (is_string($timezone)) {
            $dtz = new \DateTimeZone($timezone);
            $time_obj = new \DateTime('now', $dtz);
            $deff_time = $dtz->getOffset($time_obj);
            $timestamp += $deff_time;
        }

        // Create date needed parameters
        // get gregorian parameters and convert them to jalali
        [$gYear, $gMonth, $gDay, $gWeek] = explode('-', date('Y-m-d-w', $timestamp));
        $jdate = self::gregorianToJalali($gYear, $gMonth, $gDay);
        $pYear = $jdate['jyear'];
        $pMonth = $jdate['jmonth'];
        $pDay = $jdate['jday'];
        $pWeek = ($gWeek + 1);
        if ($pWeek >= 7) {
            $pWeek = 0;
        }

        if ($format == '\\') {
            $format = '//';
        }
        // Go through $format and output formatted result
        $lenghFormat = strlen($format);
        $i = 0;
        $result = '';
        while ($i < $lenghFormat) {
            $par = $format[$i];
            if ($par == '\\') {
                $result .= $format[++$i];
                $i++;
                continue;
            }
            switch ($par) {
                // Day
                case 'd':
                    $result .= (($pDay < 10) ? ('0'.$pDay) : $pDay);
                    break;
                case 'D':
                    $result .= ($opt['in_persian']) ? (self::$_faweek_short_name[$pWeek]) : (self::$_enweek_short_name[$pWeek]);
                    break;
                case 'j':
                    $result .= $pDay;
                    break;
                case 'l':
                    $result .= ($opt['in_persian']) ? (self::$_faweek_name[$pWeek]) : (self::$_enweek_name[$pWeek]);
                    break;
                case 'N':
                    $result .= $pWeek + 1;
                    break;
                case 'w':
                    $result .= $pWeek;
                    break;
                case 'z':
                    $result .= self::dayOfYear($pMonth, $pDay);
                    break;
                case 'S':
                    if ($opt['in_persian']) {
                        $result .= 'ام';
                    } else {
                        $result .= date($par, $timestamp);
                    }
                    break;
                // Week
                case 'W':
                    $result .= ceil(self::dayOfYear($pMonth, $pDay) / 7);
                    break;
                // Month
                case 'F':
                    $result .= ($opt['in_persian']) ? (self::$_famonth_name[$pMonth]) : (self::$_enmonth_name[$pMonth]);
                    break;
                case 'm':
                    $result .= (($pMonth < 10) ? ('0'.$pMonth) : $pMonth);
                    break;
                case 'M':
                    $month = ($opt['in_persian']) ? (self::$_famonth_name[$pMonth]) : (self::$_enmonth_name[$pMonth]);
                    $m = ($opt['in_persian']) ? substr($month, 0, 6) : substr($month, 0, 3);
                    $result .= $m;
                    break;
                case 'n':
                    $result .= $pMonth;
                    break;
                case 't':
                    $result .= self::dayOfMonth($pMonth, $pYear);
                    break;
                // Years
                case 'L':
                    $result .= (self::isLeapYear($pYear)) ? 1 : 0;
                    break;
                case 'Y':
                case 'o':
                    $result .= $pYear;
                    break;
                case 'y':
                    $result .= substr($pYear, 2);
                    break;
                // Time
                case 'a':
                case 'A':
                    if ($opt['in_persian']) {
                        if (date('a', $timestamp) == 'am') {
                            $result .= (($par == 'a') ? 'ق.ظ' : 'قبل از ظهر');
                        } else {
                            $result .= (($par == 'a') ? 'ب.ظ' : 'بعد از ظهر');
                        }
                    } else {
                        $result .= date($par, $timestamp);
                    }
                    break;
                case 'B':
                case 'g':
                case 'G':
                case 'h':
                case 'H':
                case 's':
                case 'u':
                case 'i':
                    // Timezone
                case 'e':
                case 'I':
                case 'O':
                case 'P':
                case 'T':
                case 'Z':
                    $result .= date($par, $timestamp);
                    break;
                // Full Date/Time
                case 'c':
                    $result .= ($pYear.'-'.$pMonth.'-'.$pDay.' '.date('H:i:s P', $timestamp));
                    break;
                case 'r':
                    $month = ($opt['in_persian']) ? (self::$_famonth_name[$pMonth]) : (self::$_enmonth_name[$pMonth]);
                    $result .= ($opt['in_persian']) ? (self::$_faweek_short_name[$pWeek]) : (self::$_enweek_short_name[$pWeek]);
                    $result .= '. '.$pDay.' ';
                    $result .= $month.' '.$pYear.' '.date('H:i:s P', $timestamp);
                    break;
                case 'U':
                    $result .= $timestamp;
                    break;
                default:
                    $result .= $par;
            }
            $i++;
        }
        if ($opt['in_persian']) {
            return Converter::numToPersian($result);
        }

        return $result;
    }

    /**
     * Format a local time/date according to locale settings (jalali equivalent of php strftime() function).
     *
     * @link http://php.net/en/manual/function.strftime.php
     *
     * @param string     $format    For example 'Y-m-d H:i:s'
     * @param int|string $timestamp [optional] null for current time
     * @param array      $opt       [optional] array(<br />
     *                              'in_persian' => convert numbers to persian? (Default true)<br />)
     *
     * @return string Returns a formatted datetime string.
     */
    public static function strftime($format, $timestamp = null, $opt = ['in_persian' => true])
    {
        $opt = [
            'in_persian' => isset($opt) && is_array($opt) && array_key_exists('in_persian', $opt) ? $opt['in_persian'] : true,
        ];
        // initialize $timestamp
        if (!$timestamp) {
            $timestamp = time();
        }
        // Create date needed parameters
        // Get gregorian parameters and convert them to jalali
        [$gYear, $gMonth, $gDay, $gWeek] = explode('-', date('Y-m-d-w', $timestamp));
        $jdate = self::gregorianToJalali($gYear, $gMonth, $gDay);
        $pYear = $jdate['jyear'];
        $pMonth = $jdate['jmonth'];
        $pDay = $jdate['jday'];
        $pWeek = $gWeek + 1;
        if ($pWeek >= 7) {
            $pWeek = 0;
        }
        // Go through $format and output formatted result
        $lenghFormat = strlen($format);
        $i = 0;
        $result = '';
        while ($i < $lenghFormat) {
            $par = $format[$i];
            if ($par == '%') {
                $type = $format[++$i];
                switch ($type) {
                    // Day
                    case 'a':
                        $result .= ($opt['in_persian']) ? (self::$_faweek_short_name[$pWeek]) : (self::$_enweek_short_name[$pWeek]);
                        break;

                    case 'A':
                        $result .= ($opt['in_persian']) ? (self::$_faweek_name[$pWeek]) : (self::$_enweek_name[$pWeek]);
                        break;

                    case 'd':
                        $result .= (($pDay < 10) ? '0'.$pDay : $pDay);
                        break;

                    case 'e':
                        $result .= $pDay;
                        break;

                    case 'j':
                        $dayinM = self::dayOfYear($pMonth, $pDay);
                        $result .= (($dayinM < 10) ? '00'.$dayinM : (($dayinM < 100) ? '0'.$dayinM : $dayinM));
                        break;

                    case 'u':
                        $result .= $pWeek + 1;
                        break;

                    case 'w':
                        $result .= $pWeek;
                        break;

                    // Week
                    case 'U':
                        $result .= floor(self::dayOfYear($pMonth, $pDay) / 7);
                        break;

                    case 'V':
                    case 'W':
                        $result .= ceil(self::dayOfYear($pMonth, $pDay) / 7);
                        break;

                    // Month
                    case 'b':
                    case 'h':
                        $month = ($opt['in_persian']) ? (self::$_famonth_name[$pMonth]) : (self::$_enmonth_name[$pMonth]);
                        $m = ($opt['in_persian']) ? substr($month, 0, 6) : substr($month, 0, 3);
                        $result .= $m;
                        break;

                    case 'B':
                        $month = ($opt['in_persian']) ? (self::$_famonth_name[$pMonth]) : (self::$_enmonth_name[$pMonth]);
                        $result .= $month;
                        break;

                    case 'm':
                        $result .= (($pMonth < 10) ? '0'.$pMonth : $pMonth);
                        break;

                    // Year
                    case 'C':
                        $result .= ceil($pYear / 100);
                        break;

                    case 'g':
                    case 'y':
                        $result .= substr($pYear, 2);
                        break;

                    case 'G':
                    case 'Y':
                        $result .= $pYear;
                        break;

                    // Time
                    case 'H':
                    case 'I':
                    case 'l':
                    case 'M':
                    case 'R':
                    case 'S':
                    case 'T':
                    case 'X':
                    case 'z':
                    case 'Z':
                        $result .= strftime('%'.$type, $timestamp);
                        break;

                    case 'p':
                    case 'P':
                    case 'r':
                        if ($opt['in_persian']) {
                            if (date('a', $timestamp) == 'am') {
                                $result .= (($type == 'p') ? 'ق.ظ' : (($type == 'P') ? 'قبل از ظهر' : strftime('%I:%M:%S قبل از ظهر', $timestamp)));
                            } else {
                                $result .= (($type == 'p') ? 'ب.ظ' : (($type == 'P') ? 'بعد از ظهر' : strftime('%I:%M:%S بعد از ظهر', $timestamp)));
                            }
                        } else {
                            $result .= strftime('%'.$type, $timestamp);
                        }
                        break;
                    // Time and Date Stamps
                    case 'c':
                        $result .= self::strftime('%y', $timestamp, $opt);
                        $result .= '/'.self::strftime('%m', $timestamp, $opt).'/';
                        $result .= self::strftime('%d', $timestamp, $opt).' ';
                        $result .= self::strftime('%H:%M:%S ', $timestamp, $opt);
                        break;

                    case 'D':
                    case 'x':
                        $result .= substr($pYear, 2).'/'.(($pMonth < 10) ? '0'.$pMonth : $pMonth).'/';
                        $result .= (($pDay < 10) ? '0'.$pDay : $pDay);
                        break;

                    case 'F':
                        $result .= $pYear.'/'.(($pMonth < 10) ? '0'.$pMonth : $pMonth).'/';
                        $result .= ($pDay < 10) ? '0'.$pDay : $pDay;
                        break;

                    case 's':
                        $result .= $timestamp;
                        break;

                    // Miscellaneous
                    case 'n':
                        $result .= "\n";
                        break;

                    case 't':
                        $result .= "\t";
                        break;

                    case '%':
                        $result .= '%';
                        break;

                    default:
                        $result .= '%'.$type;
                }
            } else {
                $result .= $par;
            }
            $i++;
        }
        if ($opt['in_persian']) {
            return Converter::numToPersian($result);
        }

        return $result;
    }

    /**
     * Return Unix timestamp for a jalali date (jalali equivalent of php mktime() function)<br />
     * Maximum is 06:44:07-1416/10/30.
     *
     * @link http://php.net/en/manual/function.mktime.php
     *
     * @param int $hour   [optional] max : 23<br />
     *                    The number of the hour relative to the start of the day determined by month, day and year.
     *                    Negative values reference the hour before midnight of the day in question.
     *                    Values greater than 23 reference the appropriate hour in the following day(s).
     * @param int $minute [optional] max : 59<br />
     *                    The number of the minute relative to the start of the hour.
     *                    Negative values reference the minute in the previous hour.
     *                    Values greater than 59 reference the appropriate minute in the following hour(s).
     * @param int $second [optional] max: 59<br />
     *                    The number of seconds relative to the start of the minute.
     *                    Negative values reference the second in the previous minute.
     *                    Values greater than 59 reference the appropriate second in the following minute(s).
     * @param int $year   [optional] <br />
     *                    The number of the year, may be a two or four digit value, with values between 0-69 mapping to 2000-2069
     *                    and 70-100 to 1970-2000. On systems where time_t is a 32bit signed integer, as most common today,
     *                    the valid range for year is somewhere between 1901 and 2038. However,
     *                    before PHP 5.1.0 this range was limited from 1970 to 2038 on some systems (e.g. Windows).
     * @param int $month  [optional] max: 12<br />
     *                    The number of the month relative to the end of the previous year.
     *                    Values 1 to 12 reference the normal calendar months of the year in question.
     *                    Values less than 1 (including negative values) reference the months in the previous year in reverse order,
     *                    so 0 is December, -1 is November, etc.
     *                    Values greater than 12 reference the appropriate month in the following year(s).
     * @param int $day    [optional] max: 31<br />
     *                    The number of the day relative to the end of the previous month.
     *                    Values 1 to 28, 29, 30 or 31 (depending upon the month) reference the normal days in the relevant month.
     *                    Values less than 1 (including negative values) reference the days in the previous month,
     *                    so 0 is the last day of the previous month, -1 is the day before that, etc.
     *                    Values greater than the number of days in the relevant month reference the appropriate day in the following month(s).
     *
     * @return int Unix timestamp
     */
    public static function mktime($hour = 0, $minute = 0, $second = 0, $year = 0, $month = 0, $day = 0)
    {
        if (($hour == 0) && ($minute == 0) && ($second == 0) && ($month == 0) && ($day == 0) && ($year == 0)) {
            return time();
        }
        $jdate = self::jalaliToGregorian($year, $month, $day);
        $year = $jdate['gyear'];
        $month = $jdate['gmonth'];
        $day = $jdate['gday'];

        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    /**
     * Validate a jalali date (jalali equivalent of php checkdate() function).
     *
     * @link http://php.net/en/manual/function.checkdate.php
     *
     * @param int $year  The year is between 1 and 32767 inclusive.
     * @param int $month The month is between 1 and 12 inclusive.
     * @param int $day   The day is within the allowed number of days for the given month. Leap years are taken into consideration.
     *
     * @return bool Returns TRUE if the date given is valid; otherwise returns false.
     */
    public static function checkdate($year, $month, $day)
    {
        if (($month < 1) || ($month > 12) || ($year < 1) || ($year > 32767) || ($day < 1)) {
            return false;
        }

        if ($day > self::$_jdays_in_month[$month]) {
            if (($month != 12) || ($day != 30) || !self::isLeapYear($year)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get date/time information (jalali equivalent of php getdate() function).
     *
     * @param int $timestamp [optional] The optional timestamp parameter is an integer Unix timestamp
     *                       that defaults to the current local time if a timestamp is not given.
     *
     * @return array Returns an associative array of information related to the timestamp.
     *               See the link below for more information
     *
     * @link http://php.net/en/manual/function.getdate.php
     */
    public static function getdate($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        [$seconds, $minutes, $hours, $mday, $wday, $mon, $year, $yday, $weekday, $month] = explode(
            '-',
            self::date('s-i-G-j-w-n-Y-z-l-F', $timestamp, ['timezone' => false, 'in_persian' => false])
        );

        return [
            0         => $timestamp, 'seconds' => $seconds, 'minutes' => $minutes, 'hours' => $hours,
            'mday'    => $mday, 'wday' => $wday, 'mon' => $mon, 'year' => $year, 'yday' => $yday,
            'weekday' => $weekday, 'month' => $month,
        ];
    }

    /**
     * gregorian to jalali conversion.
     *
     * @param int $g_y gregorian year
     * @param int $g_m gregorian month
     * @param int $g_d gregorian day
     *
     * @return array array('jyear' => 'jalali year', 'jmonth' => 'jalali month', 'jday' => 'jalali day')
     */
    public static function gregorianToJalali($g_y, $g_m, $g_d)
    {
        $g_y = (int) Converter::numToEnglish($g_y);
        $g_m = (int) Converter::numToEnglish($g_m);
        $g_d = (int) Converter::numToEnglish($g_d);

        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $g_day_no = ((365 * $gy) + self::_intDiv($gy + 3, 4) - self::_intDiv($gy + 99, 100) + self::_intDiv($gy + 399, 400));

        for ($i = 0; $i < $gm; $i++) {
            $g_day_no += self::$_gdays_in_month[$i + 1];
        }

        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
            // leap and after Feb
            $g_day_no++;
        }

        $g_day_no += $g_d - 1;
        $j_day_no = $g_day_no - 79;
        $j_np = self::_intDiv($j_day_no, 12053); // 12053 = (365 * 33 + 32 / 4)
        $j_day_no %= 12053;
        $jy = (979 + 33 * $j_np + 4 * self::_intDiv($j_day_no, 1461)); // 1461 = (365 * 4 + 4 / 4)
        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::_intDiv($j_day_no - 1, 365);
            $j_day_no--;
            $j_day_no %= 365;
        }

        for ($i = 0; ($i < 11 && $j_day_no >= self::$_jdays_in_month[$i + 1]); $i++) {
            $j_day_no -= self::$_jdays_in_month[$i + 1];
        }

        return [
            'jyear'  => $jy,
            'jmonth' => $i + 1,
            'jday'   => $j_day_no + 1,
        ];
    }

    /**
     * jalali to gregorian conversion.
     *
     * @param int $j_y jalali year
     * @param int $j_m jalali month
     * @param int $j_d jalali day
     *
     * @return array array('gyear' => 'gregorian year', 'gmonth' => 'gregorian month', 'gday' => 'gregorian day')
     */
    public static function jalaliToGregorian($j_y, $j_m, $j_d)
    {
        $j_y = (int) Converter::numToEnglish($j_y);
        $j_m = (int) Converter::numToEnglish($j_m);
        $j_d = (int) Converter::numToEnglish($j_d);

        $jy = $j_y - 979;
        $jm = $j_m - 1;
        $j_day_no = (365 * $jy + self::_intDiv($jy, 33) * 8 + self::_intDiv($jy % 33 + 3, 4));

        for ($i = 0; $i < $jm; $i++) {
            $j_day_no += self::$_jdays_in_month[$i + 1];
        }

        $j_day_no += $j_d - 1;
        $g_day_no = $j_day_no + 79;
        $gy = (1600 + 400 * self::_intDiv($g_day_no, 146097)); // 146097 = (365 * 400 + 400 / 4 - 400 / 100 + 400 / 400)
        $g_day_no %= 146097;
        $leap = 1;

        if ($g_day_no >= 36525) { // 36525 = (365 * 100 + 100 / 4)
            $g_day_no--;
            $gy += (100 * self::_intDiv($g_day_no, 36524)); // 36524 = (365 * 100 + 100 / 4 - 100 / 100)
            $g_day_no %= 36524;
            if ($g_day_no >= 365) {
                $g_day_no++;
            } else {
                $leap = 0;
            }
        }

        $gy += (4 * self::_intDiv($g_day_no, 1461)); // 1461 = (365 * 4 + 4 / 4)
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = 0;
            $g_day_no--;
            $gy += self::_intDiv($g_day_no, 365);
            $g_day_no %= 365;
        }

        for ($i = 0; $g_day_no >= (self::$_gdays_in_month[$i + 1] + ($i == 1 && $leap)); $i++) {
            $g_day_no -= (self::$_gdays_in_month[$i + 1] + ($i == 1 && $leap));
        }

        return [
            'gyear'  => $gy,
            'gmonth' => $i + 1,
            'gday'   => $g_day_no + 1,
        ];
    }

    /**
     * return day number from first day of year.
     *
     * @param int $pMonth jalali month
     * @param int $pDay   day of month
     *
     * @return int number of days past in year depend on given date
     */
    public static function dayOfYear($pMonth, $pDay)
    {
        $days = 0;
        for ($i = 1; $i < $pMonth; $i++) {
            $days += self::$_jdays_in_month[$i];
        }

        return $days + $pDay;
    }

    /**
     * check jalali year is leap(kabise).
     *
     * @param int $year jalali year
     *
     * @return bool true if is, false if is not
     */
    public static function isLeapYear($year)
    {
        $mod = $year % 33;

        return $mod == 1 || $mod == 5 || $mod == 9 || $mod == 13 || $mod == 17 || $mod == 22 || $mod == 26 || $mod == 30;
    }

    /**
     * return last day of month.
     *
     * @param int $month jalali month
     * @param int $year  [optional] jalali year to check if is leap (Default non-leap year)
     *
     * @return int number of day in month
     */
    public static function dayOfMonth($month, $year = 33)
    {
        if (($month == 12) && self::isLeapYear($year)) {
            return 30;
        }

        $month = (int) $month;

        return self::$_jdays_in_month[$month];
    }

    /**
     * return jalali name of month from month number.
     *
     * @param int   $month jalali month
     * @param array $opt   internal params $full_name and $get_in_persian
     *
     * @internal  bool $full_name whether to get full names or short names
     * @internal  bool $get_in_persian get persian names? (Default true)
     *
     * @return string name of month
     */
    public static function getMonthName($month, $opt = ['full_name' => true, 'in_persian' => true])
    {
        $full_name = !(isset($opt) && is_array($opt) && array_key_exists('full_name', $opt)) || $opt['full_name'];
        $get_in_persian = isset($opt) && is_array($opt) && array_key_exists('in_persian', $opt) ? $opt['in_persian'] : true;
        $month = (int) $month;

        if ($full_name) {
            return ($get_in_persian) ? self::$_famonth_name[$month] : self::$_enmonth_name[$month];
        }

        return ($get_in_persian) ? self::$_famonth_short_name[$month] : self::$_enmonth_short_name[$month];
    }

    /**
     * return week name.
     *
     * @param int  $gWeek          index of day in gregorian week (zero-based)
     * @param bool $get_in_persian get in persian? (Default true)
     *
     * @return string name of weekday
     */
    public static function getWeekName($gWeek = 0, $get_in_persian = true)
    {
        $jWeek = $gWeek + 1;
        if ($jWeek >= 7) {
            $jWeek = 0;
        }

        return ($get_in_persian) ? self::$_faweek_name[$jWeek] : self::$_enweek_name[$jWeek];
    }

    /**
     * return short week name.
     *
     * @param int  $gWeek          index of day in gregorian week (zero-based)
     * @param bool $get_in_persian get in persian? (Default true)
     *
     * @return string short name of weekday
     */
    public static function getShortWeekName($gWeek = 0, $get_in_persian = true)
    {
        $jWeek = $gWeek + 1;
        if ($jWeek >= 7) {
            $jWeek = 0;
        }

        return ($get_in_persian) ? self::$_faweek_short_name[$jWeek] : self::$_enweek_short_name[$jWeek];
    }

    /**
     * integer quotient of $a and $b.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    private static function _intDiv($a, $b)
    {
        return (int) ($a / $b);
    }
}
