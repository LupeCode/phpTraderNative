# PHP Trader Native

This is a PHP port of the Trader extension for PHP, which is a port of the TA-LIB C/Java code.

This port is written in PHP and without any other requirements.

The goal is that this library can be used by those whom cannot install the PHP Trader extension.

## Requirements

* PHP >= 7.0.0

That's the only thing you need!  As stated, you do not need to install any extensions for this library.

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

## Metrics & Coverage
### PHP Metrics Report
To see the current report of metrics by PHP Metrics, visit [Metrics](https://projects.lupecode.com/phpTraderNative/metrics/)
### PHP Unit Coverage
To see the current report of coverage by PHP Unit, visit [Coverage](https://projects.lupecode.com/phpTraderNative/logs/report/)
### PHP Unit Test Results
 * PHP 7.0 NTS [[XML]](https://projects.lupecode.com/phpTraderNative/logs/logfile-7.0NTS.xml) [[TXT]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.0NTS.txt) [[HTML]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.0NTS.html)
 * PHP 7.0 TS [[XML]](https://projects.lupecode.com/phpTraderNative/logs/logfile-7.0TS.xml) [[TXT]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.0TS.txt) [[HTML]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.0TS.html)
 * PHP 7.1 NTS [[XML]](https://projects.lupecode.com/phpTraderNative/logs/logfile-7.1NTS.xml) [[TXT]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.1NTS.txt) [[HTML]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.1NTS.html)
 * PHP 7.1 TS [[XML]](https://projects.lupecode.com/phpTraderNative/logs/logfile-7.1TS.xml) [[TXT]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.1TS.txt) [[HTML]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.1TS.html)
 * PHP 7.2 NTS [[XML]](https://projects.lupecode.com/phpTraderNative/logs/logfile-7.2NTS.xml) [[TXT]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.2NTS.txt) [[HTML]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.2TS.html)
 * PHP 7.2 TS [[XML]](https://projects.lupecode.com/phpTraderNative/logs/logfile-7.2TS.xml) [[TXT]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.2TS.txt) [[HTML]](https://projects.lupecode.com/phpTraderNative/logs/testdox-7.2TS.html)

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
