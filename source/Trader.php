<?php

/**
 * This is a PHP port of the Trader extension for PHP, which is a port of the TA-LIB C code.
 *
 * This port is written in PHP and without any other requirements.
 * The goal is that this library can be used by those whom cannot install the PHP Trader extension.
 *
 * Below is the copyright information for TA-LIB found in the source code.
 */

/* TA-LIB Copyright (c) 1999-2007, Mario Fortier
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 *
 * - Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in
 *   the documentation and/or other materials provided with the
 *   distribution.
 *
 * - Neither name of author nor the names of its contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace LupeCode\phpTraderNative;

use LupeCode\phpTraderNative\TALib\Classes\MyInteger;
use LupeCode\phpTraderNative\TALib\Core\CycleIndicators;
use LupeCode\phpTraderNative\TALib\Core\MathOperators;
use LupeCode\phpTraderNative\TALib\Core\MathTransform;
use LupeCode\phpTraderNative\TALib\Core\MomentumIndicators;
use LupeCode\phpTraderNative\TALib\Core\OverlapStudies;
use LupeCode\phpTraderNative\TALib\Core\PatternRecognition;
use LupeCode\phpTraderNative\TALib\Core\PriceTransform;
use LupeCode\phpTraderNative\TALib\Core\StatisticFunctions;
use LupeCode\phpTraderNative\TALib\Core\VolatilityIndicators;
use LupeCode\phpTraderNative\TALib\Core\VolumeIndicators;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;

class Trader
{
    protected static $errorArray = [
        ReturnCode::BadParam             => "Bad parameter",
        ReturnCode::AllocError           => "Allocation error",
        ReturnCode::OutOfRangeStartIndex => "Out of range on start index",
        ReturnCode::OutOfRangeEndIndex   => "Out of range on end index",
        ReturnCode::InternalError        => "Internal error",
    ];

    /** @var MyInteger */
    protected static $outBegIdx;
    /** @var MyInteger */
    protected static $outNBElement;

    protected static function prep()
    {
        self::$outBegIdx    = new MyInteger();
        self::$outNBElement = new MyInteger();
    }

    /**
     * @param int $ReturnCode
     *
     * @throws \Exception
     */
    protected static function checkForError(int $ReturnCode)
    {
        switch ($ReturnCode) {
            case ReturnCode::Success:
                return;
            default:
                throw new \Exception(static::$errorArray[$ReturnCode], $ReturnCode);
        }
    }

    /**
     * @param array $arrays
     *
     * @return int
     * @throws \Exception
     */
    protected static function verifyArrayCounts(array $arrays)
    {
        $count = count($arrays[0]);
        foreach ($arrays as &$array) {
            if (count($array) !== $count) {
                throw new \Exception("The count of the input arrays do not match each other.");
            }
            $array = \array_values($array);
        }

        return $count - 1;
    }

    /**
     * @param array $outReal
     * @param int   $offset
     *
     * @return array
     */
    protected static function adjustIndexes(array $outReal, int $offset): array
    {
        $newOutReal = [];
        $outReal    = \array_values($outReal);
        foreach ($outReal as $index => $inDouble) {
            $newOutReal[$index + $offset] = $inDouble;
        }

        return $newOutReal;
    }

    /**
     * Vector arc cosine
     *
     * Calculates the arc cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function acos(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->acos(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Chaikin A/D Line
     *
     * This indicator is a volume based indicator developed by Marc Chaikin which measures the cumulative flow of money into and out of an instrument.
     * The A/D line is calculated by multiplying the specific period’s volume with a multiplier that is based on the relationship of the closing price to the high-low range.
     * The A/D Line is formed by the running total of the Money Flow Volume. This indicator can be used to assert an underlying trend or to predict reversals.
     *
     * The combination of a high positive multiplier value and high volume indicates buying pressure.
     * So even with a downtrend in prices when there is an uptrend in the Accumulation Distribution Line there is indication for buying pressure (accumulation) that may result to a bullish reversal.
     *
     * Conversely a low negative multiplier value combined with, again, high volumes indicates selling pressure (distribution).
     *
     * @param array $high   High price, array of real values.
     * @param array $low    Low price, array of real values.
     * @param array $close  Closing price, array of real values.
     * @param array $volume Volume traded, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ad(array $high, array $low, array $close, array $volume): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close, &$volume]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new VolumeIndicators())->ad(0, $endIdx, $high, $low, $close, $volume, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Calculates the vector addition of real0 to real1 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function add(array $real0, array $real1): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->add(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Chaikin A/D Oscillator
     *
     * Chaikin Oscillator is positive when the 3-day EMA moves higher than the 10-day EMA and vice versa.
     *
     * The Chaikin Oscillator is the continuation of the Chaikin A/D Line and is used to observe changes in the A/D Line.
     *
     * The oscillator is based on the difference between the 3-day Exponential Moving Average (EMA) of the A/D Line and the 10-day EMA of the A/D Line and hence adds momentum to the A/D Line.
     * It is helpful for investors to use the Oscillator in order to determine the appropriate timing of trend reversals.
     *
     * When the Chaikin Oscillator turns positive there is indication that the A/D Line will increase and hence a Bullish (buy) signal will be generated. On the other hand a move into negative territory indicates a Bearish (sell) signal.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param array $volume     Volume traded, array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 3, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function adosc(array $high, array $low, array $close, array $volume, int $fastPeriod = 3, int $slowPeriod = 10): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close, &$volume]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new VolumeIndicators())->adOsc(0, $endIdx, $high, $low, $close, $volume, $fastPeriod, $slowPeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Average Directional Movement Index
     *
     * Developed by J. Welles Wilder and described in his book “New Concepts in Technical Trading Systems”, the Average Directional Movement Index (ADX) is a technical indicator that describes if a market or a financial instrument is trending or not.
     *
     * The ADX is a combination of two other indicators developed by Wilder, the positive directional indicator (+DI) and the negative directional indicator (-DI).
     *
     * Wilder recommends buying when +DI is higher than -DI, and selling when +DI is lower than -DI.
     *
     * The ADX indicates trend strength, not trend direction, and it is a lagging indicator.
     *
     * ADX range is between 0 and 100. Generally ADX readings below 20 indicate trend weakness, and readings above 40 indicate trend strength.
     * An extremely strong trend is indicated by readings above 50.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function adx(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->adx(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Average Directional Movement Index Rating
     *
     * The Average Directional Movement Index Rating (ADXR) measures the strength of the Average Directional Movement Index (ADX).
     * It's calculated by taking the average of the current ADX and the ADX from one time period before (time periods can vary, but the most typical period used is 14 days).
     *
     * Like the ADX, the ADXR ranges from values of 0 to 100 and reflects strengthening and weakening trends.
     * However, because it represents an average of ADX, values don't fluctuate as dramatically and some analysts believe the indicator helps better display trends in volatile markets.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function adxr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->adxr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Absolute Price Oscillator
     *
     * The Absolute Price Oscillator (APO) is based on the absolute differences between two moving averages of different lengths, a ‘Fast’ and a ‘Slow’ moving average.
     * A positive indicator value indicates an upward movement, while negative readings signal a downward trend.
     *
     * Divergences form when a new high or low in price is not confirmed by the Absolute Price Oscillator (APO).
     * A bullish divergence forms when price make a lower low, but the APO forms a higher low.
     * This indicates less downward momentum that could foreshadow a bullish reversal.
     * A bearish divergence forms when price makes a higher high, but the APO forms a lower high.
     * This shows less upward momentum that could foreshadow a bearish reversal.
     *
     * @param array $real       Array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function apo(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->apo(0, $endIdx, $real, $fastPeriod, $slowPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Aroon
     *
     * The Aroon indicator was developed by Tushar Chande in 1995.
     *
     * Both the Aroon up and the Aroon down fluctuate between zero and 100, with values close to 100 indicating a strong trend, and zero indicating a weak trend.
     * The lower the Aroon up, the weaker the uptrend and the stronger the downtrend, and vice versa.
     * The main assumption underlying this indicator is that a stock's price will close at record highs in an uptrend, and record lows in a downtrend.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns a 2D array with calculated data. [AroonDown => [...], AroonUp => [...]]
     * @throws \Exception
     */
    public static function aroon(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx       = self::verifyArrayCounts([&$high, &$low]);
        $outAroonDown = [];
        $outAroonUp   = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->aroon(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outAroonDown, $outAroonUp);
        static::checkForError($ReturnCode);

        return ['AroonDown' => self::adjustIndexes($outAroonDown, self::$outBegIdx->value), 'AroonUp' => self::adjustIndexes($outAroonUp, self::$outBegIdx->value)];
    }

    /**
     * Aroon Oscillator
     *
     * The Aroon oscillator is calculated by subtracting Aroon down from Aroon up.
     * Readings above zero indicate that an uptrend is present, while readings below zero indicate that a downtrend is present.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function aroonosc(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->aroonOsc(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric ASin
     *
     * Calculates the arc sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function asin(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->asin(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric ATan
     *
     * Calculates the arc tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function atan(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->atan(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Average True Range
     *
     * The average true range (ATR) is a measure of volatility introduced by Welles Wilder in his book, "New Concepts in Technical Trading Systems."
     * The true range indicator is the greatest of the following:
     *      current high less the current low,
     *      the absolute value of the current high less the previous close,
     *      and the absolute value of the current low less the previous close.
     * The average true range is a moving average, generally 14 days, of the true ranges.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function atr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new VolumeIndicators())->atr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Average Price
     *
     * An average price is a representative measure of a range of prices that is calculated by taking the sum of the values and dividing it by the number of prices being examined.
     * The average price reduces the range into a single value, which can then be compared to any point to determine if the value is higher or lower than what would be expected.
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function avgprice(array $open, array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new PriceTransform())->avgPrice(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Bollinger Bands
     *
     * A Bollinger Band® is a band plotted two standard deviations away from a simple moving average, developed by famous technical trader John Bollinger.
     *
     * Because standard deviation is a measure of volatility, Bollinger Bands® adjust themselves to the market conditions.
     * When the markets become more volatile, the bands widen (move further away from the average), and during less volatile periods, the bands contract (move closer to the average).
     * The tightening of the bands is often used by technical traders as an early indication that the volatility is about to increase sharply.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDevUp    [OPTIONAL] [DEFAULT 2.0, SUGGESTED -2.0-2.0 INCREMENT 0.2] Deviation multiplier for upper band. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param float $nbDevDn    [OPTIONAL] [DEFAULT 2.0, SUGGESTED -2.0-2.0 INCREMENT 0.2] Deviation multiplier for lower band. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns a 2D array with calculated data. [UpperBand => [...], MiddleBand => [...], LowerBand => [...]]
     * @throws \Exception
     */
    public static function bbands(array $real, int $timePeriod = 5, float $nbDevUp = 2.0, float $nbDevDn = 2.0, int $mAType = MovingAverageType::SMA): array
    {
        $real              = \array_values($real);
        $endIdx            = count($real) - 1;
        $outRealUpperBand  = [];
        $outRealMiddleBand = [];
        $outRealLowerBand  = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->bbands(0, $endIdx, $real, $timePeriod, $nbDevUp, $nbDevDn, $mAType, self::$outBegIdx, self::$outNBElement, $outRealUpperBand, $outRealMiddleBand, $outRealLowerBand);
        static::checkForError($ReturnCode);

        return
            [
                'UpperBand'  => self::adjustIndexes($outRealUpperBand, self::$outBegIdx->value),
                'MiddleBand' => self::adjustIndexes($outRealMiddleBand, self::$outBegIdx->value),
                'LowerBand'  => self::adjustIndexes($outRealLowerBand, self::$outBegIdx->value),
            ];
    }

    /**
     * Beta
     *
     * Beta is a measure of the volatility, or systematic risk, of a security or a portfolio in comparison to the market as a whole.
     * Beta is used in the capital asset pricing model (CAPM), which calculates the expected return of an asset based on its beta and expected market returns.
     * Beta is also known as the beta coefficient.
     *
     * A beta of 1 indicates that the security's price moves with the market.
     * A beta of less than 1 means that the security is theoretically less volatile than the market.
     * A beta of greater than 1 indicates that the security's price is theoretically more volatile than the market.
     *
     * @param array $real0      Array of real values.
     * @param array $real1      Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function beta(array $real0, array $real1, int $timePeriod = 5): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->beta(0, $endIdx, $real0, $real1, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Balance Of Power
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function bop(array $open, array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->bop(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Commodity Channel Index
     *
     * An oscillator used in technical analysis to help determine when an investment vehicle has been overbought and oversold.
     * The Commodity Channel Index, first developed by Donald Lambert, quantifies the relationship between the asset's price, a moving average (MA) of the asset's price, and normal deviations (D) from that average.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cci(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->cci(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Two Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdl2crows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdl2Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Three Black Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdl3blackcrows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdl3BlackCrows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Three Inside Up/Down
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdl3inside(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdl3Inside(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Three-Line Strike
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdl3linestrike(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdl3LineStrike(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Three Outside Up/Down
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdl3outside(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdl3Outside(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Three Stars In The South
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdl3starsinsouth(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdl3StarsInSouth(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Three Advancing White Soldiers
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdl3whitesoldiers(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdl3WhiteSoldiers(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Abandoned Baby
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlabandonedbaby(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlAbandonedBaby(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Advance Block
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdladvanceblock(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlAdvanceBlock(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Belt-hold
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlbelthold(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlBeltHold(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Breakaway
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlbreakaway(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlBreakaway(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Closing Marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlclosingmarubozu(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlClosingMarubozu(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Concealing Baby Swallow
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlconcealbabyswall(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlConcealBabysWall(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Counterattack
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlcounterattack(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlCounterAttack(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Dark Cloud Cover
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.5] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdldarkcloudcover(array $open, array $high, array $low, array $close, float $penetration = 0.5): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlDarkCloudCover(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdldoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Doji Star
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdldojistar(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdldojistar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Dragonfly Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdldragonflydoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlDragonflyDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Engulfing Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlengulfing(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlEngulfing(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Evening Doji Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdleveningdojistar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlEveningDojiStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Evening Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdleveningstar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlEveningStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Up/Down-gap side-by-side white lines
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlgapsidesidewhite(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlGapSideSideWhite(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Gravestone Doji
     *
     * @param array $open  Opening price, arr
     *                     ay of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlgravestonedoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlGravestoneDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Hammer
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlhammer(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHammer(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Hanging Man
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlhangingman(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHangingMan(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Harami Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlharami(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHarami(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Harami Cross Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlharamicross(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHaramiCross(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * High-Wave Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlhighwave(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHighWave(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Hikkake Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlhikkake(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHikkake(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Modified Hikkake Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlhikkakemod(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHikkakeMod(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Homing Pigeon
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlhomingpigeon(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlHomingPigeon(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Identical Three Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlidentical3crows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlIdentical3Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * In-Neck Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlinneck(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlInNeck(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Inverted Hammer
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlinvertedhammer(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlInvertedHammer(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Kicking
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlkicking(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlKicking(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Kicking - bull/bear determined by the longer marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlkickingbylength(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlKickingByLength(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Ladder Bottom
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlladderbottom(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlLadderBottom(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Long Legged Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdllongleggeddoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlLongLeggedDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Long Line Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdllongline(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlLongLine(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlmarubozu(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlMarubozu(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Matching Low
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlmatchinglow(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlMatchingLow(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Mat Hold
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.5] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlmathold(array $open, array $high, array $low, array $close, float $penetration = 0.5): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlMatHold(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Morning Doji Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlmorningdojistar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlMorningDojiStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Morning Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlmorningstar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlMorningStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * On-Neck Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlonneck(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlOnNeck(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Piercing Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlpiercing(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlPiercing(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Rickshaw Man
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlrickshawman(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlRickshawMan(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Rising/Falling Three Methods
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlrisefall3methods(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlRiseFall3Methods(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Separating Lines
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlseparatinglines(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlSeparatingLines(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Shooting Star
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlshootingstar(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlShootingStar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Short Line Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlshortline(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlShortLine(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Spinning Top
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlspinningtop(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlSpinningTop(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Stalled Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlstalledpattern(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlStalledPattern(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Stick Sandwich
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlsticksandwich(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlStickSandwich(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Takuri (Dragonfly Doji with very long lower shadow)
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdltakuri(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlTakuri(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Tasuki Gap
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdltasukigap(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlTasukiGap(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Thrusting Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlthrusting(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlThrusting(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Tristar Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdltristar(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlTristar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Unique 3 River
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlunique3river(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlUnique3River(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Upside Gap Two Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlupsidegap2crows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlUpsideGap2Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Upside/Downside Gap Three Methods
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlxsidegap3methods(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::prep();
        $ReturnCode = (new PatternRecognition())->cdlXSideGap3Methods(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Vector Ceil
     *
     * Calculates the next highest integer for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ceil(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->ceil(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Chande Momentum Oscillator
     *
     * A technical momentum indicator invented by the technical analyst Tushar Chande.
     * It is created by calculating the difference between the sum of all recent gains and the sum of all recent losses and then dividing the result by the sum of all price movement over the period.
     * This oscillator is similar to other momentum indicators such as the Relative Strength Index and the Stochastic Oscillator because it is range bounded (+100 and -100).
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cmo(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->cmo(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Pearson's Correlation Coefficient (r)
     *
     * A type of correlation coefficient that represents the relationship between two variables that are measured on the same interval or ratio scale.
     *
     * @param array $real0      Array of real values.
     * @param array $real1      Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function correl(array $real0, array $real1, int $timePeriod = 30): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->correl(0, $endIdx, $real0, $real1, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric Cos
     *
     * Calculates the cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cos(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->cos(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric Cosh
     *
     * Calculates the hyperbolic cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cosh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->cosh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Double Exponential Moving Average
     *
     * A technical indicator developed by Patrick Mulloy that first appeared in the February, 1994 Technical Analysis of Stocks & Commodities.
     * The DEMA is a calculation based on both a single exponential moving average (EMA) and a double EMA.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 3, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function dema(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->dema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Arithmetic Div
     *
     * Divides each value from real0 by the corresponding value from real1 and returns the resulting array.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function div(array $real0, array $real1): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->div(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Directional Movement Index
     *
     * The directional movement index (DMI) is an indicator developed by J. Welles Wilder for identifying when a definable trend is present in an instrument.
     * That is, the DMI tells whether an instrument is trending or not.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array  Returns an array with calculated data.
     * @throws \Exception
     */
    public static function dx(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->dx(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Exponential Moving Average
     *
     * An exponential moving average (EMA) is a type of moving average that is similar to a simple moving average, except that more weight is given to the latest data.
     * It's also known as the exponentially weighted moving average.
     * This type of moving average reacts faster to recent price changes than a simple moving average.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ema(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->ema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Arithmetic Exp
     *
     * Calculates e raised to the power of each value in real. Returns an array with the calculated data.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function exp(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->exp(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Floor
     *
     * Calculates the next lowest integer for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function floor(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->floor(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Hilbert Transform - Dominant Cycle Period
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_dcperiod(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new CycleIndicators())->htDcPeriod(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Hilbert Transform - Dominant Cycle Phase
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_dcphase(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new CycleIndicators())->htDcPhase(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Hilbert Transform - Phasor Components
     *
     * @param array $real    Array of real values.
     * @param array $inPhase Empty array, will be filled with in phase data.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_phasor(array $real, array &$inPhase): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outQuadrature = [];
        self::prep();
        $ReturnCode = (new CycleIndicators())->htPhasor(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $inPhase, $outQuadrature);
        static::checkForError($ReturnCode);
        $inPhase = self::adjustIndexes($inPhase, self::$outBegIdx->value);

        return self::adjustIndexes($outQuadrature, self::$outBegIdx->value);
    }

    /**
     * Hilbert Transform - SineWave
     *
     * @param array $real Array of real values.
     * @param array $sine Empty array, will be filled with sine data.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_sine(array $real, array &$sine): array
    {
        $real        = \array_values($real);
        $endIdx      = count($real) - 1;
        $outLeadSine = [];
        self::prep();
        $ReturnCode = (new CycleIndicators())->htSine(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $sine, $outLeadSine);
        static::checkForError($ReturnCode);
        $sine = self::adjustIndexes($sine, self::$outBegIdx->value);

        return self::adjustIndexes($outLeadSine, self::$outBegIdx->value);
    }

    /**
     * Hilbert Transform - Instantaneous Trendline
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_trendline(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->htTrendline(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Hilbert Transform - Trend vs Cycle Mode
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_trendmode(array $real): array
    {
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outInteger = [];
        self::prep();
        $ReturnCode = (new CycleIndicators())->htTrendMode(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx->value);
    }

    /**
     * Kaufman Adaptive Moving Average
     *
     * Developed by Perry Kaufman, Kaufman's Adaptive Moving Average (KAMA) is a moving average designed to account for market noise or volatility.
     * KAMA will closely follow prices when the price swings are relatively small and the noise is low.
     * KAMA will adjust when the price swings widen and follow prices from a greater distance.
     * This trend-following indicator can be used to identify the overall trend, time turning points and filter price movements.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function kama(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->kama(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Linear Regression Angle
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearreg_angle(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->linearRegAngle(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Linear Regression Intercept
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearreg_intercept(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->linearRegIntercept(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Linear Regression Slope
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearreg_slope(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->linearRegSlope(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Linear Regression
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearreg(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->linearReg(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Log Natural
     *
     * Calculates the natural logarithm for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ln(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->ln(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Log10
     *
     * Calculates the base-10 logarithm for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function log10(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->log10(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Moving average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ma(array $real, int $timePeriod = 30, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->movingAverage(0, $endIdx, $real, $timePeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Moving Average Convergence/Divergence
     *
     * @param array $real         Array of real values.
     * @param int   $fastPeriod   [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod   [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data. [MACD => [...], MACDSignal => [...], MACDHist => [...]]
     * @throws \Exception
     */
    public static function macd(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $signalPeriod = 9): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->macd(0, $endIdx, $real, $fastPeriod, $slowPeriod, $signalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        static::checkForError($ReturnCode);

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx->value),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx->value),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx->value),
            ];
    }

    /**
     * Moving Average Convergence/Divergence with controllable Moving Average type
     *
     * @param array $real         Array of real values.
     * @param int   $fastPeriod   [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $fastMAType   [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MovingAverageType::* series of constants should be used.
     * @param int   $slowPeriod   [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $slowMAType   [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MovingAverageType::* series of constants should be used.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     * @param int   $signalMAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data. [MACD => [...], MACDSignal => [...], MACDHist => [...]]
     * @throws \Exception
     */
    public static function macdext(array $real, int $fastPeriod = 12, int $fastMAType = MovingAverageType::SMA, int $slowPeriod = 26, int $slowMAType = MovingAverageType::SMA, int $signalPeriod = 9, int $signalMAType = MovingAverageType::SMA): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->macdExt(0, $endIdx, $real, $fastPeriod, $fastMAType, $slowPeriod, $slowMAType, $signalPeriod, $signalMAType, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        static::checkForError($ReturnCode);

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx->value),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx->value),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx->value),
            ];
    }

    /**
     * Moving Average Convergence/Divergence Fix 12/26
     *
     * @param array $real         Array of real values.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data. [MACD => [...], MACDSignal => [...], MACDHist => [...]]
     * @throws \Exception
     */
    public static function macdfix(array $real, int $signalPeriod = 9): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->macdFix(0, $endIdx, $real, $signalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        static::checkForError($ReturnCode);

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx->value),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx->value),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx->value),
            ];
    }

    /**
     * MESA Adaptive Moving Average
     *
     * @param array $real      Array of real values.
     * @param float $fastLimit [OPTIONAL] [DEFAULT 0.5, SUGGESTED 0.21-0.80] Upper limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     * @param float $slowLimit [OPTIONAL] [DEFAULT 0.05, SUGGESTED 0.01-0.60] Lower limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     *
     * @return array Returns an array with calculated data. [MAMA => [...], FAMA => [...]]
     * @throws \Exception
     */
    public static function mama(array $real, float $fastLimit = 0.5, float $slowLimit = 0.05): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outMAMA = [];
        $outFAMA = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->mama(0, $endIdx, $real, $fastLimit, $slowLimit, self::$outBegIdx, self::$outNBElement, $outMAMA, $outFAMA);
        static::checkForError($ReturnCode);

        return
            [
                'MAMA' => self::adjustIndexes($outMAMA, self::$outBegIdx->value),
                'FAMA' => self::adjustIndexes($outFAMA, self::$outBegIdx->value),
            ];
    }

    /**
     * Moving average with variable period
     *
     * @param array $real      Array of real values.
     * @param array $periods   Array of real values.
     * @param int   $minPeriod [OPTIONAL] [DEFAULT 2, SUGGESTED 4-200] Value less than minimum will be changed to Minimum period. Valid range from 2 to 100000
     * @param int   $maxPeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Value higher than maximum will be changed to Maximum period. Valid range from 2 to 100000
     * @param int   $mAType    [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mavp(array $real, array $periods, int $minPeriod = 2, int $maxPeriod = 30, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->movingAverageVariablePeriod(0, $endIdx, $real, $periods, $minPeriod, $maxPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Highest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function max(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->max(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Index of highest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function maxindex(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->maxIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Median Price
     *
     * @param array $high High price, array of real values.
     * @param array $low  Low price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function medprice(array $high, array $low): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new PriceTransform())->medPrice(0, $endIdx, $high, $low, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Money Flow Index
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param array $volume     Volume traded, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mfi(array $high, array $low, array $close, array $volume, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close, &$volume]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->mfi(0, $endIdx, $high, $low, $close, $volume, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * MidPoint over period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function midpoint(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->midPoint(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Midpoint Price over period
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function midprice(array $high, array $low, int $timePeriod = 14)
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->midPrice(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Lowest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function min(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->min(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Index of lowest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function minindex(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->minIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Lowest and highest values over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data. [Min => [...], Max => [...]]
     * @throws \Exception
     */
    public static function minmax(array $real, int $timePeriod = 30): array
    {
        $real   = \array_values($real);
        $endIdx = count($real) - 1;
        $outMin = [];
        $outMax = [];
        self::prep();
        $ReturnCode = (new MathOperators())->minMax(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax);
        static::checkForError($ReturnCode);

        return [
            'Min' => self::adjustIndexes($outMin, self::$outBegIdx->value),
            'Max' => self::adjustIndexes($outMax, self::$outBegIdx->value),
        ];
    }

    /**
     * Indexes of lowest and highest values over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data. [Min => [...], Max => [...]]
     * @throws \Exception
     */
    public static function minmaxindex(array $real, int $timePeriod = 30): array
    {
        $real   = \array_values($real);
        $endIdx = count($real) - 1;
        $outMin = [];
        $outMax = [];
        self::prep();
        $ReturnCode = (new MathOperators())->minMaxIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax);
        static::checkForError($ReturnCode);

        return [
            'Min' => self::adjustIndexes($outMin, self::$outBegIdx->value),
            'Max' => self::adjustIndexes($outMax, self::$outBegIdx->value),
        ];
    }

    /**
     * Minus Directional Indicator
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function minus_di(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->minusDI(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Minus Directional Movement
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function minus_dm(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->minusDM(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Momentum
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mom(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->mom(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Arithmetic Mult
     *
     * Calculates the vector dot product of real0 with real1 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mult(array $real0, array $real1): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->mult(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Normalized Average True Range
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function natr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new VolatilityIndicators())->natr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * On Balance Volume
     *
     * @param array $real   Array of real values.
     * @param array $volume Volume traded, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function obv(array $real, array $volume): array
    {
        $endIdx  = self::verifyArrayCounts([&$real, &$volume]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new VolumeIndicators())->obv(0, $endIdx, $real, $volume, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Plus Directional Indicator
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function plus_di(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->plusDI(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Plus Directional Movement
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function plus_dm(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->plusDM(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Percentage Price Oscillator
     *
     * @param array $real       Array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ppo(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->ppo(0, $endIdx, $real, $fastPeriod, $slowPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Rate of change : ((price/prevPrice)-1)*100
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function roc(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->roc(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Rate of change Percentage: (price-prevPrice)/prevPrice
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rocp(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->rocp(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Rate of change ratio 100 scale: (price/prevPrice)*100
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rocr100(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->rocr100(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Rate of change ratio: (price/prevPrice)
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rocr(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->rocr(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Relative Strength Index
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rsi(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->rsi(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Parabolic SAR
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param float $acceleration [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.20] Acceleration Factor used up to the Maximum value. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $maximum      [OPTIONAL] [DEFAULT 0.2, SUGGESTED 0.20-0.40] Acceleration Factor Maximum value. Valid range from 0 to TRADER_REAL_MAX.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sar(array $high, array $low, float $acceleration = 0.02, float $maximum = 0.2): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->sar(0, $endIdx, $high, $low, $acceleration, $maximum, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Parabolic SAR - Extended
     *
     * @param array $high                  High price, array of real values.
     * @param array $low                   Low price, array of real values.
     * @param float $startValue            [OPTIONAL] [DEFAULT 0.0] Start value and direction. 0 for Auto, >0 for Long, <0 for Short. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param float $offsetOnReverse       [OPTIONAL] [DEFAULT 0.0, SUGGESTED 0.01-0.15] Percent offset added/removed to initial stop on short/long reversal. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationInitLong  [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.19] Acceleration Factor initial value for the Long direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationLong      [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.20] Acceleration Factor for the Long direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationMaxLong   [OPTIONAL] [DEFAULT 0.2, SUGGESTED 0.20-0.40] Acceleration Factor maximum value for the Long direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationInitShort [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.19] Acceleration Factor initial value for the Short direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationShort     [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.20] Acceleration Factor for the Short direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationMaxShort  [OPTIONAL] [DEFAULT 0.2, SUGGESTED 0.20-0.40] Acceleration Factor maximum value for the Short direction. Valid range from 0 to TRADER_REAL_MAX.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sarext(array $high, array $low, float $startValue = 0.0, float $offsetOnReverse = 0.0, float $accelerationInitLong = 0.02, float $accelerationLong = 0.02, float $accelerationMaxLong = 0.2, float $accelerationInitShort = 0.02, float $accelerationShort = 0.02, float $accelerationMaxShort = 0.2): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->sarExt(0, $endIdx, $high, $low, $startValue, $offsetOnReverse, $accelerationInitLong, $accelerationLong, $accelerationMaxLong, $accelerationInitShort, $accelerationShort, $accelerationMaxShort, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric Sin
     *
     * Calculates the sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sin(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->sin(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric Sinh
     *
     * Calculates the hyperbolic sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sinh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->sinh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Simple Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sma(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->sma(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Square Root
     *
     * Calculates the square root of each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sqrt(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->sqrt(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Standard Deviation
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDev      [OPTIONAL] [DEFAULT 1.0, SUGGESTED -2-2] Number of deviations
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function stddev(array $real, int $timePeriod = 5, float $nbDev = 1.0): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->stddev(0, $endIdx, $real, $timePeriod, $nbDev, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Stochastic
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param array $close        Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $slowK_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-K line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $slowK_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-K. MovingAverageType::* series of constants should be used.
     * @param int   $slowD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-D line. Valid range from 1 to 100000.
     * @param int   $slowD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-D. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data. [SlowK => [...], SlowD => [...]]
     * @throws \Exception
     */
    public static function stoch(array $high, array $low, array $close, int $fastK_Period = 5, int $slowK_Period = 3, int $slowK_MAType = MovingAverageType::SMA, int $slowD_Period = 3, int $slowD_MAType = MovingAverageType::SMA): array
    {
        $endIdx   = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outSlowK = [];
        $outSlowD = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->stoch(0, $endIdx, $high, $low, $close, $fastK_Period, $slowK_Period, $slowK_MAType, $slowD_Period, $slowD_MAType, self::$outBegIdx, self::$outNBElement, $outSlowK, $outSlowD);
        static::checkForError($ReturnCode);

        return [
            'SlowK' => self::adjustIndexes($outSlowK, self::$outBegIdx->value),
            'SlowD' => self::adjustIndexes($outSlowD, self::$outBegIdx->value),
        ];
    }

    /**
     * Stochastic Fast
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param array $close        Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Fast-D line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $fastD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Fast-D. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data. [FastK => [...], FastD => [...]]
     * @throws \Exception
     */
    public static function stochf(array $high, array $low, array $close, int $fastK_Period = 5, int $fastD_Period = 3, int $fastD_MAType = MovingAverageType::SMA): array
    {
        $endIdx   = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outFastK = [];
        $outFastD = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->stochF(0, $endIdx, $high, $low, $close, $fastK_Period, $fastD_Period, $fastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD);
        static::checkForError($ReturnCode);

        return [
            'FastK' => self::adjustIndexes($outFastK, self::$outBegIdx->value),
            'FastD' => self::adjustIndexes($outFastD, self::$outBegIdx->value),
        ];
    }

    /**
     * Stochastic Relative Strength Index
     *
     * @param array $real         Array of real values.
     * @param int   $timePeriod   [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Fast-D line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $fastD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Fast-D. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data. [FastK => [...], FastD => [...]]
     * @throws \Exception
     */
    public static function stochrsi(array $real, int $timePeriod = 14, int $fastK_Period = 5, int $fastD_Period = 3, int $fastD_MAType = MovingAverageType::SMA): array
    {
        $real     = \array_values($real);
        $endIdx   = count($real) - 1;
        $outFastK = [];
        $outFastD = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->stochRsi(0, $endIdx, $real, $timePeriod, $fastK_Period, $fastD_Period, $fastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD);
        static::checkForError($ReturnCode);

        return [
            'FastK' => self::adjustIndexes($outFastK, self::$outBegIdx->value),
            'FastD' => self::adjustIndexes($outFastD, self::$outBegIdx->value),
        ];
    }

    /**
     * Vector Arithmetic Subtraction
     *
     * Calculates the vector subtraction of real1 from real0 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sub(array $real0, array $real1): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->sub(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Summation
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function sum(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathOperators())->sum(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Triple Exponential Moving Average (T3)
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $vFactor    [OPTIONAL] [DEFAULT 0.7, SUGGESTED 0.01-1.00] Volume Factor. Valid range from 1 to 0.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function t3(array $real, int $timePeriod = 5, float $vFactor = 0.7): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->t3(0, $endIdx, $real, $timePeriod, $vFactor, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric Tan
     *
     * Calculates the tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function tan(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->tan(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Vector Trigonometric Tanh
     *
     * Calculates the hyperbolic tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function tanh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MathTransform())->tanh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Triple Exponential Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function tema(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->tema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * True Range
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function trange(array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new VolatilityIndicators())->trueRange(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Triangular Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function trima(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->trima(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * 1-day Rate-Of-Change (ROC) of a Triple Smooth EMA
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function trix(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->trix(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Time Series Forecast
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function tsf(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->tsf(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Typical Price
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function typprice(array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new PriceTransform())->typPrice(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Ultimate Oscillator
     *
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param int   $timePeriod1 [OPTIONAL] [DEFAULT 7, SUGGESTED 1-200] Number of bars for 1st period. Valid range from 1 to 100000.
     * @param int   $timePeriod2 [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of bars for 2nd period. Valid range from 1 to 100000.
     * @param int   $timePeriod3 [OPTIONAL] [DEFAULT 28, SUGGESTED 1-200] Number of bars for 3rd period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ultosc(array $high, array $low, array $close, int $timePeriod1 = 7, int $timePeriod2 = 14, int $timePeriod3 = 28): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->ultOsc(0, $endIdx, $high, $low, $close, $timePeriod1, $timePeriod2, $timePeriod3, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Variance
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDev      [OPTIONAL] [DEFAULT 1.0, SUGGESTED -2-2] Number of deviations
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function var(array $real, int $timePeriod = 5, float $nbDev = 1.0): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new StatisticFunctions())->variance(0, $endIdx, $real, $timePeriod, $nbDev, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Weighted Close Price
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function wclprice(array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new PriceTransform())->wclPrice(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Williams' %R
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function willr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::prep();
        $ReturnCode = (new MomentumIndicators())->willR(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

    /**
     * Weighted Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function wma(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::prep();
        $ReturnCode = (new OverlapStudies())->wma(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($ReturnCode);

        return self::adjustIndexes($outReal, self::$outBegIdx->value);
    }

}
