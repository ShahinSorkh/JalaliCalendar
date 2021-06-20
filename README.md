# JalaliCalendar
A php native-like package for use of Persians and everyone who uses
Jalali calendar and wants to get rid of Arabic characters in their php applications.

## Installation
You can use [composer](http://getcomposer.org) to install this calendar
````sh
$ composer require sorkh.shahin/jalalicalendar
````

## Usage
This package is under `ShSo\Jalali` namespace and does two things:
1. Provides Jalali date/time using `ShSo\Jalali\DateTime` class
1. Converts English and Arabic numbers to Persian and vice versa using `ShSo\Jalali\Converter` class

### How to use
All methods and functionalities are static methods of the classes `ShSo\Jalali\DateTime` and `ShSo\Jalali\Converter`

`ShSo\Jalali\DateTime` provides:
* `date()`
_Jalali equivalent of php native date() function with options to
choose whether use Persian numbers and choose different timezone_
* `strftime()`
_Jalali equivalent of php native strftime() function with options to
choose whether use Persian numbers_
* `mktime()`
_Jalali equivalent of php native mktime() function_
* `checkdate()`
_Jalali equivalent of php native checkdate() function_
* `getdate()`
_Jalali equivalent of php native getdate() function_
* `gregorianToJalali()`
_Converts Gregorian date to Jalali_
* `jalaliToGregorian()`
_Converts Jalali date to Gregorian_
* `dayOfYear()`
_Returns number of passed days since the start of the year, `dayOfYear(6,8)` returns 163_
* `isLeapYear()`
_Returns true if the given year is leap or false otherwise_
* `dayOfMonth()`
_Returns number of days in the month, `dayOfMonth(12,1395)` returns 30_
* `getMonthName()`
_Returns the name of the month, whether in full or short and in Persian or English_
* `getWeekName()`
_Returns day of week in Gregorian order whether in Persian or English_
* `getShortWeekName()`
_Returns day of week in Gregorian order in 3 letters whether in Persian or English_

`ShSo\Jalali\Converter` provides:
* `numToPersian()`
_Converts all numbers whether in English or Arabic to Persian_
* `numToEnglish()`
_Converts all numbers whether in Arabic or Persian to English_
* `arabicToPersian()`
_Converts ('ي', 'ك', 'ة') to ('ی', 'ک', 'ه') and ('٤', '٥', '٦') to ('۴', '۵', '۶')_

## License
This package is under [MIT License](https://opensource.org/licenses/MIT).
