# PHP Trader Native

This is a PHP port of the Trader extension for PHP, which is a port of the TA-LIB C/Java code.

This port is written in PHP and without any other requirements.

The goal is that this library can be used by those whom cannot install the PHP Trader extension.

![PHPUnit Tests](https://github.com/LupeCode/phpTraderNative/workflows/PHPUnit%20Tests/badge.svg)

## Requirements

* PHP >= 8.2.0

That's the only thing you need!  As stated, you do not need to install any extensions for this library.

## Installation

This library is intended to be installed with composer.

~~~
composer require lupecode/php-trader-native
~~~

## Usage

### Drop-In Replacement

This library is intended to be a drop-in replacement for the Trader extension, it comes with a polyfill for the Trader extension.

### Friendly-Named Replacement

Another option that this package provides is to use functions that have an easier to understand name.

If you do not want to use `adosc` because it is not descriptive enough, you can instead use `chaikinAccumulationDistributionOscillator` like this:
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

* PHP >= 8.0.0
* ext_trader >= 0.4.1 [here](https://pecl.php.net/package/trader)

### Setup

Checkout the repository and then install with composer.

~~~
git checkout git@github.com:LupeCode/phpTraderNative.git
cd phpTraderNative
composer install --dev
~~~

### Directory Structure

* `source` - The source code for the library.
* `tests` - The PHPUnit tests for the library.
* `pecl` - The PECL Trader extension source code.
* `talib` - The TA-LIB source code that the PECL Trader extension uses.

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

Below is the license for my porting of the code to PHP.

~~~
MIT License

Copyright (C) Lupe Code, LLC.; Joshua Lopez

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
~~~
