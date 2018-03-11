# JalaliCalendar
A php native-like package for use of persians and everyone who uses
jalali calendar and wants to get rid of arabic characters in their php applications.

## Instalation
You can use [composer](http://getcomposer.org) to install this calendar
````$
$ composer require sorkh.shahin/jalalicalendar
````

## Usage
This package is under `ShSo\Jalali` namespace and does two things:
1. Provides jalali date/time using `ShSo\Jalali\DateTime` class
1. Converts english and arabic numbers to persian and vice versa using `ShSo\Jalali\Converter` class

### How to use
All methods and functionalities are static methods of the classes `ShSo\Jalali\DateTime` and `ShSo\Jalali\Converter`

`ShSo\Jalali\DateTime` provides:
* `date()`
_Jalali equivalent of php native date() function with options to
choose whether use persian numbers and choose diffrent timezones_
* `strftime()`
_Jalali equivalent of php native strftime() function with options to
choose whether use persian numbers_
* `mktime()`
_Jalali equivalent of php native mktime() function_
* `checkdate()`
_Jalali equivalent of php native checkdate() function_
* `getdate()`
_Jalali equivalent of php native getdate() function_
* `gregorianToJalali()`
_Converts gregorian date to jalali_
* `jalaliToGregorian()`
_Converts jalali date to gregorian_
* `dayOfYear()`
_Returns number of passed days since the start of the year, `dayOfYear(6,8)` returns 163_
* `isLeapYear()`
_Returns true if the given year is leap or false otherwise_
* `dayOfMonth()`
_Returns number of days in the month, `dayOfMonth(12,1395)` returns 30_
* `getMonthName()`
_Returns the name of the month, whether in full or short and in persian or english_
* `getWeekName()`
_Returns day of week in gregorian order whether in persian or english_
* `getShortWeekName()`
_Returns day of week in gregorian order in 3 letters whether in persian or english_

`ShSo\Jalali\Converter` provides:
* `numToPersian()`
_Converts all numbers whether in english or arabic to persian_
* `numToEnglish()`
_Converts all numbers whether in arabic or persian to english_
* `arabicToPersian()`
_Converts ('ي', 'ك', 'ة') to ('ی', 'ک', 'ه') and ('٤', '٥', '٦') to ('۴', '۵', '۶')_

## License
This package is under [MIT License](https://opensource.org/licenses/MIT), simply means
_Do whatever you want with this, just **don't** delete my name from it_.
