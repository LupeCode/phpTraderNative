# PHP Trader Native

This is a PHP port of the Trader extension for PHP, which is a port of the TA-LIB C/Java code.

This port is written in PHP and without any other requirements.

The goal is that this library can be used by those whom cannot install the PHP Trader extension.

![PHPUnit Tests](https://github.com/LupeCode/phpTraderNative/workflows/PHPUnit%20Tests/badge.svg)

## Requirements

* PHP >= 7.0.0

That's the only thing you need!  As stated, you do not need to install any extensions for this library.

## Installation

This library is intended to be installed with composer.

~~~
composer require lupecode/php-trader-native
~~~

## Usage

### Drop-In Replacement

This library is intended to be a drop-in replacement for the Trader extension, with a minimum of code changed.

If you had    
`trader_adosc($high, $low, $close, $volume, $fastPeriod, $slowPeriod)`    
You can swap that with    
`Trader::adosc($high, $low, $close, $volume, $fastPeriod, $slowPeriod)`    
You only need to change `trader_` to `Trader::`.  That's it!

### Friendly-Named Replacement

Another option that this package provides is to use functions that have an easier to understand name.

If you do not to use `adosc` because it is not descriptive enough, you can instead use `chaikinAccumulationDistributionOscillator` like this:    
`TraderFriendly::chaikinAccumulationDistributionOscillator($high, $low, $close, $volume, $fastPeriod, $slowPeriod)`

## Note about default values
The PECL version of the TA-LIB, "Trader", does not have the correct default values for the functions.
A quick look shows that many of the function use the minimum value for the optional parameters instead of the defaults used in the C/Java version of TA-LIB.
Some of the tests, like `testAdOscDefaultsDifferent` pass as long as the PECL Trader library uses different defaults than those in the C/Java code.

For the curious, the TA-LIB source for AdOsc can be seen [here](https://svn.php.net/viewvc/pecl/trader/trunk/ta-lib/src/ta_func/ta_ADOSC.c?revision=325828&view=markup) with defaults of 3 and 10,
while the PECL Trader source can be seen [here](https://svn.php.net/viewvc/pecl/trader/trunk/functions/trader_adosc.c?revision=344243&view=markup) with defaults of 2 and 2.

**This package uses the C/Java defaults and not the PECL defaults.**

## Speed

Given that this library is written in pure PHP, is does run slower than the PECL extension which is written in C.
My benchmarks give 5x to 30x slower depending on the function.

**I welcome any help with optimizations!**
I have not worked to optimize this library; it's a simple conversion from C to PHP.

## Contributing/Development

### Requirements

* PHP >= 7.0.0
* ext_trader >= 0.4.1 [here](https://pecl.php.net/package/trader)

### Setup

Checkout the repository and then install with composer.

~~~
git checkout git@github.com:LupeCode/phpTraderNative.git
cd phpTraderNative
composer install --dev
~~~

### Testing

Two PHPUnit XML files are included, one for testing and the other for coverage.  This is due to the fact that when some tests are run with coverage, PHP hangs and never finishes.

Run the tests using
~~~
php -dxdebug.coverage_enable=0 ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml ./tests
~~~

Run the coverage using
~~~
php -dxdebug.coverage_enable=1 ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit_coverage.xml ./tests
~~~

## Metrics & Coverage
### PHP Metrics Report
To see the current report of metrics by PHP Metrics, visit [Metrics](https://projects.lupecode.com/phpTraderNative/metrics/)
### PHP Unit Coverage
To see the current report of coverage by PHP Unit, visit [Coverage](https://projects.lupecode.com/phpTraderNative/logs/report/)
### PHP Unit Test Results
 * [[PHP 7.0 NTS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.0nts.html)
 * [[PHP 7.0 TS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.0ts.html)
 * [[PHP 7.1 NTS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.1nts.html)
 * [[PHP 7.1 TS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.1ts.html)
 * [[PHP 7.2 NTS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.2nts.html)
 * [[PHP 7.2 TS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.2ts.html)
 * [[PHP 7.3 NTS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.3nts.html)
 * [[PHP 7.3 TS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.3ts.html)
 * [[PHP 7.4 NTS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.4nts.html)
 * [[PHP 7.4 TS]](https://projects.lupecode.com/phpTraderNative/logs/php-7.4ts.html)
 * [[PHP 8.0 NTS]](https://projects.lupecode.com/phpTraderNative/logs/php-8.0nts.html)
 * [[PHP 8.0 TS]](https://projects.lupecode.com/phpTraderNative/logs/php-8.0ts.html)

## License

Below is the copyright information for TA-LIB found in the source code.

~~~
TA-LIB Copyright (c) 1999-2007, Mario Fortier
All rights reserved.

Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice,
 this list of conditions and the following disclaimer.

- Redistributions in binary form must reproduce the above copyright 
 notice, this list of conditions and the following disclaimer in the 
 documentation and/or other materials provided with the distribution.

- Neither name of author nor the names of its contributors may be used
 to endorse or promote products derived from this software without
 specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 ''AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE REGENTS OR
 CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
~~~
Below is the license for my porting of the code to PHP; GNU GPL v3 or newer.
~~~
Copyright (C) Lupe Code, LLC.; Joshua Lopez

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see http://www.gnu.org/licenses/.
~~~
