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

use LupeCode\phpTraderNative\ConvertedJava\Core;
use LupeCode\phpTraderNative\ConvertedJava\MAType;
use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class Trader
{
    protected static $errorArray = [
        RetCode::BadParam             => "Bad parameter",
        RetCode::AllocError           => "Allocation error",
        RetCode::OutOfRangeStartIndex => "Out of range on start index",
        RetCode::OutOfRangeEndIndex   => "Out of range on end index",
        RetCode::InternalError        => "Internal error",
    ];

    /** @var Core */
    protected static $_Core = null;
    protected static $outBegIdx;
    protected static $outNBElement;

    protected static function getCore()
    {
        if (empty(self::$_Core)) {
            self::$_Core        = new Core();
            self::$outBegIdx    = new MInteger();
            self::$outNBElement = new MInteger();
        }

        return self::$_Core;
    }

    /**
     * @param int $retCode
     *
     * @throws \Exception
     */
    protected static function checkForError(int $retCode)
    {
        switch ($retCode) {
            case RetCode::Success:
                return;
            default:
                throw new \Exception(static::$errorArray[$retCode], $retCode);
        }
    }

    /**
     * @param array $arrays
     *
     * @throws \Exception
     */
    protected static function verifyArrayCounts(array $arrays)
    {
        $numberOfArrays = count($arrays);
        $count          = count($arrays[0]);
        for ($i = 1; $i < $numberOfArrays; $i++) {
            if (count($arrays[$i]) !== $count) {
                throw new \Exception("The count of the input arrays do not match each other.");
            }
        }
    }

    protected static function adjustIndexes(array $outReal, MInteger $outBegIdx)
    {
        $newOutReal = [];
        $outReal    = \array_values($outReal);
        foreach ($outReal as $index => $inDouble) {
            $newOutReal[$index + $outBegIdx->value] = $inDouble;
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function acos(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->acos(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Chaikin A/D Line
     *
     * @param array $high   High price, array of real values.
     * @param array $low    Low price, array of real values.
     * @param array $close  Closing price, array of real values.
     * @param array $volume Volume traded, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ad(array $high, array $low, array $close, array $volume): array
    {
        self::verifyArrayCounts([$high, $low, $close, $volume]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $volume  = \array_values($volume);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->ad(0, $endIdx, $high, $low, $close, $volume, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Calculates the vector addition of real0 to real1 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function add(array $real0, array $real1): array
    {
        self::verifyArrayCounts([$real0, $real1]);
        $real0   = \array_values($real0);
        $real1   = \array_values($real1);
        $endIdx  = count($real0) - 1;
        $outReal = [];
        $RetCode = self::getCore()->add(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Chaikin A/D Oscillator
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param array $volume     Volume traded, array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 3, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function adosc(array $high, array $low, array $close, array $volume, int $fastPeriod = null, int $slowPeriod = null): array
    {
        $fastPeriod = $fastPeriod ?? 3;
        $slowPeriod = $slowPeriod ?? 10;
        self::verifyArrayCounts([$high, $low, $close, $volume]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $volume  = \array_values($volume);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->adOsc(0, $endIdx, $high, $low, $close, $volume, $fastPeriod, $slowPeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Average Directional Movement Index
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function adx(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->adx(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Average Directional Movement Index Rating
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function adxr(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->adxr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Absolute Price Oscillator
     *
     * @param array $real       Array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function apo(array $real, int $fastPeriod = null, int $slowPeriod = null, int $mAType = null): array
    {
        $fastPeriod = $fastPeriod ?? 12;
        $slowPeriod = $slowPeriod ?? 26;
        $mAType     = $mAType ?? MAType::SMA;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->apo(0, $endIdx, $real, $fastPeriod, $slowPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Aroon
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns a 2D array with calculated data or false on failure. [AroonDown => [...], AroonUp => [...]]
     * @throws \Exception
     */
    public static function aroon(array $high, array $low, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low]);
        $high         = \array_values($high);
        $low          = \array_values($low);
        $endIdx       = count($high) - 1;
        $outAroonDown = [];
        $outAroonUp   = [];
        $RetCode      = self::getCore()->aroon(0, $endIdx, $high, $low, $timePeriod, $outBegIdx, $outNBElement, $outAroonDown, $outAroonUp);
        static::checkForError($RetCode);

        return ['AroonDown' => self::adjustIndexes($outAroonDown, self::$outBegIdx), 'AroonUp' => self::adjustIndexes($outAroonUp, self::$outBegIdx)];
    }

    /**
     * Aroon Oscillator
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function aroonosc(array $high, array $low, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->aroonOsc(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric ASin
     * Calculates the arc sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function asin(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->asin(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric ATan
     * Calculates the arc tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function atan(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->atan(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Average True Range
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function atr(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->atr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Average Price
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function avgprice(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open    = \array_values($open);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->avgPrice(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Bollinger Bands
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDevUp    [OPTIONAL] [DEFAULT 2.0, SUGGESTED -2.0-2.0 INCREMENT 0.2] Deviation multiplier for upper band. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param float $nbDevDn    [OPTIONAL] [DEFAULT 2.0, SUGGESTED -2.0-2.0 INCREMENT 0.2] Deviation multiplier for lower band. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MAType::* series of constants should be used.
     *
     * @return array Returns a 2D array with calculated data or false on failure. [UpperBand => [...], MiddleBand => [...], LowerBand => [...]]
     * @throws \Exception
     */
    public static function bbands(array $real, int $timePeriod = null, float $nbDevUp = null, float $nbDevDn = null, int $mAType = null): array
    {
        $timePeriod        = $timePeriod ?? 5;
        $nbDevUp           = $nbDevUp ?? 2.0;
        $nbDevDn           = $nbDevDn ?? 2.0;
        $mAType            = $mAType ?? MAType::SMA;
        $real              = \array_values($real);
        $endIdx            = count($real) - 1;
        $outRealUpperBand  = [];
        $outRealMiddleBand = [];
        $outRealLowerBand  = [];
        $RetCode           = self::getCore()->bbands(0, $endIdx, $real, $timePeriod, $nbDevUp, $nbDevDn, $mAType, self::$outBegIdx, self::$outNBElement, $outRealUpperBand, $outRealMiddleBand, $outRealLowerBand);
        static::checkForError($RetCode);

        return
            [
                'UpperBand'  => self::adjustIndexes($outRealUpperBand, self::$outBegIdx),
                'MiddleBand' => self::adjustIndexes($outRealMiddleBand, self::$outBegIdx),
                'LowerBand'  => self::adjustIndexes($outRealLowerBand, self::$outBegIdx),
            ];
    }

    /**
     * Beta
     *
     * @param array $real0      Array of real values.
     * @param array $real1      Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function beta(array $real0, array $real1, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 5;
        self::verifyArrayCounts([$real0, $real1]);
        $real0   = \array_values($real0);
        $real1   = \array_values($real1);
        $endIdx  = count($real0) - 1;
        $outReal = [];
        $RetCode = self::getCore()->beta(0, $endIdx, $real0, $real1, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Balance Of Power
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function bop(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open    = \array_values($open);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($open) - 1;
        $outReal = [];
        $RetCode = self::getCore()->bop(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Commodity Channel Index
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cci(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->cci(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Two Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdl2crows(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdl2Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Three Black Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdl3blackcrows(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdl3BlackCrows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Three Inside Up/Down
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdl3inside(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdl3Inside(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Three-Line Strike
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdl3linestrike(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdl3LineStrike(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Three Outside Up/Down
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdl3outside(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdl3Outside(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Three Stars In The South
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdl3starsinsouth(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdl3StarsInSouth(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Three Advancing White Soldiers
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdl3whitesoldiers(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdl3WhiteSoldiers(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlabandonedbaby(array $open, array $high, array $low, array $close, float $penetration = null): array
    {
        $penetration = $penetration ?? 0.3;
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlAbandonedBaby(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Advance Block
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdladvanceblock(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlAdvanceBlock(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Belt-hold
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlbelthold(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlBeltHold(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Breakaway
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlbreakaway(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlBreakaway(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Closing Marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlclosingmarubozu(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlClosingMarubozu(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Concealing Baby Swallow
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlconcealbabyswall(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlConcealBabysWall(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Counterattack
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlcounterattack(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlCounterAttack(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdldarkcloudcover(array $open, array $high, array $low, array $close, float $penetration = null): array
    {
        $penetration = $penetration ?? 0.5;
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlDarkCloudCover(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdldoji(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Doji Star
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdldojistar(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdldojistar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Dragonfly Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdldragonflydoji(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlDragonflyDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Engulfing Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlengulfing(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlEngulfing(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdleveningdojistar(array $open, array $high, array $low, array $close, float $penetration = null): array
    {
        $penetration = $penetration ?? 0.3;
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlEveningDojiStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdleveningstar(array $open, array $high, array $low, array $close, float $penetration = null): array
    {
        $penetration = $penetration ?? 0.3;
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlEveningStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Up/Down-gap side-by-side white lines
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlgapsidesidewhite(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlGapSideSideWhite(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlgravestonedoji(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlGravestoneDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Hammer
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlhammer(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHammer(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Hanging Man
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlhangingman(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHangingMan(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Harami Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlharami(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHarami(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Harami Cross Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlharamicross(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHaramiCross(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * High-Wave Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlhighwave(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHighWave(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Hikkake Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlhikkake(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHikkake(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Modified Hikkake Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlhikkakemod(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHikkakeMod(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Homing Pigeon
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlhomingpigeon(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlHomingPigeon(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Identical Three Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlidentical3crows(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlIdentical3Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * In-Neck Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlinneck(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlInNeck(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Inverted Hammer
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlinvertedhammer(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlInvertedHammer(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Kicking
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlkicking(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlKicking(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Kicking - bull/bear determined by the longer marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlkickingbylength(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlKickingByLength(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Ladder Bottom
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlladderbottom(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlLadderBottom(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Long Legged Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdllongleggeddoji(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlLongLeggedDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Long Line Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdllongline(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlLongLine(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlmarubozu(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlMarubozu(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Matching Low
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlmatchinglow(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlMatchingLow(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlmathold(array $open, array $high, array $low, array $close, float $penetration = null): array
    {
        $penetration = $penetration ?? 0.5;
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlMatHold(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlmorningdojistar(array $open, array $high, array $low, array $close, float $penetration = null): array
    {
        $penetration = $penetration ?? 0.3;
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlMorningDojiStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlmorningstar(array $open, array $high, array $low, array $close, float $penetration = null): array
    {
        $penetration = $penetration ?? 0.3;
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlMorningStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * On-Neck Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlonneck(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlOnNeck(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Piercing Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlpiercing(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlPiercing(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Rickshaw Man
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlrickshawman(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlRickshawMan(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Rising/Falling Three Methods
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlrisefall3methods(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlRiseFall3Methods(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Separating Lines
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlseparatinglines(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlSeparatingLines(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Shooting Star
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlshootingstar(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlShootingStar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Short Line Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlshortline(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlShortLine(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Spinning Top
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlspinningtop(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlSpinningTop(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Stalled Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlstalledpattern(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlStalledPattern(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Stick Sandwich
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlsticksandwich(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlStickSandwich(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Takuri (Dragonfly Doji with very long lower shadow)
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdltakuri(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlTakuri(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Tasuki Gap
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdltasukigap(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlTasukiGap(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Thrusting Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlthrusting(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlThrusting(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Tristar Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdltristar(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlTristar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Unique 3 River
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlunique3river(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlUnique3River(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Upside Gap Two Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlupsidegap2crows(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlUpsideGap2Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Upside/Downside Gap Three Methods
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cdlxsidegap3methods(array $open, array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$open, $high, $low, $close]);
        $open       = \array_values($open);
        $high       = \array_values($high);
        $low        = \array_values($low);
        $close      = \array_values($close);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->cdlXSideGap3Methods(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Vector Ceil
     * Calculates the next highest integer for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ceil(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->ceil(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Chande Momentum Oscillator
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cmo(array $real, int $timePeriod = null): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->cmo(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Pearson's Correlation Coefficient (r)
     *
     * @param array $real0      Array of real values.
     * @param array $real1      Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function correl(array $real0, array $real1, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        self::verifyArrayCounts([$real0, $real1]);
        $real0   = \array_values($real0);
        $real1   = \array_values($real1);
        $endIdx  = count($real0) - 1;
        $outReal = [];
        $RetCode = self::getCore()->correl(0, $endIdx, $real0, $real1, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric Cos
     * Calculates the cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cos(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->cos(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric Cosh
     * Calculates the hyperbolic cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function cosh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->cosh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Double Exponential Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 3, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function dema(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->dema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Arithmetic Div
     * Divides each value from real0 by the corresponding value from real1 and returns the resulting array.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function div(array $real0, array $real1): array
    {
        self::verifyArrayCounts([$real0, $real1]);
        $real0   = \array_values($real0);
        $real1   = \array_values($real1);
        $endIdx  = count($real0) - 1;
        $outReal = [];
        $RetCode = self::getCore()->div(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Directional Movement Index
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array  Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function dx(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->dx(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Exponential Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ema(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->ema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Arithmetic Exp
     * Calculates e raised to the power of each value in real. Returns an array with the calculated data.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function exp(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->exp(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Floor
     * Calculates the next lowest integer for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function floor(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->floor(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Dominant Cycle Period
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ht_dcperiod(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->htDcPeriod(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Dominant Cycle Phase
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ht_dcphase(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->htDcPhase(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Phasor Components
     *
     * @param array $real    Array of real values.
     * @param array $inPhase Empty array, will be filled with in phase data.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ht_phasor(array $real, array &$inPhase): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outQuadrature = [];
        $RetCode       = self::getCore()->htPhasor(0, $endIdx, $real, $outBegIdx, $outNBElement, $inPhase, $outQuadrature);
        static::checkForError($RetCode);

        return self::adjustIndexes($outQuadrature, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - SineWave
     *
     * @param array $real Array of real values.
     * @param array $sine Empty array, will be filled with sine data.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ht_sine(array $real, array &$sine): array
    {
        $real        = \array_values($real);
        $endIdx      = count($real) - 1;
        $outLeadSine = [];
        $RetCode     = self::getCore()->htSine(0, $endIdx, $real, $outBegIdx, $outNBElement, $sine, $outLeadSine);
        static::checkForError($RetCode);

        return self::adjustIndexes($outLeadSine, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Instantaneous Trendline
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ht_trendline(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->htTrendline(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Trend vs Cycle Mode
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ht_trendmode(array $real): array
    {
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outInteger = [];
        $RetCode    = self::getCore()->htTrendMode(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outInteger);
        static::checkForError($RetCode);

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Kaufman Adaptive Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function kama(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->kama(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Linear Regression Angle
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function linearreg_angle(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->linearRegAngle(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Linear Regression Angle
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function linearreg_intercept(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->linearRegIntercept(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Linear Regression Slope
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function linearreg_slope(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->linearRegSlope(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Linear Regression
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function linearreg(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->linearReg(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Log Natural
     * Calculates the natural logarithm for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ln(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->ln(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Log10
     * Calculates the base-10 logarithm for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function log10(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->log10(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Moving average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ma(array $real, int $timePeriod = null, int $mAType = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $mAType     = $mAType ?? MAType::SMA;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->movingAverage(0, $endIdx, $real, $timePeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Moving Average Convergence/Divergence
     *
     * @param array $real         Array of real values.
     * @param int   $fastPeriod   [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod   [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure. [MACD => [...], MACDSignal => [...], MACDHist => [...]]
     * @throws \Exception
     */
    public static function macd(array $real, int $fastPeriod = null, int $slowPeriod = null, int $signalPeriod = null): array
    {
        $fastPeriod    = $fastPeriod ?? 12;
        $slowPeriod    = $slowPeriod ?? 26;
        $signalPeriod  = $signalPeriod ?? 9;
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        $RetCode       = self::getCore()->macd(0, $endIdx, $real, $fastPeriod, $slowPeriod, $signalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        static::checkForError($RetCode);

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx),
            ];
    }

    /**
     * Moving Average Convergence/Divergence with controllable Moving Average type
     *
     * @param array    $real         Array of real values.
     * @param int      $fastPeriod   [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int      $fastMAType   [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MAType::* series of constants should be used.
     * @param int      $slowPeriod   [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int      $slowMAType   [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MAType::* series of constants should be used.
     * @param int      $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     * @param int|null $signalMAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function macdext(array $real, int $fastPeriod = null, int $fastMAType = null, int $slowPeriod = null, int $slowMAType = null, int $signalPeriod = null, int $signalMAType = null): array
    {
        $fastPeriod    = $fastPeriod ?? 12;
        $fastMAType    = $fastMAType ?? MAType::SMA;
        $slowPeriod    = $slowPeriod ?? 26;
        $slowMAType    = $slowMAType ?? MAType::SMA;
        $signalPeriod  = $signalPeriod ?? 9;
        $signalMAType  = $signalMAType ?? MAType::SMA;
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        $RetCode       = self::getCore()->macdExt(0, $endIdx, $real, $fastPeriod, $fastMAType, $slowPeriod, $slowMAType, $signalPeriod, $signalMAType, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        static::checkForError($RetCode);

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx),
            ];
    }

    /**
     * Moving Average Convergence/Divergence Fix 12/26
     *
     * @param array $real         Array of real values.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function macdfix(array $real, int $signalPeriod = null): array
    {
        $signalPeriod  = $signalPeriod ?? 9;
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        $RetCode       = self::getCore()->macdFix(0, $endIdx, $real, $signalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        static::checkForError($RetCode);

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx),
            ];
    }

    /**
     * MESA Adaptive Moving Average
     *
     * @param array $real      Array of real values.
     * @param float $fastLimit [OPTIONAL] [DEFAULT 0.5, SUGGESTED 0.21-0.80] Upper limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     * @param float $slowLimit [OPTIONAL] [DEFAULT 0.05, SUGGESTED 0.01-0.60] Lower limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function mama(array $real, float $fastLimit = null, float $slowLimit = null): array
    {
        $fastLimit = $fastLimit ?? 0.5;
        $slowLimit = $slowLimit ?? 0.05;
        $real      = \array_values($real);
        $endIdx    = count($real) - 1;
        $outMAMA   = [];
        $outFAMA   = [];
        $RetCode   = self::getCore()->mama(0, $endIdx, $real, $fastLimit, $slowLimit, $outBegIdx, $outNBElement, $outMAMA, $outFAMA);
        static::checkForError($RetCode);

        return
            [
                'MAMA' => self::adjustIndexes($outMAMA, self::$outBegIdx),
                'FAMA' => self::adjustIndexes($outFAMA, self::$outBegIdx),
            ];
    }

    /**
     * Moving average with variable period
     *
     * @param array $real      Array of real values.
     * @param array $periods   Array of real values.
     * @param int   $minPeriod [OPTIONAL] [DEFAULT 2, SUGGESTED 4-200] Value less than minimum will be changed to Minimum period. Valid range from 2 to 100000
     * @param int   $maxPeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Value higher than maximum will be changed to Maximum period. Valid range from 2 to 100000
     * @param int   $mAType    [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function mavp(array $real, array $periods, int $minPeriod = null, int $maxPeriod = null, int $mAType = null): array
    {
        $minPeriod = $minPeriod ?? 2;
        $maxPeriod = $maxPeriod ?? 30;
        $mAType    = $mAType ?? MAType::SMA;
        $real      = \array_values($real);
        $endIdx    = count($real) - 1;
        $outReal   = [];
        $RetCode   = self::getCore()->movingAverageVariablePeriod(0, $endIdx, $real, $periods, $minPeriod, $maxPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Highest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function max(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->max(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Index of highest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function maxindex(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->maxIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Median Price
     *
     * @param array $high High price, array of real values.
     * @param array $low  Low price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function medprice(array $high, array $low): array
    {
        self::verifyArrayCounts([$high, $low]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->medPrice(0, $endIdx, $high, $low, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function mfi(array $high, array $low, array $close, array $volume, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close, $volume]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $volume  = \array_values($volume);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->mfi(0, $endIdx, $high, $low, $close, $volume, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * MidPoint over period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function midpoint(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->midPoint(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Midpoint Price over period
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function midprice(array $high, array $low, int $timePeriod = null)
    {
        $timePeriod = $timePeriod ?? 14;
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->midPrice(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Lowest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function min(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->min(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Index of lowest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function minindex(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->minIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Lowest and highest values over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function minmax(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outMin     = [];
        $outMax     = [];
        $RetCode    = self::getCore()->minMax(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax);
        static::checkForError($RetCode);

        return [
            'Min' => self::adjustIndexes($outMin, self::$outBegIdx),
            'Max' => self::adjustIndexes($outMax, self::$outBegIdx),
        ];
    }

    /**
     * Indexes of lowest and highest values over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function minmaxindex(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outMin     = [];
        $outMax     = [];
        $RetCode    = self::getCore()->minMaxIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax);
        static::checkForError($RetCode);

        return [
            'Min' => self::adjustIndexes($outMin, self::$outBegIdx),
            'Max' => self::adjustIndexes($outMax, self::$outBegIdx),
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function minus_di(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->minusDI(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Minus Directional Movement
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function minus_dm(array $high, array $low, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->minusDM(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Momentum
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function mom(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 10;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->mom(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Arithmetic Mult
     * Calculates the vector dot product of real0 with real1 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function mult(array $real0, array $real1): array
    {
        self::verifyArrayCounts([$real0, $real1]);
        $real0   = \array_values($real0);
        $real1   = \array_values($real1);
        $endIdx  = count($real0) - 1;
        $outReal = [];
        $RetCode = self::getCore()->mult(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Normalized Average True Range
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function natr(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->natr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * On Balance Volume
     *
     * @param array $real   Array of real values.
     * @param array $volume Volume traded, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function obv(array $real, array $volume): array
    {
        self::verifyArrayCounts([$real, $volume]);
        $real    = \array_values($real);
        $volume  = \array_values($volume);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->obv(0, $endIdx, $real, $volume, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Plus Directional Indicator
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function plus_di(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->plusDI(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Plus Directional Movement
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function plus_dm(array $high, array $low, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->plusDM(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Percentage Price Oscillator
     *
     * @param array $real       Array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ppo(array $real, int $fastPeriod = null, int $slowPeriod = null, int $mAType = null): array
    {
        $fastPeriod = $fastPeriod ?? 12;
        $slowPeriod = $slowPeriod ?? 26;
        $mAType     = $mAType ?? MAType::SMA;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->ppo(0, $endIdx, $real, $fastPeriod, $slowPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Rate of change : ((price/prevPrice)-1)*100
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function roc(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 10;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->roc(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Rate of change Percentage: (price-prevPrice)/prevPrice
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function rocp(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 10;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->rocp(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Rate of change ratio 100 scale: (price/prevPrice)*100
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function rocr100(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 10;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->rocr100(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Rate of change ratio: (price/prevPrice)
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function rocr(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 10;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->rocr(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Relative Strength Index
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function rsi(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->rsi(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Parabolic SAR
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param float $acceleration [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.20] Acceleration Factor used up to the Maximum value. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $maximum      [OPTIONAL] [DEFAULT 0.2, SUGGESTED 0.20-0.40] Acceleration Factor Maximum value. Valid range from 0 to TRADER_REAL_MAX.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sar(array $high, array $low, float $acceleration = null, float $maximum = null): array
    {
        $acceleration = $acceleration ?? 0.02;
        $maximum      = $maximum ?? 0.2;
        self::verifyArrayCounts([$high, $low]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->sar(0, $endIdx, $high, $low, $acceleration, $maximum, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sarext(array $high, array $low, float $startValue = null, float $offsetOnReverse = null, float $accelerationInitLong = null, float $accelerationLong = null, float $accelerationMaxLong = null, float $accelerationInitShort = null, float $accelerationShort = null, float $accelerationMaxShort = null): array
    {
        $startValue            = $startValue ?? 0.0;
        $offsetOnReverse       = $offsetOnReverse ?? 0.0;
        $accelerationInitLong  = $accelerationInitLong ?? 0.02;
        $accelerationLong      = $accelerationLong ?? 0.02;
        $accelerationMaxLong   = $accelerationMaxLong ?? 0.2;
        $accelerationInitShort = $accelerationInitShort ?? 0.02;
        $accelerationShort     = $accelerationShort ?? 0.02;
        $accelerationMaxShort  = $accelerationMaxShort ?? 0.2;
        self::verifyArrayCounts([$high, $low]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->sarExt(0, $endIdx, $high, $low, $startValue, $offsetOnReverse, $accelerationInitLong, $accelerationLong, $accelerationMaxLong, $accelerationInitShort, $accelerationShort, $accelerationMaxShort, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric Sin
     * Calculates the sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sin(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->sin(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric Sinh
     * Calculates the hyperbolic sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sinh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->sinh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Simple Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sma(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->sma(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Square Root
     * Calculates the square root of each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sqrt(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->sqrt(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Standard Deviation
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDev      [OPTIONAL] [DEFAULT 1.0, SUGGESTED -2-2] Number of deviations
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function stddev(array $real, int $timePeriod = null, float $nbDev = null): array
    {
        $timePeriod = $timePeriod ?? 5;
        $nbDev      = $nbDev ?? 1.0;
        $timePeriod = $timePeriod ?? 30;
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outReal    = [];
        $RetCode    = self::getCore()->stddev(0, $endIdx, $real, $timePeriod, $nbDev, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Stochastic
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param array $close        Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $slowK_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-K line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $slowK_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-K. MAType::* series of constants should be used.
     * @param int   $slowD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-D line. Valid range from 1 to 100000.
     * @param int   $slowD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-D. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function stoch(array $high, array $low, array $close, int $fastK_Period = null, int $slowK_Period = null, int $slowK_MAType = null, int $slowD_Period = null, int $slowD_MAType = null): array
    {
        $fastK_Period = $fastK_Period ?? 5;
        $slowK_Period = $slowK_Period ?? 3;
        $slowK_MAType = $slowK_MAType ?? MAType::SMA;
        $slowD_Period = $slowD_Period ?? 3;
        $slowD_MAType = $slowD_MAType ?? MAType::SMA;
        self::verifyArrayCounts([$high, $low, $close]);
        $high     = \array_values($high);
        $low      = \array_values($low);
        $close    = \array_values($close);
        $endIdx   = count($high) - 1;
        $outSlowK = [];
        $outSlowD = [];
        $RetCode  = self::getCore()->stoch(0, $endIdx, $high, $low, $close, $fastK_Period, $slowK_Period, $slowK_MAType, $slowD_Period, $slowD_MAType, self::$outBegIdx, self::$outNBElement, $outSlowK, $outSlowD);
        static::checkForError($RetCode);

        return [
            'SlowK' => self::adjustIndexes($outSlowK, self::$outBegIdx),
            'SlowD' => self::adjustIndexes($outSlowD, self::$outBegIdx),
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
     * @param int   $fastD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Fast-D. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function stochf(array $high, array $low, array $close, int $fastK_Period = null, int $fastD_Period = null, int $fastD_MAType = null): array
    {
        $fastK_Period = $fastK_Period ?? 5;
        $fastD_Period = $fastD_Period ?? 3;
        $fastD_MAType = $fastD_MAType ?? MAType::SMA;
        self::verifyArrayCounts([$high, $low, $close]);
        $high     = \array_values($high);
        $low      = \array_values($low);
        $close    = \array_values($close);
        $endIdx   = count($high) - 1;
        $outFastK = [];
        $outFastD = [];
        $RetCode  = self::getCore()->stochF(0, $endIdx, $high, $low, $close, $fastK_Period, $fastD_Period, $fastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD);
        static::checkForError($RetCode);

        return [
            'SlowK' => self::adjustIndexes($outFastK, self::$outBegIdx),
            'SlowD' => self::adjustIndexes($outFastD, self::$outBegIdx),
        ];
    }

    /**
     * Stochastic Relative Strength Index
     *
     * @param array $real         Array of real values.
     * @param int   $timePeriod   [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Fast-D line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $fastD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Fast-D. MAType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function stochrsi(array $real, int $timePeriod = null, int $fastK_Period = null, int $fastD_Period = null, int $fastD_MAType = null): array
    {
        $timePeriod   = $timePeriod ?? 14;
        $fastK_Period = $fastK_Period ?? 5;
        $fastD_Period = $fastD_Period ?? 3;
        $fastD_MAType = $fastD_MAType ?? MAType::SMA;
        $real    = \array_values($real);
        $endIdx   = count($real) - 1;
        $outFastK = [];
        $outFastD = [];
        $RetCode  = self::getCore()->stochRsi(0, $endIdx, $real, $timePeriod, $fastK_Period, $fastD_Period, $fastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD);
        static::checkForError($RetCode);

        return [
            'SlowK' => self::adjustIndexes($outFastK, self::$outBegIdx),
            'SlowD' => self::adjustIndexes($outFastD, self::$outBegIdx),
        ];
    }

    /**
     * Vector Arithmetic Subtraction
     * Calculates the vector subtraction of real1 from real0 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sub(array $real0, array $real1): array
    {
        self::verifyArrayCounts([$real0, $real1]);
        $real0    = \array_values($real0);
        $real1     = \array_values($real1);
        $endIdx  = count($real0) - 1;
        $outReal = [];
        $RetCode = self::getCore()->sub(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Summation
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function sum(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->sum(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Triple Exponential Moving Average (T3)
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $vFactor    [OPTIONAL] [DEFAULT 0.7, SUGGESTED 0.01-1.00] Volume Factor. Valid range from 1 to 0.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function t3(array $real, int $timePeriod = null, float $vFactor = null): array
    {
        $timePeriod = $timePeriod ?? 5;
        $vFactor    = $vFactor ?? 0.7;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->t3(0, $endIdx, $real, $timePeriod, $vFactor, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric Tan
     * Calculates the tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function tan(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->tan(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Vector Trigonometric Tanh
     * Calculates the hyperbolic tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function tanh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->tanh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Triple Exponential Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function tema(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->tema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * True Range
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function trange(array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$high, $low, $close]);
        $high   = \array_values($high);
        $low   = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->trueRange(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Triangular Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function trima(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->trima(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * 1-day Rate-Of-Change (ROC) of a Triple Smooth EMA
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function trix(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->trix(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Time Series Forecast
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function tsf(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->tsf(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Typical Price
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function typprice(array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->typPrice(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function ultosc(array $high, array $low, array $close, int $timePeriod1 = null, int $timePeriod2 = null, int $timePeriod3 = null): array
    {
        $timePeriod1 = $timePeriod1 ?? 7;
        $timePeriod2 = $timePeriod2 ?? 14;
        $timePeriod3 = $timePeriod3 ?? 28;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->ultOsc(0, $endIdx, $high, $low, $close, $timePeriod1, $timePeriod2, $timePeriod3, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Variance
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDev      [OPTIONAL] [DEFAULT 1.0, SUGGESTED -2-2] Number of deviations
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function var(array $real, int $timePeriod = null, float $nbDev = null): array
    {
        $timePeriod = $timePeriod ?? 5;
        $nbDev      = $nbDev ?? 1.0;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->variance(0, $endIdx, $real, $timePeriod, $nbDev, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Weighted Close Price
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function wclprice(array $high, array $low, array $close): array
    {
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->wclPrice(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Williams' %R
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function willr(array $high, array $low, array $close, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 14;
        self::verifyArrayCounts([$high, $low, $close]);
        $high    = \array_values($high);
        $low     = \array_values($low);
        $close   = \array_values($close);
        $endIdx  = count($high) - 1;
        $outReal = [];
        $RetCode = self::getCore()->willR(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Weighted Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data or false on failure.
     * @throws \Exception
     */
    public static function wma(array $real, int $timePeriod = null): array
    {
        $timePeriod = $timePeriod ?? 30;
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $outReal = [];
        $RetCode = self::getCore()->wma(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal);
        static::checkForError($RetCode);

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

}
