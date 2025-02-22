<?php

/**
 * This is a PHP port of the Trader extension for PHP, which is a port of the TA-LIB C code.
 *
 * This port is written in PHP and without any other requirements.
 * The goal is that this library can be used by those whom cannot install the PHP Trader extension.
 *
 * Below is the copyright information for TA-LIB found in the source code.
 *
 *
 * TA-LIB Copyright (c) 1999-2007, Mario Fortier
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

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Classes\CandleSetting;
use LupeCode\phpTraderNative\TALib\Enum\CandleSettingType;
use LupeCode\phpTraderNative\TALib\Enum\Compatibility;
use LupeCode\phpTraderNative\TALib\Enum\RangeType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;
use LupeCode\phpTraderNative\TALib\Enum\UnstablePeriodFunctionID;

/**
 * Class Core
 *
 * @package LupeCode\phpTraderNative\TALib\Core
 */
class Core
{

    /** @var int[] */
    protected static array $unstablePeriod;
    /** @var CandleSetting[] */
    protected static array $candleSettings;
    public static int $compatibility = Compatibility::Default->value;

    /**
     * Core constructor.
     *
     * These settings would be set above, but are not allowed to be defaults for static variables.
     */
    public static function construct(): void
    {
        static::$candleSettings = [
            /* real body is long when it's longer than the average of the 10 previous candles' real body */
            new CandleSetting(CandleSettingType::BodyLong->value, RangeType::RealBody->value, 10, 1.),
            /* real body is very long when it's longer than 3 times the average of the 10 previous candles' real body */
            new CandleSetting(CandleSettingType::BodyVeryLong->value, RangeType::RealBody->value, 10, 3.),
            /* real body is short when it's shorter than the average of the 10 previous candles' real bodies */
            new CandleSetting(CandleSettingType::BodyShort->value, RangeType::RealBody->value, 10, 1.),
            /* real body is like doji's body when it's shorter than 10% the average of the 10 previous candles' high-low range */
            new CandleSetting(CandleSettingType::BodyDoji->value, RangeType::HighLow->value, 10, 0.1),
            /* shadow is long when it's longer than the real body */
            new CandleSetting(CandleSettingType::ShadowLong->value, RangeType::RealBody->value, 0, 1.),
            /* shadow is very long when it's longer than 2 times the real body */
            new CandleSetting(CandleSettingType::ShadowVeryLong->value, RangeType::RealBody->value, 0, 2.),
            /* shadow is short when it's shorter than half the average of the 10 previous candles' sum of shadows */
            new CandleSetting(CandleSettingType::ShadowShort->value, RangeType::Shadows->value, 10, 1.),
            /* shadow is very short when it's shorter than 10% the average of the 10 previous candles' high-low range */
            new CandleSetting(CandleSettingType::ShadowVeryShort->value, RangeType::HighLow->value, 10, 0.1),
            /* when measuring distance between parts of candles or width of gaps "near" means "<= 20% of the average of the 5 previous candles' high-low range" */
            new CandleSetting(CandleSettingType::Near->value, RangeType::HighLow->value, 5, 0.2),
            /* when measuring distance between parts of candles or width of gaps "far" means ">= 60% of the average of the 5 previous candles' high-low range" */
            new CandleSetting(CandleSettingType::Far->value, RangeType::HighLow->value, 5, 0.6),
            /* when measuring distance between parts of candles or width of gaps "equal" means "<= 5% of the average of the 5 previous candles' high-low range" */
            new CandleSetting(CandleSettingType::Equal->value, RangeType::HighLow->value, 5, 0.05),
        ];
        // Changed to correct array size to avoid the "Uncaught ErrorException: Undefined array key 22"
        // static::$unstablePeriod = \array_pad([], UnstablePeriodFunctionID::ALL - 2, 0);
        static::$unstablePeriod = \array_pad([], UnstablePeriodFunctionID::ALL->value, 0);
    }

    public static function setUnstablePeriod(int $functionID, int $unstablePeriod): void
    {
        static::$unstablePeriod[$functionID] = $unstablePeriod;
    }

    public static function getUnstablePeriod(int $functionID): int
    {
        return static::$unstablePeriod[$functionID];
    }

    public static function setCompatibility(int $compatibility): void
    {
        static::$compatibility = $compatibility;
    }

    public static function getCompatibility(): int
    {
        return static::$compatibility;
    }

    protected static function double(int $size): array
    {
        return \array_pad([], $size, 0.);
    }

    protected static function validateStartEndIndexes(int $startIdx, int $endIdx): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex->value;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex->value;
        }

        return ReturnCode::Success->value;
    }

    protected static function TA_INT_PO(
        int $startIdx,
        int $endIdx,
        array $inReal,
        int $optInFastPeriod,
        int $optInSlowPeriod,
        int $optInMethod_2,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outReal,
        array &$tempBuffer,
        bool $doPercentageOutput
    ): int {
        $outBegIdx1 = 0;
        $outNbElement1 = 0;
        $outBegIdx2 = 0;
        $outNbElement2 = 0;
        if ($optInSlowPeriod < $optInFastPeriod) {
            $tempInteger = $optInSlowPeriod;
            $optInSlowPeriod = $optInFastPeriod;
            $optInFastPeriod = $tempInteger;
        }
        $ReturnCode = OverlapStudies::movingAverage($startIdx, $endIdx, $inReal, $optInFastPeriod, $optInMethod_2, $outBegIdx2, $outNbElement2, $tempBuffer);
        if ($ReturnCode === ReturnCode::Success->value) {
            $ReturnCode = OverlapStudies::movingAverage($startIdx, $endIdx, $inReal, $optInSlowPeriod, $optInMethod_2, $outBegIdx1, $outNbElement1, $outReal);
            if ($ReturnCode === ReturnCode::Success->value) {
                $tempInteger = $outBegIdx1 - $outBegIdx2;
                if ($doPercentageOutput) {
                    for ($i = 0, $j = $tempInteger; $i < $outNbElement1; $i++, $j++) {
                        $tempReal = $outReal[$i];
                        if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                            $outReal[$i] = (($tempBuffer[$j] - $tempReal) / $tempReal) * 100.0;
                        } else {
                            $outReal[$i] = 0.0;
                        }
                    }
                } else {
                    for ($i = 0, $j = $tempInteger; $i < $outNbElement1; $i++, $j++) {
                        $outReal[$i] = $tempBuffer[$j] - $outReal[$i];
                    }
                }
                $outBegIdx = $outBegIdx1;
                $outNBElement = $outNbElement1;
            }
        }
        if ($ReturnCode !== ReturnCode::Success->value) {
            $outBegIdx = 0;
            $outNBElement = 0;
        }

        return $ReturnCode;
    }

    protected static function TA_INT_MACD(
        int $startIdx,
        int $endIdx,
        array $inReal,
        int $optInFastPeriod,
        int $optInSlowPeriod,
        int $optInSignalPeriod_2,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outMACD,
        array &$outMACDSignal,
        array &$outMACDHist
    ): int {
        //double[] $slowEMABuffer;
        //double[] $fastEMABuffer;
        //double $k1, $k2;
        //ReturnCode $ReturnCode;
        //int $tempInteger;
        $outBegIdx1 = 0;
        $outNbElement1 = 0;
        $outBegIdx2 = 0;
        $outNbElement2 = 0;
        //int $lookbackTotal, $lookbackSignal;
        //int $i;
        if ($optInSlowPeriod < $optInFastPeriod) {
            $tempInteger = $optInSlowPeriod;
            $optInSlowPeriod = $optInFastPeriod;
            $optInFastPeriod = $tempInteger;
        }
        if ($optInSlowPeriod !== 0) {
            $k1 = (2.0 / ((double)($optInSlowPeriod + 1)));
        } else {
            $optInSlowPeriod = 26;
            $k1 = 0.075;
        }
        if ($optInFastPeriod !== 0) {
            $k2 = (2.0 / ((double)($optInFastPeriod + 1)));
        } else {
            $optInFastPeriod = 12;
            $k2 = 0.15;
        }
        $lookbackSignal = Lookback::emaLookback($optInSignalPeriod_2);
        $lookbackTotal = $lookbackSignal;
        $lookbackTotal += Lookback::emaLookback($optInSlowPeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $tempInteger = ($endIdx - $startIdx) + 1 + $lookbackSignal;
        $fastEMABuffer = static::double($tempInteger);
        $slowEMABuffer = static::double($tempInteger);
        $tempInteger = $startIdx - $lookbackSignal;
        $ReturnCode = static::TA_INT_EMA($tempInteger, $endIdx, $inReal, $optInSlowPeriod, $k1, $outBegIdx1, $outNbElement1, $slowEMABuffer);
        if ($ReturnCode !== ReturnCode::Success->value) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        $ReturnCode = static::TA_INT_EMA($tempInteger, $endIdx, $inReal, $optInFastPeriod, $k2, $outBegIdx2, $outNbElement2, $fastEMABuffer);
        if ($ReturnCode !== ReturnCode::Success->value) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        if (($outBegIdx1 !== $tempInteger) ||
            ($outBegIdx2 !== $tempInteger) ||
            ($outNbElement1 !== $outNbElement2) ||
            ($outNbElement1 !== ($endIdx - $startIdx) + 1 + $lookbackSignal)) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::InternalError->value;
        }
        for ($i = 0; $i < $outNbElement1; $i++) {
            $fastEMABuffer[$i] -= $slowEMABuffer[$i];
        }
        //System::arraycopy($fastEMABuffer, $lookbackSignal, $outMACD, 0, ($endIdx - $startIdx) + 1);
        $outMACD = \array_slice($fastEMABuffer, $lookbackSignal, ($endIdx - $startIdx) + 1);
        $ReturnCode = static::TA_INT_EMA(0, $outNbElement1 - 1, $fastEMABuffer, $optInSignalPeriod_2, (2.0 / ((double)($optInSignalPeriod_2 + 1))), $outBegIdx2, $outNbElement2, $outMACDSignal);
        if ($ReturnCode !== ReturnCode::Success->value) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        for ($i = 0; $i < $outNbElement2; $i++) {
            $outMACDHist[$i] = $outMACD[$i] - $outMACDSignal[$i];
        }
        $outBegIdx = $startIdx;
        $outNBElement = $outNbElement2;

        return ReturnCode::Success->value;
    }

    protected static function TA_INT_EMA(int $startIdx, int $endIdx, $inReal, int $optInTimePeriod, float $optInK_1, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        //double $tempReal, $prevMA;
        //int $i, $today, $outIdx, $lookbackTotal;
        $lookbackTotal = Lookback::emaLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $outBegIdx = $startIdx;
        if ((static::$compatibility) === Compatibility::Default->value) {
            $today = $startIdx - $lookbackTotal;
            $i = $optInTimePeriod;
            $tempReal = 0.0;
            while ($i-- > 0) {
                $tempReal += $inReal[$today++];
            }
            $prevMA = $tempReal / $optInTimePeriod;
        } else {
            $prevMA = $inReal[0];
            $today = 1;
        }
        while ($today <= $startIdx) {
            $prevMA = (($inReal[$today++] - $prevMA) * $optInK_1) + $prevMA;
        }
        $outReal[0] = $prevMA;
        $outIdx = 1;
        while ($today <= $endIdx) {
            $prevMA = (($inReal[$today++] - $prevMA) * $optInK_1) + $prevMA;
            $outReal[$outIdx++] = $prevMA;
        }
        $outNBElement = $outIdx;

        return ReturnCode::Success->value;
    }

    protected static function TA_INT_SMA(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        //double $periodTotal, $tempReal;
        //int $i, $outIdx, $trailingIdx, $lookbackTotal;
        $lookbackTotal = ($optInTimePeriod - 1);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $periodTotal = 0;
        $trailingIdx = $startIdx - $lookbackTotal;
        $i = $trailingIdx;
        if ($optInTimePeriod > 1) {
            while ($i < $startIdx) {
                $periodTotal += $inReal[$i++];
            }
        }
        $outIdx = 0;
        do {
            $periodTotal += $inReal[$i++];
            $tempReal = $periodTotal;
            $periodTotal -= $inReal[$trailingIdx++];
            $outReal[$outIdx++] = $tempReal / $optInTimePeriod;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    protected static function TA_INT_stddev_using_precalc_ma(array $inReal, array $inMovAvg, int $inMovAvgBegIdx, int $inMovAvgNbElement, int $timePeriod, array &$output): int
    {
        //double $tempReal, $periodTotal2, $meanValue2;
        //int $outIdx;
        //int $startSum, $endSum;
        $startSum = 1 + $inMovAvgBegIdx - $timePeriod;
        $endSum = $inMovAvgBegIdx;
        $periodTotal2 = 0;
        for ($outIdx = $startSum; $outIdx < $endSum; $outIdx++) {
            $tempReal = $inReal[$outIdx];
            $tempReal *= $tempReal;
            $periodTotal2 += $tempReal;
        }
        for ($outIdx = 0; $outIdx < $inMovAvgNbElement; $outIdx++, $startSum++, $endSum++) {
            $tempReal = $inReal[$endSum];
            $tempReal *= $tempReal;
            $periodTotal2 += $tempReal;
            $meanValue2 = $periodTotal2 / $timePeriod;
            $tempReal = $inReal[$startSum];
            $tempReal *= $tempReal;
            $periodTotal2 -= $tempReal;
            $tempReal = $inMovAvg[$outIdx];
            $tempReal *= $tempReal;
            $meanValue2 -= $tempReal;
            if (!($meanValue2 < 0.00000001)) {
                $output[$outIdx] = sqrt($meanValue2);
            } else {
                $output[$outIdx] = 0.0;
            }
        }

        return ReturnCode::Success->value;
    }

    protected static function TA_INT_VAR(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        //double $tempReal, $periodTotal1, $periodTotal2, $meanValue1, $meanValue2;
        //int $i, $outIdx, $trailingIdx, $nbInitialElementNeeded;
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $periodTotal1 = 0;
        $periodTotal2 = 0;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $i = $trailingIdx;
        if ($optInTimePeriod > 1) {
            while ($i < $startIdx) {
                $tempReal = $inReal[$i++];
                $periodTotal1 += $tempReal;
                $tempReal *= $tempReal;
                $periodTotal2 += $tempReal;
            }
        }
        $outIdx = 0;
        do {
            $tempReal = $inReal[$i++];
            $periodTotal1 += $tempReal;
            $tempReal *= $tempReal;
            $periodTotal2 += $tempReal;
            $meanValue1 = $periodTotal1 / $optInTimePeriod;
            $meanValue2 = $periodTotal2 / $optInTimePeriod;
            $tempReal = $inReal[$trailingIdx++];
            $periodTotal1 -= $tempReal;
            $tempReal *= $tempReal;
            $periodTotal2 -= $tempReal;
            $outReal[$outIdx++] = $meanValue2 - $meanValue1 * $meanValue1;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }
}
