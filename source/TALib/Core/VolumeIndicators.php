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

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;
use LupeCode\phpTraderNative\TALib\Enum\UnstablePeriodFunctionID;

class VolumeIndicators extends Core
{
    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param float[] $inClose
     * @param float[] $inVolume
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function ad(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $nbBar        = $endIdx - $startIdx + 1;
        $outNBElement = $nbBar;
        $outBegIdx    = $startIdx;
        $currentBar   = $startIdx;
        $outIdx       = 0;
        $ad           = 0.0;
        while ($nbBar != 0) {
            $high  = $inHigh[$currentBar];
            $low   = $inLow[$currentBar];
            $tmp   = $high - $low;
            $close = $inClose[$currentBar];
            if ($tmp > 0.0) {
                $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$currentBar]);
            }
            $outReal[$outIdx++] = $ad;
            $currentBar++;
            $nbBar--;
        }

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param float[] $inClose
     * @param float[] $inVolume
     * @param int     $optInFastPeriod
     * @param int     $optInSlowPeriod
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function adOsc(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, int $optInFastPeriod, int $optInSlowPeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 3;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 10;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInFastPeriod < $optInSlowPeriod) {
            $slowestPeriod = $optInSlowPeriod;
        } else {
            $slowestPeriod = $optInFastPeriod;
        }
        $lookbackTotal = Lookback::emaLookback($slowestPeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outBegIdx       = $startIdx;
        $today           = $startIdx - $lookbackTotal;
        $ad              = 0.0;
        $fastK           = ((double)2.0 / ((double)($optInFastPeriod + 1)));
        $one_minus_fastK = 1.0 - $fastK;
        $slowK           = ((double)2.0 / ((double)($optInSlowPeriod + 1)));
        $one_minus_slowK = 1.0 - $slowK;
        {
            $high  = $inHigh[$today];
            $low   = $inLow[$today];
            $tmp   = $high - $low;
            $close = $inClose[$today];
            if ($tmp > 0.0) {
                $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$today]);
            }
            $today++;
        };
        $fastEMA = $ad;
        $slowEMA = $ad;
        while ($today < $startIdx) {
            {
                $high  = $inHigh[$today];
                $low   = $inLow[$today];
                $tmp   = $high - $low;
                $close = $inClose[$today];
                if ($tmp > 0.0) {
                    $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$today]);
                }
                $today++;
            };
            $fastEMA = ($fastK * $ad) + ($one_minus_fastK * $fastEMA);
            $slowEMA = ($slowK * $ad) + ($one_minus_slowK * $slowEMA);
        }
        $outIdx = 0;
        while ($today <= $endIdx) {
            {
                $high  = $inHigh[$today];
                $low   = $inLow[$today];
                $tmp   = $high - $low;
                $close = $inClose[$today];
                if ($tmp > 0.0) {
                    $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$today]);
                }
                $today++;
            };
            $fastEMA            = ($fastK * $ad) + ($one_minus_fastK * $fastEMA);
            $slowEMA            = ($slowK * $ad) + ($one_minus_slowK * $slowEMA);
            $outReal[$outIdx++] = $fastEMA - $slowEMA;
        }
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param float[] $inClose
     * @param int     $optInTimePeriod
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function atr(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $outBegIdx1    = 0;
        $outNbElement1 = 0;
        $prevATRTemp   = static::double(1);
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $outBegIdx     = 0;
        $outNBElement  = 0;
        $lookbackTotal = Lookback::atrLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        if ($optInTimePeriod <= 1) {
            return VolatilityIndicators::trueRange($startIdx, $endIdx, $inHigh, $inLow, $inClose, $outBegIdx, $outNBElement, $outReal);
        }
        $tempBuffer = static::double($lookbackTotal + ($endIdx - $startIdx) + 1);
        $retCode    = VolatilityIndicators::trueRange(($startIdx - $lookbackTotal + 1), $endIdx, $inHigh, $inLow, $inClose, $outBegIdx1, $outNbElement1, $tempBuffer);
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $retCode = static::TA_INT_SMA($optInTimePeriod - 1, $optInTimePeriod - 1, $tempBuffer, $optInTimePeriod, $outBegIdx1, $outNbElement1, $prevATRTemp);
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $prevATR = $prevATRTemp[0];
        $today   = $optInTimePeriod;
        $outIdx  = (static::$unstablePeriod[UnstablePeriodFunctionID::ATR]);
        while ($outIdx != 0) {
            $prevATR *= $optInTimePeriod - 1;
            $prevATR += $tempBuffer[$today++];
            $prevATR /= $optInTimePeriod;
            $outIdx--;
        }
        $outIdx     = 1;
        $outReal[0] = $prevATR;
        $nbATR      = ($endIdx - $startIdx) + 1;
        while (--$nbATR != 0) {
            $prevATR            *= $optInTimePeriod - 1;
            $prevATR            += $tempBuffer[$today++];
            $prevATR            /= $optInTimePeriod;
            $outReal[$outIdx++] = $prevATR;
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return $retCode;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param array $inVolume
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function obv(int $startIdx, int $endIdx, array $inReal, array &$inVolume, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $prevOBV  = $inVolume[$startIdx];
        $prevReal = $inReal[$startIdx];
        $outIdx   = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $tempReal = $inReal[$i];
            if ($tempReal > $prevReal) {
                $prevOBV += $inVolume[$i];
            } elseif ($tempReal < $prevReal) {
                $prevOBV -= $inVolume[$i];
            }
            $outReal[$outIdx++] = $prevOBV;
            $prevReal           = $tempReal;
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }
}
