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

use LupeCode\phpTraderNative\TALib\Enum\CandleSettingType;
use LupeCode\phpTraderNative\TALib\Enum\RangeType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;

class PatternRecognition extends Core
{

    public static function cdl2Crows(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdl2CrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inOpen[$i] < $inOpen[$i - 1] && $inOpen[$i] > $inClose[$i - 1] &&
                $inClose[$i] > $inOpen[$i - 2] && $inClose[$i] < $inClose[$i - 2]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdl3BlackCrows(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdl3BlackCrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == 1 &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] < $inOpen[$i - 2] && $inOpen[$i - 1] > $inClose[$i - 2] &&
                $inOpen[$i] < $inOpen[$i - 1] && $inOpen[$i] > $inClose[$i - 1] &&
                $inHigh[$i - 3] > $inClose[$i - 2] &&
                $inClose[$i - 2] > $inClose[$i - 1] &&
                $inClose[$i - 1] > $inClose[$i]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdl3Inside(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdl3InsideLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) < ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) > ((($inClose[$i - 2]) < ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                ((($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 && ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 && $inClose[$i] < $inOpen[$i - 2])
                 ||
                 (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 && ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 && $inClose[$i] > $inOpen[$i - 2])
                )
            ) {
                $outInteger[$outIdx++] = -($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdl3LineStrike(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdl3LineStrikeLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $NearPeriodTotal[3] = 0;
        $NearPeriodTotal[2] = 0;
        $NearTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[3] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $NearPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                $inOpen[$i - 2] >= ((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[3] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i - 2] <= ((($inOpen[$i - 3]) > ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[3] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] >= ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] <= ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    (
                        ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                        $inClose[$i - 1] > $inClose[$i - 2] && $inClose[$i - 2] > $inClose[$i - 3] &&
                        $inOpen[$i] > $inClose[$i - 1] &&
                        $inClose[$i] < $inOpen[$i - 3]
                    ) ||
                    (
                        ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                        $inClose[$i - 1] < $inClose[$i - 2] && $inClose[$i - 2] < $inClose[$i - 3] &&
                        $inOpen[$i] < $inClose[$i - 1] &&
                        $inClose[$i] > $inOpen[$i - 3]
                    )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 3; $totIdx >= 2; --$totIdx) {
                $NearPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdl3Outside(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdl3OutsideLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                 $inClose[$i - 1] > $inOpen[$i - 2] && $inOpen[$i - 1] < $inClose[$i - 2] &&
                 $inClose[$i] > $inClose[$i - 1]
                )
                ||
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                 $inOpen[$i - 1] > $inClose[$i - 2] && $inClose[$i - 1] < $inOpen[$i - 2] &&
                 $inClose[$i] < $inClose[$i - 1]
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdl3StarsInSouth(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $ShadowVeryShortPeriodTotal = static::double(2);
        $lookbackTotal = Lookback::cdl3StarsInSouthLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $BodyShortPeriodTotal = 0;
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (abs($inClose[$i - 2] - $inOpen[$i - 2])) &&
                $inOpen[$i - 1] > $inClose[$i - 2] && $inOpen[$i - 1] <= $inHigh[$i - 2] &&
                $inLow[$i - 1] < $inClose[$i - 2] &&
                $inLow[$i - 1] >= $inLow[$i - 2] &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inLow[$i] > $inLow[$i - 1] && $inHigh[$i] < $inHigh[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))
                                    - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 2] - $inOpen[$BodyLongTrailingIdx - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 2] - $inLow[$BodyLongTrailingIdx - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 2] - ($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inClose[$BodyLongTrailingIdx - 2] : $inOpen[$BodyLongTrailingIdx - 2])) + (($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inOpen[$BodyLongTrailingIdx - 2] : $inClose[$BodyLongTrailingIdx - 2]) - $inLow[$BodyLongTrailingIdx - 2]) : 0)));
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))
                                      - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx - 2] - $inOpen[$ShadowLongTrailingIdx - 2])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx - 2] - $inLow[$ShadowLongTrailingIdx - 2]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx - 2] - ($inClose[$ShadowLongTrailingIdx - 2] >= $inOpen[$ShadowLongTrailingIdx - 2] ? $inClose[$ShadowLongTrailingIdx - 2] : $inOpen[$ShadowLongTrailingIdx - 2])) + (($inClose[$ShadowLongTrailingIdx - 2] >= $inOpen[$ShadowLongTrailingIdx - 2] ? $inOpen[$ShadowLongTrailingIdx - 2] : $inClose[$ShadowLongTrailingIdx - 2]) - $inLow[$ShadowLongTrailingIdx - 2]) : 0)));
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                     - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdl3WhiteSoldiers(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $ShadowVeryShortPeriodTotal = static::double(3);
        $NearPeriodTotal = static::double(3);
        $FarPeriodTotal = static::double(3);
        $lookbackTotal = Lookback::cdl3WhiteSoldiersLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $NearPeriodTotal[2] = 0;
        $NearPeriodTotal[1] = 0;
        $NearPeriodTotal[0] = 0;
        $NearTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $FarPeriodTotal[2] = 0;
        $FarPeriodTotal[1] = 0;
        $FarPeriodTotal[0] = 0;
        $FarTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Far->value]->avgPeriod);
        $BodyShortPeriodTotal = 0;
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $NearPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $FarTrailingIdx;
        while ($i < $startIdx) {
            $FarPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $FarPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] > $inClose[$i - 1] && $inClose[$i - 1] > $inClose[$i - 2] &&
                $inOpen[$i - 1] > $inOpen[$i - 2] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] > $inOpen[$i - 1] &&
                $inOpen[$i] <= $inClose[$i - 1] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (abs($inClose[$i - 2] - $inOpen[$i - 2])) - ((static::$candleSettings[CandleSettingType::Far->value]->factor) * ((static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) != 0.0 ? $FarPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (abs($inClose[$i - 1] - $inOpen[$i - 1])) - ((static::$candleSettings[CandleSettingType::Far->value]->factor) * ((static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) != 0.0 ? $FarPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $FarPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                            - ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$FarTrailingIdx - $totIdx] - $inOpen[$FarTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$FarTrailingIdx - $totIdx] - $inLow[$FarTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$FarTrailingIdx - $totIdx] - ($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inClose[$FarTrailingIdx - $totIdx] : $inOpen[$FarTrailingIdx - $totIdx])) + (($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inOpen[$FarTrailingIdx - $totIdx] : $inClose[$FarTrailingIdx - $totIdx]) - $inLow[$FarTrailingIdx - $totIdx]) : 0)));
                $NearPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
            $FarTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlAbandonedBaby(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outInteger
    ): int {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::cdlAbandonedBabyLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyDojiPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyDojiTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                  ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                  $inClose[$i] < $inClose[$i - 2] - (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration &&
                  ($inLow[$i - 1] > $inHigh[$i - 2]) &&
                  ($inHigh[$i] < $inLow[$i - 1])
                 )
                 ||
                 (
                     ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                     ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                     $inClose[$i] > $inClose[$i - 2] + (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration &&
                     ($inHigh[$i - 1] < $inLow[$i - 2]) &&
                     ($inLow[$i] > $inHigh[$i - 1])
                 )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlAdvanceBlock(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $ShadowShortPeriodTotal = static::double(3);
        $ShadowLongPeriodTotal = static::double(2);
        $NearPeriodTotal = static::double(3);
        $FarPeriodTotal = static::double(3);
        $lookbackTotal = Lookback::cdlAdvanceBlockLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowShortPeriodTotal[2] = 0;
        $ShadowShortPeriodTotal[1] = 0;
        $ShadowShortPeriodTotal[0] = 0;
        $ShadowShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod);
        $ShadowLongPeriodTotal[1] = 0;
        $ShadowLongPeriodTotal[0] = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $NearPeriodTotal[2] = 0;
        $NearPeriodTotal[1] = 0;
        $NearPeriodTotal[0] = 0;
        $NearTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $FarPeriodTotal[2] = 0;
        $FarPeriodTotal[1] = 0;
        $FarPeriodTotal[0] = 0;
        $FarTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Far->value]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $ShadowShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowShortPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowShortPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowLongPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $NearPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $FarTrailingIdx;
        while ($i < $startIdx) {
            $FarPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $FarPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 1] && $inClose[$i - 1] > $inClose[$i - 2] &&
                $inOpen[$i - 1] > $inOpen[$i - 2] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] > $inOpen[$i - 1] &&
                $inOpen[$i] <= $inClose[$i - 1] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) < ((static::$candleSettings[CandleSettingType::ShadowShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) != 0.0 ? $ShadowShortPeriodTotal[2] / (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    (
                        (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (abs($inClose[$i - 2] - $inOpen[$i - 2])) - ((static::$candleSettings[CandleSettingType::Far->value]->factor) * ((static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) != 0.0 ? $FarPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    ) ||
                    (
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) - ((static::$candleSettings[CandleSettingType::Far->value]->factor) * ((static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) != 0.0 ? $FarPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Far->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    ) ||
                    (
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) &&
                        (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (abs($inClose[$i - 2] - $inOpen[$i - 2])) &&
                        (
                            ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) != 0.0 ? $ShadowShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) ||
                            ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::ShadowShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) != 0.0 ? $ShadowShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                        )
                    ) ||
                    (
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) &&
                        ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    )
                )
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                    - ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowShortTrailingIdx - $totIdx] - $inOpen[$ShadowShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowShortTrailingIdx - $totIdx] - $inLow[$ShadowShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowShortTrailingIdx - $totIdx] - ($inClose[$ShadowShortTrailingIdx - $totIdx] >= $inOpen[$ShadowShortTrailingIdx - $totIdx] ? $inClose[$ShadowShortTrailingIdx - $totIdx] : $inOpen[$ShadowShortTrailingIdx - $totIdx])) + (($inClose[$ShadowShortTrailingIdx - $totIdx] >= $inOpen[$ShadowShortTrailingIdx - $totIdx] ? $inOpen[$ShadowShortTrailingIdx - $totIdx] : $inClose[$ShadowShortTrailingIdx - $totIdx]) - $inLow[$ShadowShortTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $ShadowLongPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                   - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx - $totIdx] - $inOpen[$ShadowLongTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx - $totIdx] - $inLow[$ShadowLongTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx - $totIdx] - ($inClose[$ShadowLongTrailingIdx - $totIdx] >= $inOpen[$ShadowLongTrailingIdx - $totIdx] ? $inClose[$ShadowLongTrailingIdx - $totIdx] : $inOpen[$ShadowLongTrailingIdx - $totIdx])) + (($inClose[$ShadowLongTrailingIdx - $totIdx] >= $inOpen[$ShadowLongTrailingIdx - $totIdx] ? $inOpen[$ShadowLongTrailingIdx - $totIdx] : $inClose[$ShadowLongTrailingIdx - $totIdx]) - $inLow[$ShadowLongTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $FarPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                            - ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$FarTrailingIdx - $totIdx] - $inOpen[$FarTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$FarTrailingIdx - $totIdx] - $inLow[$FarTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::Far->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$FarTrailingIdx - $totIdx] - ($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inClose[$FarTrailingIdx - $totIdx] : $inOpen[$FarTrailingIdx - $totIdx])) + (($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inOpen[$FarTrailingIdx - $totIdx] : $inClose[$FarTrailingIdx - $totIdx]) - $inLow[$FarTrailingIdx - $totIdx]) : 0)));
                $NearPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 2] - $inOpen[$BodyLongTrailingIdx - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 2] - $inLow[$BodyLongTrailingIdx - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 2] - ($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inClose[$BodyLongTrailingIdx - 2] : $inOpen[$BodyLongTrailingIdx - 2])) + (($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inOpen[$BodyLongTrailingIdx - 2] : $inClose[$BodyLongTrailingIdx - 2]) - $inLow[$BodyLongTrailingIdx - 2]) : 0)));
            $i++;
            $ShadowShortTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $NearTrailingIdx++;
            $FarTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlBeltHold(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlBeltHoldLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                        (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    ) ||
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                        ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    )
                )) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlBreakaway(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlBreakawayLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 4] - $inOpen[$i - 4])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) &&
                ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (
                    (($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == -1 &&
                     (((($inOpen[$i - 3]) > ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) < ((($inOpen[$i - 4]) < ($inClose[$i - 4])) ? ($inOpen[$i - 4]) : ($inClose[$i - 4]))) &&
                     $inHigh[$i - 2] < $inHigh[$i - 3] && $inLow[$i - 2] < $inLow[$i - 3] &&
                     $inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] < $inLow[$i - 2] &&
                     $inClose[$i] > $inOpen[$i - 3] && $inClose[$i] < $inClose[$i - 4]
                    )
                    ||
                    (($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == 1 &&
                     (((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) > ((($inOpen[$i - 4]) > ($inClose[$i - 4])) ? ($inOpen[$i - 4]) : ($inClose[$i - 4]))) &&
                     $inHigh[$i - 2] > $inHigh[$i - 3] && $inLow[$i - 2] > $inLow[$i - 3] &&
                     $inHigh[$i - 1] > $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                     $inClose[$i] < $inOpen[$i - 3] && $inClose[$i] > $inClose[$i - 4]
                    )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))
                                    - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 4] - $inOpen[$BodyLongTrailingIdx - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 4] - $inLow[$BodyLongTrailingIdx - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 4] - ($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inClose[$BodyLongTrailingIdx - 4] : $inOpen[$BodyLongTrailingIdx - 4])) + (($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inOpen[$BodyLongTrailingIdx - 4] : $inClose[$BodyLongTrailingIdx - 4]) - $inLow[$BodyLongTrailingIdx - 4]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlClosingMarubozu(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlClosingMarubozuLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                        ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    ) ||
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                        (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    )
                )) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlConcealBabysWall(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $ShadowVeryShortPeriodTotal = static::double(4);
        $lookbackTotal = Lookback::cdlConcealBabysWallLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal[3] = 0;
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[3] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $ShadowVeryShortPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == -1 &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[3] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[3] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inHigh[$i - 1] > $inClose[$i - 2] &&
                $inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 3; $totIdx >= 1; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlCounterAttack(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $BodyLongPeriodTotal = static::double(2);
        $lookbackTotal = Lookback::cdlCounterAttackLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $BodyLongPeriodTotal[1] = 0;
        $BodyLongPeriodTotal[0] = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] <= $inClose[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 1] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlDarkCloudCover(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outInteger
    ): int {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::cdlDarkCloudCoverLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inOpen[$i] > $inHigh[$i - 1] &&
                $inClose[$i] > $inOpen[$i - 1] &&
                $inClose[$i] < $inClose[$i - 1] - (abs($inClose[$i - 1] - $inOpen[$i - 1])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlDoji(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyDojiPeriodTotal = 0;
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlDojiStar(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlDojiStarLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyDojiPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && (((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))))
                 ||
                 (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && (((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))))
                )) {
                $outInteger[$outIdx++] = -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlDragonflyDoji(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlDragonflyDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyDojiPeriodTotal = 0;
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlEngulfing(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlEngulfingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 && ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                 $inClose[$i] > $inOpen[$i - 1] && $inOpen[$i] < $inClose[$i - 1]
                )
                ||
                (($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 && ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                 $inOpen[$i] > $inClose[$i - 1] && $inClose[$i] < $inOpen[$i - 1]
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlEveningDojiStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outInteger
    ): int {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::cdlEveningDojiStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyDojiPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyDojiTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inClose[$i] < $inClose[$i - 2] - (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlEveningStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outInteger
    ): int {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::cdlEveningStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyShortPeriodTotal2 = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $BodyShortPeriodTotal2 += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i + 1] - $inOpen[$i + 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i + 1] - $inLow[$i + 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i + 1] - ($inClose[$i + 1] >= $inOpen[$i + 1] ? $inClose[$i + 1] : $inOpen[$i + 1])) + (($inClose[$i + 1] >= $inOpen[$i + 1] ? $inOpen[$i + 1] : $inClose[$i + 1]) - $inLow[$i + 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal2 / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inClose[$i] < $inClose[$i - 2] - (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $BodyShortPeriodTotal2 += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx + 1] - $inOpen[$BodyShortTrailingIdx + 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx + 1] - $inLow[$BodyShortTrailingIdx + 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx + 1] - ($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inClose[$BodyShortTrailingIdx + 1] : $inOpen[$BodyShortTrailingIdx + 1])) + (($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inOpen[$BodyShortTrailingIdx + 1] : $inClose[$BodyShortTrailingIdx + 1]) - $inLow[$BodyShortTrailingIdx + 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlGapSideSideWhite(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlGapSideSideWhiteLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $NearPeriodTotal = 0;
        $EqualPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (
                (
                    ((((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) && (((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))))
                    ||
                    ((((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) && (((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))))
                ) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                (abs($inClose[$i] - $inOpen[$i])) >= (abs($inClose[$i - 1] - $inOpen[$i - 1])) - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= (abs($inClose[$i - 1] - $inOpen[$i - 1])) + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inOpen[$i - 1] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] <= $inOpen[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = ((((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) ? 100 : -100);
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - 1] - $inOpen[$NearTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - 1] - $inLow[$NearTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - 1] - ($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inClose[$NearTrailingIdx - 1] : $inOpen[$NearTrailingIdx - 1])) + (($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inOpen[$NearTrailingIdx - 1] : $inClose[$NearTrailingIdx - 1]) - $inLow[$NearTrailingIdx - 1]) : 0)));
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $i++;
            $NearTrailingIdx++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlGravestoneDoji(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlGravestoneDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyDojiPeriodTotal = 0;
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHammer(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHammerLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $NearPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx - 1) {
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) <= $inLow[$i - 1] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                      - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx] - $inOpen[$NearTrailingIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx] - $inLow[$NearTrailingIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx] - ($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inClose[$NearTrailingIdx] : $inOpen[$NearTrailingIdx])) + (($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inOpen[$NearTrailingIdx] : $inClose[$NearTrailingIdx]) - $inLow[$NearTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHangingMan(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHangingManLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $NearPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx - 1) {
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) >= $inHigh[$i - 1] - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                      - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx] - $inOpen[$NearTrailingIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx] - $inLow[$NearTrailingIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx] - ($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inClose[$NearTrailingIdx] : $inOpen[$NearTrailingIdx])) + (($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inOpen[$NearTrailingIdx] : $inClose[$NearTrailingIdx]) - $inLow[$NearTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHarami(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHaramiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i]) > ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) < ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) > ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1]))
            ) {
                $outInteger[$outIdx++] = -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHaramiCross(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHaramiCrossLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyDojiPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i]) > ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) < ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) > ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1]))
            ) {
                $outInteger[$outIdx++] = -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHighWave(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHighWaveLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $ShadowPeriodTotal = 0;
        $ShadowTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowTrailingIdx;
        while ($i < $startIdx) {
            $ShadowPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowTrailingIdx] - $inOpen[$ShadowTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowTrailingIdx] - $inLow[$ShadowTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowTrailingIdx] - ($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inClose[$ShadowTrailingIdx] : $inOpen[$ShadowTrailingIdx])) + (($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inOpen[$ShadowTrailingIdx] : $inClose[$ShadowTrailingIdx]) - $inLow[$ShadowTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHikkake(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHikkakeLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $patternIdx = 0;
        $patternResult = 0;
        $i = $startIdx - 3;
        while ($i < $startIdx) {
            if ($inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1])
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1])
                )
            ) {
                $patternResult = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx = $i;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $patternIdx = 0;
            }
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ($inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1])
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1])
                )
            ) {
                $patternResult = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx = $i;
                $outInteger[$outIdx++] = $patternResult;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $outInteger[$outIdx++] = $patternResult + 100 * ($patternResult > 0 ? 1 : -1);
                $patternIdx = 0;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHikkakeMod(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHikkakeModLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $NearPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - 3 - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $i = $NearTrailingIdx;
        while ($i < $startIdx - 3) {
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $patternIdx = 0;
        $patternResult = 0;
        $i = $startIdx - 3;
        while ($i < $startIdx) {
            if ($inHigh[$i - 2] < $inHigh[$i - 3] && $inLow[$i - 2] > $inLow[$i - 3] &&
                $inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1] &&
                  $inClose[$i - 2] <= $inLow[$i - 2] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                 )
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1] &&
                  $inClose[$i - 2] >= $inHigh[$i - 2] - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                 )
                )
            ) {
                $patternResult = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx = $i;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $patternIdx = 0;
            }
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - 2] - $inOpen[$NearTrailingIdx - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - 2] - $inLow[$NearTrailingIdx - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - 2] - ($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inClose[$NearTrailingIdx - 2] : $inOpen[$NearTrailingIdx - 2])) + (($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inOpen[$NearTrailingIdx - 2] : $inClose[$NearTrailingIdx - 2]) - $inLow[$NearTrailingIdx - 2]) : 0)));
            $NearTrailingIdx++;
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ($inHigh[$i - 2] < $inHigh[$i - 3] && $inLow[$i - 2] > $inLow[$i - 3] &&
                $inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1] &&
                  $inClose[$i - 2] <= $inLow[$i - 2] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                 )
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1] &&
                  $inClose[$i - 2] >= $inHigh[$i - 2] - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                 )
                )
            ) {
                $patternResult = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx = $i;
                $outInteger[$outIdx++] = $patternResult;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $outInteger[$outIdx++] = $patternResult + 100 * ($patternResult > 0 ? 1 : -1);
                $patternIdx = 0;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - 2] - $inOpen[$NearTrailingIdx - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - 2] - $inLow[$NearTrailingIdx - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - 2] - ($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inClose[$NearTrailingIdx - 2] : $inOpen[$NearTrailingIdx - 2])) + (($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inOpen[$NearTrailingIdx - 2] : $inClose[$NearTrailingIdx - 2]) - $inLow[$NearTrailingIdx - 2]) : 0)));
            $NearTrailingIdx++;
            $i++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlHomingPigeon(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlHomingPigeonLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] < $inOpen[$i - 1] &&
                $inClose[$i] > $inClose[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlIdentical3Crows(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $ShadowVeryShortPeriodTotal = static::double(3);
        $EqualPeriodTotal = static::double(3);
        $lookbackTotal = Lookback::cdlIdentical3CrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $EqualPeriodTotal[2] = 0;
        $EqualPeriodTotal[1] = 0;
        $EqualPeriodTotal[0] = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $EqualPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i - 2] > $inClose[$i - 1] &&
                $inClose[$i - 1] > $inClose[$i] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] >= $inClose[$i - 2] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] <= $inClose[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inClose[$i - 1] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $EqualPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                              - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - $totIdx] - $inOpen[$EqualTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - $totIdx] - $inLow[$EqualTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - $totIdx] - ($inClose[$EqualTrailingIdx - $totIdx] >= $inOpen[$EqualTrailingIdx - $totIdx] ? $inClose[$EqualTrailingIdx - $totIdx] : $inOpen[$EqualTrailingIdx - $totIdx])) + (($inClose[$EqualTrailingIdx - $totIdx] >= $inOpen[$EqualTrailingIdx - $totIdx] ? $inOpen[$EqualTrailingIdx - $totIdx] : $inClose[$EqualTrailingIdx - $totIdx]) - $inLow[$EqualTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlInNeck(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlInNeckLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] <= $inClose[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 1]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                    - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlInvertedHammer(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlInvertedHammerLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])))) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                      - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlKicking(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $ShadowVeryShortPeriodTotal = static::double(2);
        $BodyLongPeriodTotal = static::double(2);
        $lookbackTotal = Lookback::cdlKickingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $BodyLongPeriodTotal[1] = 0;
        $BodyLongPeriodTotal[0] = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && ($inLow[$i] > $inHigh[$i - 1]))
                    ||
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && ($inHigh[$i] < $inLow[$i - 1]))
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
                $ShadowVeryShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlKickingByLength(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $ShadowVeryShortPeriodTotal = static::double(2);
        $BodyLongPeriodTotal = static::double(2);
        $lookbackTotal = Lookback::cdlKickingByLengthLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $BodyLongPeriodTotal[1] = 0;
        $BodyLongPeriodTotal[0] = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && ($inLow[$i] > $inHigh[$i - 1]))
                    ||
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && ($inHigh[$i] < $inLow[$i - 1]))
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[((abs($inClose[$i] - $inOpen[$i])) > (abs($inClose[$i - 1] - $inOpen[$i - 1])) ? $i : $i - 1)] >= $inOpen[((abs($inClose[$i] - $inOpen[$i])) > (abs($inClose[$i - 1] - $inOpen[$i - 1])) ? $i : $i - 1)] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
                $ShadowVeryShortPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlLadderBottom(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlLadderBottomLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == -1 && ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == -1 && ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                $inOpen[$i - 4] > $inOpen[$i - 3] && $inOpen[$i - 3] > $inOpen[$i - 2] &&
                $inClose[$i - 4] > $inClose[$i - 3] && $inClose[$i - 3] > $inClose[$i - 2] &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] > $inOpen[$i - 1] &&
                $inClose[$i] > $inHigh[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - 1] - $inOpen[$ShadowVeryShortTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - $inLow[$ShadowVeryShortTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - ($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inClose[$ShadowVeryShortTrailingIdx - 1] : $inOpen[$ShadowVeryShortTrailingIdx - 1])) + (($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inOpen[$ShadowVeryShortTrailingIdx - 1] : $inClose[$ShadowVeryShortTrailingIdx - 1]) - $inLow[$ShadowVeryShortTrailingIdx - 1]) : 0)));
            $i++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlLongLeggedDoji(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlLongLeggedDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyDojiPeriodTotal = 0;
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ((($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                 ||
                 ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                )
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlLongLine(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlLongLineLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $ShadowPeriodTotal = 0;
        $ShadowTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowTrailingIdx;
        while ($i < $startIdx) {
            $ShadowPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowTrailingIdx] - $inOpen[$ShadowTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowTrailingIdx] - $inLow[$ShadowTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowTrailingIdx] - ($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inClose[$ShadowTrailingIdx] : $inOpen[$ShadowTrailingIdx])) + (($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inOpen[$ShadowTrailingIdx] : $inClose[$ShadowTrailingIdx]) - $inLow[$ShadowTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlMarubozu(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlMarubozuLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlMatchingLow(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlMatchingLowLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inClose[$i] <= $inClose[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 1] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlMatHold(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outInteger
    ): int {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $BodyPeriodTotal = static::double(5);
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::cdlMatHoldLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal[4] = 0;
        $BodyPeriodTotal[3] = 0;
        $BodyPeriodTotal[2] = 0;
        $BodyPeriodTotal[1] = 0;
        $BodyPeriodTotal[0] = 0;
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[3] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $BodyPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $BodyPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[4] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (
                (abs($inClose[$i - 4] - $inOpen[$i - 4])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[4] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 3] - $inOpen[$i - 3])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[3] / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[2] / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[1] / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == 1 &&
                ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                (((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) > ((($inOpen[$i - 4]) > ($inClose[$i - 4])) ? ($inOpen[$i - 4]) : ($inClose[$i - 4]))) &&
                ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) < $inClose[$i - 4] &&
                ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < $inClose[$i - 4] &&
                ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) > $inClose[$i - 4] - (abs($inClose[$i - 4] - $inOpen[$i - 4])) * $optInPenetration &&
                ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > $inClose[$i - 4] - (abs($inClose[$i - 4] - $inOpen[$i - 4])) * $optInPenetration &&
                ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) < $inOpen[$i - 3] &&
                ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) < ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                $inOpen[$i] > $inClose[$i - 1] &&
                $inClose[$i] > (((((($inHigh[$i - 3]) > ($inHigh[$i - 2])) ? ($inHigh[$i - 3]) : ($inHigh[$i - 2]))) > ($inHigh[$i - 1])) ? (((($inHigh[$i - 3]) > ($inHigh[$i - 2])) ? ($inHigh[$i - 3]) : ($inHigh[$i - 2]))) : ($inHigh[$i - 1]))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal[4] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 4] - $inOpen[$BodyLongTrailingIdx - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 4] - $inLow[$BodyLongTrailingIdx - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 4] - ($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inClose[$BodyLongTrailingIdx - 4] : $inOpen[$BodyLongTrailingIdx - 4])) + (($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inOpen[$BodyLongTrailingIdx - 4] : $inClose[$BodyLongTrailingIdx - 4]) - $inLow[$BodyLongTrailingIdx - 4]) : 0)));
            for ($totIdx = 3; $totIdx >= 1; --$totIdx) {
                $BodyPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx - $totIdx] - $inOpen[$BodyShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - $inLow[$BodyShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - ($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inClose[$BodyShortTrailingIdx - $totIdx] : $inOpen[$BodyShortTrailingIdx - $totIdx])) + (($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inOpen[$BodyShortTrailingIdx - $totIdx] : $inClose[$BodyShortTrailingIdx - $totIdx]) - $inLow[$BodyShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $BodyShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlMorningDojiStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outInteger
    ): int {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::cdlMorningDojiStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyDojiPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyDojiTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 2] + (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlMorningStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        int &$outBegIdx,
        int &$outNBElement,
        array &$outInteger
    ): int {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::cdlMorningStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyShortPeriodTotal2 = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $BodyShortPeriodTotal2 += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i + 1] - $inOpen[$i + 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i + 1] - $inLow[$i + 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i + 1] - ($inClose[$i + 1] >= $inOpen[$i + 1] ? $inClose[$i + 1] : $inOpen[$i + 1])) + (($inClose[$i + 1] >= $inOpen[$i + 1] ? $inOpen[$i + 1] : $inClose[$i + 1]) - $inLow[$i + 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal2 / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 2] + (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $BodyShortPeriodTotal2 += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx + 1] - $inOpen[$BodyShortTrailingIdx + 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx + 1] - $inLow[$BodyShortTrailingIdx + 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx + 1] - ($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inClose[$BodyShortTrailingIdx + 1] : $inOpen[$BodyShortTrailingIdx + 1])) + (($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inOpen[$BodyShortTrailingIdx + 1] : $inClose[$BodyShortTrailingIdx + 1]) - $inLow[$BodyShortTrailingIdx + 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlOnNeck(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlOnNeckLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] <= $inLow[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inLow[$i - 1] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                    - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlPiercing(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $BodyLongPeriodTotal = static::double(2);
        $lookbackTotal = Lookback::cdlPiercingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal[1] = 0;
        $BodyLongPeriodTotal[0] = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] < $inOpen[$i - 1] &&
                $inClose[$i] > $inClose[$i - 1] + (abs($inClose[$i - 1] - $inOpen[$i - 1])) * 0.5
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlRickshawMan(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlRickshawManLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyDojiPeriodTotal = 0;
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $NearPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    ((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i]))
                    <= $inLow[$i] + ($inHigh[$i] - $inLow[$i]) / 2 + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    &&
                    ((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i]))
                    >= $inLow[$i] + ($inHigh[$i] - $inLow[$i]) / 2 - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                )
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx] - $inOpen[$NearTrailingIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx] - $inLow[$NearTrailingIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx] - ($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inClose[$NearTrailingIdx] : $inOpen[$NearTrailingIdx])) + (($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inOpen[$NearTrailingIdx] : $inClose[$NearTrailingIdx]) - $inLow[$NearTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlRiseFall3Methods(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $BodyPeriodTotal = static::double(5);
        $lookbackTotal = Lookback::cdlRiseFall3MethodsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal[4] = 0;
        $BodyPeriodTotal[3] = 0;
        $BodyPeriodTotal[2] = 0;
        $BodyPeriodTotal[1] = 0;
        $BodyPeriodTotal[0] = 0;
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[3] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $BodyPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $BodyPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[4] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)));
            $BodyPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (
                (abs($inClose[$i - 4] - $inOpen[$i - 4])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[4] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 3] - $inOpen[$i - 3])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[3] / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 3] - $inLow[$i - 3]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[2] / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[1] / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal[0] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == -($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) &&
                ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                ((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) < $inHigh[$i - 4] && ((($inOpen[$i - 3]) > ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) > $inLow[$i - 4] &&
                ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) < $inHigh[$i - 4] && ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) > $inLow[$i - 4] &&
                ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < $inHigh[$i - 4] && ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > $inLow[$i - 4] &&
                $inClose[$i - 2] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) < $inClose[$i - 3] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) &&
                $inClose[$i - 1] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) < $inClose[$i - 2] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) &&
                $inOpen[$i] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) > $inClose[$i - 1] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) &&
                $inClose[$i] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) > $inClose[$i - 4] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1)
            ) {
                $outInteger[$outIdx++] = 100 * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1);
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal[4] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 4] - $inLow[$i - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 4] - $inOpen[$BodyLongTrailingIdx - 4])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 4] - $inLow[$BodyLongTrailingIdx - 4]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 4] - ($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inClose[$BodyLongTrailingIdx - 4] : $inOpen[$BodyLongTrailingIdx - 4])) + (($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inOpen[$BodyLongTrailingIdx - 4] : $inClose[$BodyLongTrailingIdx - 4]) - $inLow[$BodyLongTrailingIdx - 4]) : 0)));
            for ($totIdx = 3; $totIdx >= 1; --$totIdx) {
                $BodyPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx - $totIdx] - $inOpen[$BodyShortTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - $inLow[$BodyShortTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - ($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inClose[$BodyShortTrailingIdx - $totIdx] : $inOpen[$BodyShortTrailingIdx - $totIdx])) + (($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inOpen[$BodyShortTrailingIdx - $totIdx] : $inClose[$BodyShortTrailingIdx - $totIdx]) - $inLow[$BodyShortTrailingIdx - $totIdx]) : 0)));
            }
            $BodyPeriodTotal[0] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $i++;
            $BodyShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlSeparatingLines(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlSeparatingLinesLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                $inOpen[$i] <= $inOpen[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inOpen[$i - 1] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (
                    (($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                     (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    )
                    ||
                    (($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                     ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                    )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $BodyLongTrailingIdx++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlShootingStar(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlShootingStarLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > ((static::$candleSettings[CandleSettingType::ShadowLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])))) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                      - ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlShortLine(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlShortLineLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $ShadowPeriodTotal = 0;
        $ShadowTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowTrailingIdx;
        while ($i < $startIdx) {
            $ShadowPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < ((static::$candleSettings[CandleSettingType::ShadowShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowTrailingIdx] - $inOpen[$ShadowTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowTrailingIdx] - $inLow[$ShadowTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowTrailingIdx] - ($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inClose[$ShadowTrailingIdx] : $inOpen[$ShadowTrailingIdx])) + (($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inOpen[$ShadowTrailingIdx] : $inClose[$ShadowTrailingIdx]) - $inLow[$ShadowTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlSpinningTop(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlSpinningTopLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (abs($inClose[$i] - $inOpen[$i])) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (abs($inClose[$i] - $inOpen[$i]))
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlStalledPattern(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $BodyLongPeriodTotal = static::double(3);
        $NearPeriodTotal = static::double(3);
        $lookbackTotal = Lookback::cdlStalledPatternLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal[2] = 0;
        $BodyLongPeriodTotal[1] = 0;
        $BodyLongPeriodTotal[0] = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortPeriodTotal = 0;
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $NearPeriodTotal[2] = 0;
        $NearPeriodTotal[1] = 0;
        $NearPeriodTotal[0] = 0;
        $NearTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $BodyLongPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[2] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $NearPeriodTotal[1] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 1] && $inClose[$i - 1] > $inClose[$i - 2] &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[2] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] > $inOpen[$i - 2] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inClose[$i - 1] - (abs($inClose[$i] - $inOpen[$i])) - ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
                $NearPeriodTotal[$totIdx] += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx - 1] - $inOpen[$ShadowVeryShortTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - $inLow[$ShadowVeryShortTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - ($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inClose[$ShadowVeryShortTrailingIdx - 1] : $inOpen[$ShadowVeryShortTrailingIdx - 1])) + (($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inOpen[$ShadowVeryShortTrailingIdx - 1] : $inClose[$ShadowVeryShortTrailingIdx - 1]) - $inLow[$ShadowVeryShortTrailingIdx - 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlStickSandwich(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlStickSandwichLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inLow[$i - 1] > $inClose[$i - 2] &&
                $inClose[$i] <= $inClose[$i - 2] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 2] - ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 2] - $inOpen[$EqualTrailingIdx - 2])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 2] - $inLow[$EqualTrailingIdx - 2]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 2] - ($inClose[$EqualTrailingIdx - 2] >= $inOpen[$EqualTrailingIdx - 2] ? $inClose[$EqualTrailingIdx - 2] : $inOpen[$EqualTrailingIdx - 2])) + (($inClose[$EqualTrailingIdx - 2] >= $inOpen[$EqualTrailingIdx - 2] ? $inOpen[$EqualTrailingIdx - 2] : $inClose[$EqualTrailingIdx - 2]) - $inLow[$EqualTrailingIdx - 2]) : 0)));
            $i++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlTakuri(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlTakuriLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyDojiPeriodTotal = 0;
        $BodyDojiTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod);
        $ShadowVeryLongPeriodTotal = 0;
        $ShadowVeryLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod);
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->factor) * ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod) != 0.0 ? $ShadowVeryLongPeriodTotal / (static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $ShadowVeryLongPeriodTotal += ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                          - ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$ShadowVeryLongTrailingIdx] - $inOpen[$ShadowVeryLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$ShadowVeryLongTrailingIdx] - $inLow[$ShadowVeryLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$ShadowVeryLongTrailingIdx] - ($inClose[$ShadowVeryLongTrailingIdx] >= $inOpen[$ShadowVeryLongTrailingIdx] ? $inClose[$ShadowVeryLongTrailingIdx] : $inOpen[$ShadowVeryLongTrailingIdx])) + (($inClose[$ShadowVeryLongTrailingIdx] >= $inOpen[$ShadowVeryLongTrailingIdx] ? $inOpen[$ShadowVeryLongTrailingIdx] : $inClose[$ShadowVeryLongTrailingIdx]) - $inLow[$ShadowVeryLongTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $ShadowVeryLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlTasukiGap(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlTasukiGapLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $NearPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod);
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (
                (
                    (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                    ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                    ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                    $inOpen[$i] < $inClose[$i - 1] && $inOpen[$i] > $inOpen[$i - 1] &&
                    $inClose[$i] < $inOpen[$i - 1] &&
                    $inClose[$i] > ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                    abs((abs($inClose[$i - 1] - $inOpen[$i - 1])) - (abs($inClose[$i] - $inOpen[$i]))) < ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                ) ||
                (
                    (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                    ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                    ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                    $inOpen[$i] < $inOpen[$i - 1] && $inOpen[$i] > $inClose[$i - 1] &&
                    $inClose[$i] > $inOpen[$i - 1] &&
                    $inClose[$i] < ((($inClose[$i - 2]) < ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                    abs((abs($inClose[$i - 1] - $inOpen[$i - 1])) - (abs($inClose[$i] - $inOpen[$i]))) < ((static::$candleSettings[CandleSettingType::Near->value]->factor) * ((static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) != 0.0 ? $NearPeriodTotal / (static::$candleSettings[CandleSettingType::Near->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $NearPeriodTotal += ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$NearTrailingIdx - 1] - $inOpen[$NearTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$NearTrailingIdx - 1] - $inLow[$NearTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Near->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$NearTrailingIdx - 1] - ($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inClose[$NearTrailingIdx - 1] : $inOpen[$NearTrailingIdx - 1])) + (($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inOpen[$NearTrailingIdx - 1] : $inClose[$NearTrailingIdx - 1]) - $inLow[$NearTrailingIdx - 1]) : 0)));
            $i++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlThrusting(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlThrustingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] > $inClose[$i - 1] + ((static::$candleSettings[CandleSettingType::Equal->value]->factor) * ((static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) != 0.0 ? $EqualPeriodTotal / (static::$candleSettings[CandleSettingType::Equal->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                $inClose[$i] <= $inClose[$i - 1] + (abs($inClose[$i - 1] - $inOpen[$i - 1])) * 0.5
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::Equal->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                    - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlTristar(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlTristarLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod);
        $i = $BodyTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= ((static::$candleSettings[CandleSettingType::BodyDoji->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) != 0.0 ? $BodyPeriodTotal / (static::$candleSettings[CandleSettingType::BodyDoji->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0))) {
                $outInteger[$outIdx] = 0;
                if ((((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                    &&
                    ((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))
                ) {
                    $outInteger[$outIdx] = -100;
                }
                if ((((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                    &&
                    ((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))
                ) {
                    $outInteger[$outIdx] = +100;
                }
                $outIdx++;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyDoji->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlUnique3River(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlUnique3RiverLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                $inClose[$i - 1] > $inClose[$i - 2] && $inOpen[$i - 1] <= $inOpen[$i - 2] &&
                $inLow[$i - 1] < $inLow[$i - 2] &&
                (abs($inClose[$i] - $inOpen[$i])) < ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] > $inLow[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlUpsideGap2Crows(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlUpsideGap2CrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $BodyLongPeriodTotal = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - 1 - (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod);
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i] - $inOpen[$i])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i] - $inLow[$i]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > ((static::$candleSettings[CandleSettingType::BodyLong->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / (static::$candleSettings[CandleSettingType::BodyLong->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= ((static::$candleSettings[CandleSettingType::BodyShort->value]->factor) * ((static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / (static::$candleSettings[CandleSettingType::BodyShort->value]->avgPeriod) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inOpen[$i] > $inOpen[$i - 1] && $inClose[$i] < $inClose[$i - 1] &&
                $inClose[$i] > $inClose[$i - 2]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 2] - $inLow[$i - 2]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyLong->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$i - 1] - $inLow[$i - 1]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::RealBody->value ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::HighLow->value ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : ((static::$candleSettings[CandleSettingType::BodyShort->value]->rangeType) == RangeType::Shadows->value ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }

    public static function cdlXSideGap3Methods(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $lookbackTotal = Lookback::cdlXSideGap3MethodsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx = 0;
            $outNBElement = 0;

            return ReturnCode::Success->value;
        }
        $i = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                $inOpen[$i] < ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                $inOpen[$i] > ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                $inClose[$i] < ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                $inClose[$i] > ((($inClose[$i - 2]) < ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                ((
                     ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                     (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                 ) ||
                 (
                     ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                     (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                 )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx = $startIdx;

        return ReturnCode::Success->value;
    }
}
