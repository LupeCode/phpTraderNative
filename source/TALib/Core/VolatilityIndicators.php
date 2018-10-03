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

class VolatilityIndicators extends Core
{

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param array $inClose
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function natr(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
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
        $lookbackTotal = Lookback::natrLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        if ($optInTimePeriod <= 1) {
            return self::trueRange(
                $startIdx, $endIdx,
                $inHigh, $inLow, $inClose,
                $outBegIdx, $outNBElement, $outReal
            );
        }
        $tempBuffer = static::double($lookbackTotal + ($endIdx - $startIdx) + 1);
        $retCode    = self::trueRange(
            ($startIdx - $lookbackTotal + 1), $endIdx,
                                              $inHigh, $inLow, $inClose,
                                              $outBegIdx1, $outNbElement1,
                                              $tempBuffer
        );
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $retCode = static::TA_INT_SMA(
            $optInTimePeriod - 1,
            $optInTimePeriod - 1,
            $tempBuffer, $optInTimePeriod,
            $outBegIdx1, $outNbElement1,
            $prevATRTemp
        );
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $prevATR = $prevATRTemp[0];
        $today   = $optInTimePeriod;
        $outIdx  = (static::$unstablePeriod[UnstablePeriodFunctionID::NATR]);
        while ($outIdx != 0) {
            $prevATR *= $optInTimePeriod - 1;
            $prevATR += $tempBuffer[$today++];
            $prevATR /= $optInTimePeriod;
            $outIdx--;
        }
        $outIdx    = 1;
        $tempValue = $inClose[$today];
        if (!(((-0.00000001) < $tempValue) && ($tempValue < 0.00000001))) {
            $outReal[0] = ($prevATR / $tempValue) * 100.0;
        } else {
            $outReal[0] = 0.0;
        }
        $nbATR = ($endIdx - $startIdx) + 1;
        while (--$nbATR != 0) {
            $prevATR   *= $optInTimePeriod - 1;
            $prevATR   += $tempBuffer[$today++];
            $prevATR   /= $optInTimePeriod;
            $tempValue = $inClose[$today];
            if (!(((-0.00000001) < $tempValue) && ($tempValue < 0.00000001))) {
                $outReal[$outIdx] = ($prevATR / $tempValue) * 100.0;
            } else {
                $outReal[0] = 0.0;
            }
            $outIdx++;
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return $retCode;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param array $inClose
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function trueRange(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ($startIdx < 1) {
            $startIdx = 1;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx = 0;
        $today  = $startIdx;
        while ($today <= $endIdx) {
            $tempLT   = $inLow[$today];
            $tempHT   = $inHigh[$today];
            $tempCY   = $inClose[$today - 1];
            $greatest = $tempHT - $tempLT;
            $val2     = abs($tempCY - $tempHT);
            if ($val2 > $greatest) {
                $greatest = $val2;
            }
            $val3 = abs($tempCY - $tempLT);
            if ($val3 > $greatest) {
                $greatest = $val3;
            }
            $outReal[$outIdx++] = $greatest;
            $today++;
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }
}
