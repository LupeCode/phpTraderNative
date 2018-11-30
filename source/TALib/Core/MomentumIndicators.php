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

use LupeCode\phpTraderNative\TALib\Classes\MoneyFlow;
use LupeCode\phpTraderNative\TALib\Enum\Compatibility;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;
use LupeCode\phpTraderNative\TALib\Enum\UnstablePeriodFunctionID;

class MomentumIndicators extends Core
{
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
    public static function adx(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = (2 * $optInTimePeriod) + (static::$unstablePeriod[UnstablePeriodFunctionID::ADX]) - 1;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outBegIdx   = $today = $startIdx;
        $prevMinusDM = 0.0;
        $prevPlusDM  = 0.0;
        $prevTR      = 0.0;
        $today       = $startIdx - $lookbackTotal;
        $prevHigh    = $inHigh[$today];
        $prevLow     = $inLow[$today];
        $prevClose   = $inClose[$today];
        $i           = $optInTimePeriod - 1;
        while ($i-- > 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    += $tempReal;
            $prevClose = $inClose[$today];
        }
        $sumDX = 0.0;
        $i     = $optInTimePeriod;
        while ($i-- > 0) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
                $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
                $tempReal = $minusDI + $plusDI;
                if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                    $sumDX += (100.0 * (abs($minusDI - $plusDI) / $tempReal));
                }
            }
        }
        $prevADX = ($sumDX / $optInTimePeriod);
        $i       = (static::$unstablePeriod[UnstablePeriodFunctionID::ADX]);
        while ($i-- > 0) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
                $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
                $tempReal = $minusDI + $plusDI;
                if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                    $tempReal = (100.0 * (abs($minusDI - $plusDI) / $tempReal));
                    $prevADX  = ((($prevADX * ($optInTimePeriod - 1)) + $tempReal) / $optInTimePeriod);
                }
            }
        }
        $outReal[0] = $prevADX;
        $outIdx     = 1;
        while ($today < $endIdx) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
                $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
                $tempReal = $minusDI + $plusDI;
                if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                    $tempReal = (100.0 * (abs($minusDI - $plusDI) / $tempReal));
                    $prevADX  = ((($prevADX * ($optInTimePeriod - 1)) + $tempReal) / $optInTimePeriod);
                }
            }
            $outReal[$outIdx++] = $prevADX;
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
    public static function adxr(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $adxrLookback = Lookback::adxrLookback($optInTimePeriod);
        if ($startIdx < $adxrLookback) {
            $startIdx = $adxrLookback;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $adx        = static::double($endIdx - $startIdx + $optInTimePeriod);
        $ReturnCode = self::adx($startIdx - ($optInTimePeriod - 1), $endIdx, $inHigh, $inLow, $inClose, $optInTimePeriod, $outBegIdx, $outNBElement, $adx);
        if ($ReturnCode != ReturnCode::Success) {
            return $ReturnCode;
        }
        $i         = $optInTimePeriod - 1;
        $j         = 0;
        $outIdx    = 0;
        $nbElement = $endIdx - $startIdx + 2;
        while (--$nbElement != 0) {
            $outReal[$outIdx++] = (($adx[$i++] + $adx[$j++]) / 2.0);
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inReal
     * @param int     $optInFastPeriod
     * @param int     $optInSlowPeriod
     * @param int     $optInMAType
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function apo(int $startIdx, int $endIdx, array $inReal, int $optInFastPeriod, int $optInSlowPeriod, int $optInMAType, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $tempBuffer = static::double($endIdx - $startIdx + 1);
        $ReturnCode = static::TA_INT_PO($startIdx, $endIdx, $inReal, $optInFastPeriod, $optInSlowPeriod, $optInMAType, $outBegIdx, $outNBElement, $outReal, $tempBuffer, false);

        return $ReturnCode;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param int     $optInTimePeriod
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outAroonDown
     * @param float[] $outAroonUp
     *
     * @return int
     */
    public static function aroon(int $startIdx, int $endIdx, array $inHigh, array $inLow, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outAroonDown, array &$outAroonUp): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        $lowestIdx   = -1;
        $highestIdx  = -1;
        $lowest      = 0.0;
        $highest     = 0.0;
        $factor      = (double)100.0 / (double)$optInTimePeriod;
        while ($today <= $endIdx) {
            $tmp = $inLow[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inLow[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inLow[$i];
                    if ($tmp <= $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $tmp = $inHigh[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inHigh[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inHigh[$i];
                    if ($tmp >= $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $outAroonUp[$outIdx]   = $factor * ($optInTimePeriod - ($today - $highestIdx));
            $outAroonDown[$outIdx] = $factor * ($optInTimePeriod - ($today - $lowestIdx));
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param int     $optInTimePeriod
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function aroonOsc(int $startIdx, int $endIdx, array $inHigh, array $inLow, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        $lowestIdx   = -1;
        $highestIdx  = -1;
        $lowest      = 0.0;
        $highest     = 0.0;
        $factor      = (double)100.0 / (double)$optInTimePeriod;
        while ($today <= $endIdx) {
            $tmp = $inLow[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inLow[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inLow[$i];
                    if ($tmp <= $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $tmp = $inHigh[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inHigh[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inHigh[$i];
                    if ($tmp >= $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $aroon            = $factor * ($highestIdx - $lowestIdx);
            $outReal[$outIdx] = $aroon;
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inOpen
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param float[] $inClose
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function bop(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $outIdx = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $tempReal = $inHigh[$i] - $inLow[$i];
            if (($tempReal < 0.00000001)) {
                $outReal[$outIdx++] = 0.0;
            } else {
                $outReal[$outIdx++] = ($inClose[$i] - $inOpen[$i]) / $tempReal;
            }
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

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
    public static function cci(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $circBuffer_Idx = 0;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = ($optInTimePeriod - 1);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        {
            if ($optInTimePeriod <= 0) {
                return ReturnCode::AllocError;
            }
            $circBuffer        = static::double($optInTimePeriod);
            $maxIdx_circBuffer = ($optInTimePeriod - 1);
        };
        $i = $startIdx - $lookbackTotal;
        if ($optInTimePeriod > 1) {
            while ($i < $startIdx) {
                $circBuffer[$circBuffer_Idx] = ($inHigh[$i] + $inLow[$i] + $inClose[$i]) / 3;
                $i++;
                {
                    $circBuffer_Idx++;
                    if ($circBuffer_Idx > $maxIdx_circBuffer) {
                        $circBuffer_Idx = 0;
                    }
                };
            }
        }
        $outIdx = 0;
        do {
            $lastValue                   = ($inHigh[$i] + $inLow[$i] + $inClose[$i]) / 3;
            $circBuffer[$circBuffer_Idx] = $lastValue;
            $theAverage                  = 0;
            for ($j = 0; $j < $optInTimePeriod; $j++) {
                $theAverage += $circBuffer[$j];
            }
            $theAverage /= $optInTimePeriod;
            $tempReal2  = 0;
            for ($j = 0; $j < $optInTimePeriod; $j++) {
                $tempReal2 += abs($circBuffer[$j] - $theAverage);
            }
            $tempReal = $lastValue - $theAverage;
            if (($tempReal != 0.0) && ($tempReal2 != 0.0)) {
                $outReal[$outIdx++] = $tempReal / (0.015 * ($tempReal2 / $optInTimePeriod));
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            {
                $circBuffer_Idx++;
                if ($circBuffer_Idx > $maxIdx_circBuffer) {
                    $circBuffer_Idx = 0;
                }
            };
            $i++;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function cmo(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $outBegIdx     = 0;
        $outNBElement  = 0;
        $lookbackTotal = Lookback::cmoLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        $outIdx = 0;
        if ($optInTimePeriod == 1) {
            $outBegIdx    = $startIdx;
            $i            = ($endIdx - $startIdx) + 1;
            $outNBElement = $i;
            $outReal      = \array_slice($inReal, 0, $i);

            return ReturnCode::Success;
        }
        $today          = $startIdx - $lookbackTotal;
        $prevValue      = $inReal[$today];
        $unstablePeriod = (static::$unstablePeriod[UnstablePeriodFunctionID::CMO]);
        if (($unstablePeriod == 0) &&
            ((static::$compatibility) == Compatibility::Metastock)) {
            $savePrevValue = $prevValue;
            $prevGain      = 0.0;
            $prevLoss      = 0.0;
            for ($i = $optInTimePeriod; $i > 0; $i--) {
                $tempValue1 = $inReal[$today++];
                $tempValue2 = $tempValue1 - $prevValue;
                $prevValue  = $tempValue1;
                if ($tempValue2 < 0) {
                    $prevLoss -= $tempValue2;
                } else {
                    $prevGain += $tempValue2;
                }
            }
            $tempValue1 = $prevLoss / $optInTimePeriod;
            $tempValue2 = $prevGain / $optInTimePeriod;
            $tempValue3 = $tempValue2 - $tempValue1;
            $tempValue4 = $tempValue1 + $tempValue2;
            if (!(((-0.00000001) < $tempValue4) && ($tempValue4 < 0.00000001))) {
                $outReal[$outIdx++] = 100 * ($tempValue3 / $tempValue4);
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            if ($today > $endIdx) {
                $outBegIdx    = $startIdx;
                $outNBElement = $outIdx;

                return ReturnCode::Success;
            }
            $today     -= $optInTimePeriod;
            $prevValue = $savePrevValue;
        }
        $prevGain = 0.0;
        $prevLoss = 0.0;
        $today++;
        for ($i = $optInTimePeriod; $i > 0; $i--) {
            $tempValue1 = $inReal[$today++];
            $tempValue2 = $tempValue1 - $prevValue;
            $prevValue  = $tempValue1;
            if ($tempValue2 < 0) {
                $prevLoss -= $tempValue2;
            } else {
                $prevGain += $tempValue2;
            }
        }
        $prevLoss /= $optInTimePeriod;
        $prevGain /= $optInTimePeriod;
        if ($today > $startIdx) {
            $tempValue1 = $prevGain + $prevLoss;
            if (!(((-0.00000001) < $tempValue1) && ($tempValue1 < 0.00000001))) {
                $outReal[$outIdx++] = 100.0 * (($prevGain - $prevLoss) / $tempValue1);
            } else {
                $outReal[$outIdx++] = 0.0;
            }
        } else {
            while ($today < $startIdx) {
                $tempValue1 = $inReal[$today];
                $tempValue2 = $tempValue1 - $prevValue;
                $prevValue  = $tempValue1;
                $prevLoss   *= ($optInTimePeriod - 1);
                $prevGain   *= ($optInTimePeriod - 1);
                if ($tempValue2 < 0) {
                    $prevLoss -= $tempValue2;
                } else {
                    $prevGain += $tempValue2;
                }
                $prevLoss /= $optInTimePeriod;
                $prevGain /= $optInTimePeriod;
                $today++;
            }
        }
        while ($today <= $endIdx) {
            $tempValue1 = $inReal[$today++];
            $tempValue2 = $tempValue1 - $prevValue;
            $prevValue  = $tempValue1;
            $prevLoss   *= ($optInTimePeriod - 1);
            $prevGain   *= ($optInTimePeriod - 1);
            if ($tempValue2 < 0) {
                $prevLoss -= $tempValue2;
            } else {
                $prevGain += $tempValue2;
            }
            $prevLoss   /= $optInTimePeriod;
            $prevGain   /= $optInTimePeriod;
            $tempValue1 = $prevGain + $prevLoss;
            if (!(((-0.00000001) < $tempValue1) && ($tempValue1 < 0.00000001))) {
                $outReal[$outIdx++] = 100.0 * (($prevGain - $prevLoss) / $tempValue1);
            } else {
                $outReal[$outIdx++] = 0.0;
            }
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

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
    public static function dx(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInTimePeriod > 1) {
            $lookbackTotal = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::DX]);
        } else {
            $lookbackTotal = 2;
        }
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $outBegIdx   = $today = $startIdx;
        $prevMinusDM = 0.0;
        $prevPlusDM  = 0.0;
        $prevTR      = 0.0;
        $today       = $startIdx - $lookbackTotal;
        $prevHigh    = $inHigh[$today];
        $prevLow     = $inLow[$today];
        $prevClose   = $inClose[$today];
        $i           = $optInTimePeriod - 1;
        while ($i-- > 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    += $tempReal;
            $prevClose = $inClose[$today];
        }
        $i = (static::$unstablePeriod[UnstablePeriodFunctionID::DX]) + 1;
        while ($i-- != 0) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
        }
        if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
            $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
            $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
            $tempReal = $minusDI + $plusDI;
            if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                $outReal[0] = (100.0 * (abs($minusDI - $plusDI) / $tempReal));
            } else {
                $outReal[0] = 0.0;
            }
        } else {
            $outReal[0] = 0.0;
        }
        $outIdx = 1;
        while ($today < $endIdx) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
                $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
                $tempReal = $minusDI + $plusDI;
                if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                    $outReal[$outIdx] = (100.0 * (abs($minusDI - $plusDI) / $tempReal));
                } else {
                    $outReal[$outIdx] = $outReal[$outIdx - 1];
                }
            } else {
                $outReal[$outIdx] = $outReal[$outIdx - 1];
            }
            $outIdx++;
        }
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInFastPeriod
     * @param int   $optInSlowPeriod
     * @param int   $optInSignalPeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outMACD
     * @param array $outMACDSignal
     * @param array $outMACDHist
     *
     * @return int
     */
    public static function macd(int $startIdx, int $endIdx, array $inReal, int $optInFastPeriod, int $optInSlowPeriod, int $optInSignalPeriod, int &$outBegIdx, int &$outNBElement, array &$outMACD, array &$outMACDSignal, array &$outMACDHist): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSignalPeriod == (PHP_INT_MIN)) {
            $optInSignalPeriod = 9;
        } elseif (((int)$optInSignalPeriod < 1) || ((int)$optInSignalPeriod > 100000)) {
            return ReturnCode::BadParam;
        }

        return static::TA_INT_MACD(
            $startIdx, $endIdx, $inReal,
            $optInFastPeriod,
            $optInSlowPeriod,
            $optInSignalPeriod,
            $outBegIdx,
            $outNBElement,
            $outMACD,
            $outMACDSignal,
            $outMACDHist
        );
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInFastPeriod
     * @param int   $optInFastMAType
     * @param int   $optInSlowPeriod
     * @param int   $optInSlowMAType
     * @param int   $optInSignalPeriod
     * @param int   $optInSignalMAType
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outMACD
     * @param array $outMACDSignal
     * @param array $outMACDHist
     *
     * @return int
     */
    public static function macdExt(int $startIdx, int $endIdx, array $inReal, int $optInFastPeriod, int $optInFastMAType, int $optInSlowPeriod, int $optInSlowMAType, int $optInSignalPeriod, int $optInSignalMAType, int &$outBegIdx, int &$outNBElement, array &$outMACD, array &$outMACDSignal, array &$outMACDHist): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $outBegIdx1    = 0;
        $outNbElement1 = 0;
        $outBegIdx2    = 0;
        $outNbElement2 = 0;
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSignalPeriod == (PHP_INT_MIN)) {
            $optInSignalPeriod = 9;
        } elseif (((int)$optInSignalPeriod < 1) || ((int)$optInSignalPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInSlowPeriod < $optInFastPeriod) {
            $tempInteger     = $optInSlowPeriod;
            $optInSlowPeriod = $optInFastPeriod;
            $optInFastPeriod = $tempInteger;
            $tempMAType      = $optInSlowMAType;
            $optInSlowMAType = $optInFastMAType;
            $optInFastMAType = $tempMAType;
        }
        $lookbackLargest = Lookback::movingAverageLookback($optInFastPeriod, $optInFastMAType);
        $tempInteger     = Lookback::movingAverageLookback($optInSlowPeriod, $optInSlowMAType);
        if ($tempInteger > $lookbackLargest) {
            $lookbackLargest = $tempInteger;
        }
        $lookbackSignal = Lookback::movingAverageLookback($optInSignalPeriod, $optInSignalMAType);
        $lookbackTotal  = $lookbackSignal + $lookbackLargest;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $tempInteger  = ($endIdx - $startIdx) + 1 + $lookbackSignal;
        $fastMABuffer = static::double($tempInteger);
        $slowMABuffer = static::double($tempInteger);
        $tempInteger  = $startIdx - $lookbackSignal;
        $ReturnCode   = OverlapStudies::movingAverage(
            $tempInteger, $endIdx,
            $inReal, $optInSlowPeriod, $optInSlowMAType,
            $outBegIdx1, $outNbElement1,
            $slowMABuffer
        );
        if ($ReturnCode != ReturnCode::Success) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        $ReturnCode = OverlapStudies::movingAverage(
            $tempInteger, $endIdx,
            $inReal, $optInFastPeriod, $optInFastMAType,
            $outBegIdx2, $outNbElement2,
            $fastMABuffer
        );
        if ($ReturnCode != ReturnCode::Success) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        if (($outBegIdx1 != $tempInteger) ||
            ($outBegIdx2 != $tempInteger) ||
            ($outNbElement1 != $outNbElement2) ||
            ($outNbElement1 != ($endIdx - $startIdx) + 1 + $lookbackSignal)) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return (ReturnCode::InternalError);
        }
        for ($i = 0; $i < $outNbElement1; $i++) {
            $fastMABuffer[$i] = $fastMABuffer[$i] - $slowMABuffer[$i];
        }
        //System::arraycopy($fastMABuffer, $lookbackSignal, $outMACD, 0, ($endIdx - $startIdx) + 1);
        $outMACD    = \array_slice($fastMABuffer, $lookbackSignal, ($endIdx - $startIdx) + 1);
        $ReturnCode = OverlapStudies::movingAverage(
            0, $outNbElement1 - 1,
            $fastMABuffer, $optInSignalPeriod, $optInSignalMAType,
            $outBegIdx2, $outNbElement2, $outMACDSignal
        );
        if ($ReturnCode != ReturnCode::Success) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        for ($i = 0; $i < $outNbElement2; $i++) {
            $outMACDHist[$i] = $outMACD[$i] - $outMACDSignal[$i];
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outNbElement2;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInSignalPeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outMACD
     * @param array $outMACDSignal
     * @param array $outMACDHist
     *
     * @return int
     */
    public static function macdFix(int $startIdx, int $endIdx, array $inReal, int $optInSignalPeriod, int &$outBegIdx, int &$outNBElement, array &$outMACD, array &$outMACDSignal, array &$outMACDHist): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInSignalPeriod == (PHP_INT_MIN)) {
            $optInSignalPeriod = 9;
        } elseif (((int)$optInSignalPeriod < 1) || ((int)$optInSignalPeriod > 100000)) {
            return ReturnCode::BadParam;
        }

        return static::TA_INT_MACD(
            $startIdx, $endIdx, $inReal,
            0,
            0,
            $optInSignalPeriod,
            $outBegIdx,
            $outNBElement,
            $outMACD,
            $outMACDSignal,
            $outMACDHist
        );
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param array $inClose
     * @param array $inVolume
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function mfi(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array &$inVolume, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $money_flow_Idx    = 0;
        $maxIdx_money_flow = (50 - 1);
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        {
            if ($optInTimePeriod <= 0) {
                return ReturnCode::AllocError;
            }
            $money_flow = \array_pad([], $optInTimePeriod, new MoneyFlow());
            for ($_money_flow_index = 0; $_money_flow_index < $optInTimePeriod; $_money_flow_index++) {
                $money_flow[$_money_flow_index] = new MoneyFlow();
            }
            $maxIdx_money_flow = ($optInTimePeriod - 1);
        };
        $outBegIdx     = 0;
        $outNBElement  = 0;
        $lookbackTotal = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::MFI]);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        $outIdx    = 0;
        $today     = $startIdx - $lookbackTotal;
        $prevValue = ($inHigh[$today] + $inLow[$today] + $inClose[$today]) / 3.0;
        $posSumMF  = 0.0;
        $negSumMF  = 0.0;
        $today++;
        for ($i = $optInTimePeriod; $i > 0; $i--) {
            $tempValue1 = ($inHigh[$today] + $inLow[$today] + $inClose[$today]) / 3.0;
            $tempValue2 = $tempValue1 - $prevValue;
            $prevValue  = $tempValue1;
            $tempValue1 *= $inVolume[$today++];
            if ($tempValue2 < 0) {
                ($money_flow[$money_flow_Idx])->negative = $tempValue1;
                $negSumMF                                += $tempValue1;
                ($money_flow[$money_flow_Idx])->positive = 0.0;
            } elseif ($tempValue2 > 0) {
                ($money_flow[$money_flow_Idx])->positive = $tempValue1;
                $posSumMF                                += $tempValue1;
                ($money_flow[$money_flow_Idx])->negative = 0.0;
            } else {
                ($money_flow[$money_flow_Idx])->positive = 0.0;
                ($money_flow[$money_flow_Idx])->negative = 0.0;
            }
            {
                $money_flow_Idx++;
                if ($money_flow_Idx > $maxIdx_money_flow) {
                    $money_flow_Idx = 0;
                }
            };
        }
        if ($today > $startIdx) {
            $tempValue1 = $posSumMF + $negSumMF;
            if ($tempValue1 < 1.0) {
                $outReal[$outIdx++] = 0.0;
            } else {
                $outReal[$outIdx++] = 100.0 * ($posSumMF / $tempValue1);
            }
        } else {
            while ($today < $startIdx) {
                $posSumMF   -= ($money_flow[$money_flow_Idx])->positive;
                $negSumMF   -= ($money_flow[$money_flow_Idx])->negative;
                $tempValue1 = ($inHigh[$today] + $inLow[$today] + $inClose[$today]) / 3.0;
                $tempValue2 = $tempValue1 - $prevValue;
                $prevValue  = $tempValue1;
                $tempValue1 *= $inVolume[$today++];
                if ($tempValue2 < 0) {
                    ($money_flow[$money_flow_Idx])->negative = $tempValue1;
                    $negSumMF                                += $tempValue1;
                    ($money_flow[$money_flow_Idx])->positive = 0.0;
                } elseif ($tempValue2 > 0) {
                    ($money_flow[$money_flow_Idx])->positive = $tempValue1;
                    $posSumMF                                += $tempValue1;
                    ($money_flow[$money_flow_Idx])->negative = 0.0;
                } else {
                    ($money_flow[$money_flow_Idx])->positive = 0.0;
                    ($money_flow[$money_flow_Idx])->negative = 0.0;
                }
                {
                    $money_flow_Idx++;
                    if ($money_flow_Idx > $maxIdx_money_flow) {
                        $money_flow_Idx = 0;
                    }
                };
            }
        }
        while ($today <= $endIdx) {
            $posSumMF   -= ($money_flow[$money_flow_Idx])->positive;
            $negSumMF   -= ($money_flow[$money_flow_Idx])->negative;
            $tempValue1 = ($inHigh[$today] + $inLow[$today] + $inClose[$today]) / 3.0;
            $tempValue2 = $tempValue1 - $prevValue;
            $prevValue  = $tempValue1;
            $tempValue1 *= $inVolume[$today++];
            if ($tempValue2 < 0) {
                ($money_flow[$money_flow_Idx])->negative = $tempValue1;
                $negSumMF                                += $tempValue1;
                ($money_flow[$money_flow_Idx])->positive = 0.0;
            } elseif ($tempValue2 > 0) {
                ($money_flow[$money_flow_Idx])->positive = $tempValue1;
                $posSumMF                                += $tempValue1;
                ($money_flow[$money_flow_Idx])->negative = 0.0;
            } else {
                ($money_flow[$money_flow_Idx])->positive = 0.0;
                ($money_flow[$money_flow_Idx])->negative = 0.0;
            }
            $tempValue1 = $posSumMF + $negSumMF;
            if ($tempValue1 < 1.0) {
                $outReal[$outIdx++] = 0.0;
            } else {
                $outReal[$outIdx++] = 100.0 * ($posSumMF / $tempValue1);
            }
            {
                $money_flow_Idx++;
                if ($money_flow_Idx > $maxIdx_money_flow) {
                    $money_flow_Idx = 0;
                }
            };
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

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
    public static function minusDI(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInTimePeriod > 1) {
            $lookbackTotal = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::MinusDI]);
        } else {
            $lookbackTotal = 1;
        }
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx = 0;
        if ($optInTimePeriod <= 1) {
            $outBegIdx = $startIdx;
            $today     = $startIdx - 1;
            $prevHigh  = $inHigh[$today];
            $prevLow   = $inLow[$today];
            $prevClose = $inClose[$today];
            while ($today < $endIdx) {
                $today++;
                $tempReal = $inHigh[$today];
                $diffP    = $tempReal - $prevHigh;
                $prevHigh = $tempReal;
                $tempReal = $inLow[$today];
                $diffM    = $prevLow - $tempReal;
                $prevLow  = $tempReal;
                if (($diffM > 0) && ($diffP < $diffM)) {
                    {
                        $tempReal  = $prevHigh - $prevLow;
                        $tempReal2 = abs($prevHigh - $prevClose);
                        if ($tempReal2 > $tempReal) {
                            $tempReal = $tempReal2;
                        }
                        $tempReal2 = abs($prevLow - $prevClose);
                        if ($tempReal2 > $tempReal) {
                            $tempReal = $tempReal2;
                        }
                    };
                    if ((((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                        $outReal[$outIdx++] = (double)0.0;
                    } else {
                        $outReal[$outIdx++] = $diffM / $tempReal;
                    }
                } else {
                    $outReal[$outIdx++] = (double)0.0;
                }
                $prevClose = $inClose[$today];
            }
            $outNBElement = $outIdx;

            return ReturnCode::Success;
        }
        $outBegIdx   = $today = $startIdx;
        $prevMinusDM = 0.0;
        $prevTR      = 0.0;
        $today       = $startIdx - $lookbackTotal;
        $prevHigh    = $inHigh[$today];
        $prevLow     = $inLow[$today];
        $prevClose   = $inClose[$today];
        $i           = $optInTimePeriod - 1;
        while ($i-- > 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    += $tempReal;
            $prevClose = $inClose[$today];
        }
        $i = (static::$unstablePeriod[UnstablePeriodFunctionID::MinusDI]) + 1;
        while ($i-- != 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod) + $diffM;
            } else {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod);
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
        }
        if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
            $outReal[0] = (100.0 * ($prevMinusDM / $prevTR));
        } else {
            $outReal[0] = 0.0;
        }
        $outIdx = 1;
        while ($today < $endIdx) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod) + $diffM;
            } else {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod);
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $outReal[$outIdx++] = (100.0 * ($prevMinusDM / $prevTR));
            } else {
                $outReal[$outIdx++] = 0.0;
            }
        }
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function minusDM(int $startIdx, int $endIdx, array $inHigh, array $inLow, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInTimePeriod > 1) {
            $lookbackTotal = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::MinusDM]) - 1;
        } else {
            $lookbackTotal = 1;
        }
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx = 0;
        if ($optInTimePeriod <= 1) {
            $outBegIdx = $startIdx;
            $today     = $startIdx - 1;
            $prevHigh  = $inHigh[$today];
            $prevLow   = $inLow[$today];
            while ($today < $endIdx) {
                $today++;
                $tempReal = $inHigh[$today];
                $diffP    = $tempReal - $prevHigh;
                $prevHigh = $tempReal;
                $tempReal = $inLow[$today];
                $diffM    = $prevLow - $tempReal;
                $prevLow  = $tempReal;
                if (($diffM > 0) && ($diffP < $diffM)) {
                    $outReal[$outIdx++] = $diffM;
                } else {
                    $outReal[$outIdx++] = 0;
                }
            }
            $outNBElement = $outIdx;

            return ReturnCode::Success;
        }
        $outBegIdx   = $startIdx;
        $prevMinusDM = 0.0;
        $today       = $startIdx - $lookbackTotal;
        $prevHigh    = $inHigh[$today];
        $prevLow     = $inLow[$today];
        $i           = $optInTimePeriod - 1;
        while ($i-- > 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            }
        }
        $i = (static::$unstablePeriod[UnstablePeriodFunctionID::MinusDM]);
        while ($i-- != 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod) + $diffM;
            } else {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod);
            }
        }
        $outReal[0] = $prevMinusDM;
        $outIdx     = 1;
        while ($today < $endIdx) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod) + $diffM;
            } else {
                $prevMinusDM = $prevMinusDM - ($prevMinusDM / $optInTimePeriod);
            }
            $outReal[$outIdx++] = $prevMinusDM;
        }
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function mom(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $inIdx       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        while ($inIdx <= $endIdx) {
            $outReal[$outIdx++] = $inReal[$inIdx++] - $inReal[$trailingIdx++];
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

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
    public static function plusDI(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInTimePeriod > 1) {
            $lookbackTotal = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::PlusDI]);
        } else {
            $lookbackTotal = 1;
        }
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx = 0;
        if ($optInTimePeriod <= 1) {
            $outBegIdx = $startIdx;
            $today     = $startIdx - 1;
            $prevHigh  = $inHigh[$today];
            $prevLow   = $inLow[$today];
            $prevClose = $inClose[$today];
            while ($today < $endIdx) {
                $today++;
                $tempReal = $inHigh[$today];
                $diffP    = $tempReal - $prevHigh;
                $prevHigh = $tempReal;
                $tempReal = $inLow[$today];
                $diffM    = $prevLow - $tempReal;
                $prevLow  = $tempReal;
                if (($diffP > 0) && ($diffP > $diffM)) {
                    {
                        $tempReal  = $prevHigh - $prevLow;
                        $tempReal2 = abs($prevHigh - $prevClose);
                        if ($tempReal2 > $tempReal) {
                            $tempReal = $tempReal2;
                        }
                        $tempReal2 = abs($prevLow - $prevClose);
                        if ($tempReal2 > $tempReal) {
                            $tempReal = $tempReal2;
                        }
                    };
                    if ((((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                        $outReal[$outIdx++] = (double)0.0;
                    } else {
                        $outReal[$outIdx++] = $diffP / $tempReal;
                    }
                } else {
                    $outReal[$outIdx++] = (double)0.0;
                }
                $prevClose = $inClose[$today];
            }
            $outNBElement = $outIdx;

            return ReturnCode::Success;
        }
        $outBegIdx  = $today = $startIdx;
        $prevPlusDM = 0.0;
        $prevTR     = 0.0;
        $today      = $startIdx - $lookbackTotal;
        $prevHigh   = $inHigh[$today];
        $prevLow    = $inLow[$today];
        $prevClose  = $inClose[$today];
        $i          = $optInTimePeriod - 1;
        while ($i-- > 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    += $tempReal;
            $prevClose = $inClose[$today];
        }
        $i = (static::$unstablePeriod[UnstablePeriodFunctionID::PlusDI]) + 1;
        while ($i-- != 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod) + $diffP;
            } else {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod);
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
        }
        if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
            $outReal[0] = (100.0 * ($prevPlusDM / $prevTR));
        } else {
            $outReal[0] = 0.0;
        }
        $outIdx = 1;
        while ($today < $endIdx) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod) + $diffP;
            } else {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod);
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $outReal[$outIdx++] = (100.0 * ($prevPlusDM / $prevTR));
            } else {
                $outReal[$outIdx++] = 0.0;
            }
        }
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function plusDM(int $startIdx, int $endIdx, array $inHigh, array $inLow, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInTimePeriod > 1) {
            $lookbackTotal = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::PlusDM]) - 1;
        } else {
            $lookbackTotal = 1;
        }
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx = 0;
        if ($optInTimePeriod <= 1) {
            $outBegIdx = $startIdx;
            $today     = $startIdx - 1;
            $prevHigh  = $inHigh[$today];
            $prevLow   = $inLow[$today];
            while ($today < $endIdx) {
                $today++;
                $tempReal = $inHigh[$today];
                $diffP    = $tempReal - $prevHigh;
                $prevHigh = $tempReal;
                $tempReal = $inLow[$today];
                $diffM    = $prevLow - $tempReal;
                $prevLow  = $tempReal;
                if (($diffP > 0) && ($diffP > $diffM)) {
                    $outReal[$outIdx++] = $diffP;
                } else {
                    $outReal[$outIdx++] = 0;
                }
            }
            $outNBElement = $outIdx;

            return ReturnCode::Success;
        }
        $outBegIdx  = $startIdx;
        $prevPlusDM = 0.0;
        $today      = $startIdx - $lookbackTotal;
        $prevHigh   = $inHigh[$today];
        $prevLow    = $inLow[$today];
        $i          = $optInTimePeriod - 1;
        while ($i-- > 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
        }
        $i = (static::$unstablePeriod[UnstablePeriodFunctionID::PlusDM]);
        while ($i-- != 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod) + $diffP;
            } else {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod);
            }
        }
        $outReal[0] = $prevPlusDM;
        $outIdx     = 1;
        while ($today < $endIdx) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod) + $diffP;
            } else {
                $prevPlusDM = $prevPlusDM - ($prevPlusDM / $optInTimePeriod);
            }
            $outReal[$outIdx++] = $prevPlusDM;
        }
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInFastPeriod
     * @param int   $optInSlowPeriod
     * @param int   $optInMAType
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function ppo(int $startIdx, int $endIdx, array $inReal, int $optInFastPeriod, int $optInSlowPeriod, int $optInMAType, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $tempBuffer = static::double($endIdx - $startIdx + 1);
        $one        = 1;
        $ReturnCode = static::TA_INT_PO(
            $startIdx, $endIdx, $inReal,
            $optInFastPeriod,
            $optInSlowPeriod,
            $optInMAType,
            $outBegIdx,
            $outNBElement,
            $outReal,
            $tempBuffer,
            $one
        );

        return $ReturnCode;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function roc(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $inIdx       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        while ($inIdx <= $endIdx) {
            $tempReal = $inReal[$trailingIdx++];
            if ($tempReal != 0.0) {
                $outReal[$outIdx++] = (($inReal[$inIdx] / $tempReal) - 1.0) * 100.0;
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            $inIdx++;
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function rocP(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $inIdx       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        while ($inIdx <= $endIdx) {
            $tempReal = $inReal[$trailingIdx++];
            if ($tempReal != 0.0) {
                $outReal[$outIdx++] = ($inReal[$inIdx] - $tempReal) / $tempReal;
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            $inIdx++;
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function rocR(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $inIdx       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        while ($inIdx <= $endIdx) {
            $tempReal = $inReal[$trailingIdx++];
            if ($tempReal != 0.0) {
                $outReal[$outIdx++] = ($inReal[$inIdx] / $tempReal);
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            $inIdx++;
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function rocR100(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $inIdx       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        while ($inIdx <= $endIdx) {
            $tempReal = $inReal[$trailingIdx++];
            if ($tempReal != 0.0) {
                $outReal[$outIdx++] = ($inReal[$inIdx] / $tempReal) * 100.0;
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            $inIdx++;
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function rsi(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $outBegIdx     = 0;
        $outNBElement  = 0;
        $lookbackTotal = Lookback::rsiLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        $outIdx = 0;
        if ($optInTimePeriod == 1) {
            $outBegIdx    = $startIdx;
            $i            = ($endIdx - $startIdx) + 1;
            $outNBElement = $i;
            //System::arraycopy($inReal, $startIdx, $outReal, 0, $i);
            $outReal = \array_slice($inReal, $startIdx, $i);

            return ReturnCode::Success;
        }
        $today          = $startIdx - $lookbackTotal;
        $prevValue      = $inReal[$today];
        $unstablePeriod = (static::$unstablePeriod[UnstablePeriodFunctionID::RSI]);
        if (($unstablePeriod == 0) &&
            ((static::$compatibility) == Compatibility::Metastock)) {
            $savePrevValue = $prevValue;
            $prevGain      = 0.0;
            $prevLoss      = 0.0;
            for ($i = $optInTimePeriod; $i > 0; $i--) {
                $tempValue1 = $inReal[$today++];
                $tempValue2 = $tempValue1 - $prevValue;
                $prevValue  = $tempValue1;
                if ($tempValue2 < 0) {
                    $prevLoss -= $tempValue2;
                } else {
                    $prevGain += $tempValue2;
                }
            }
            $tempValue1 = $prevLoss / $optInTimePeriod;
            $tempValue2 = $prevGain / $optInTimePeriod;
            $tempValue1 = $tempValue2 + $tempValue1;
            if (!(((-0.00000001) < $tempValue1) && ($tempValue1 < 0.00000001))) {
                $outReal[$outIdx++] = 100 * ($tempValue2 / $tempValue1);
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            if ($today > $endIdx) {
                $outBegIdx    = $startIdx;
                $outNBElement = $outIdx;

                return ReturnCode::Success;
            }
            $today     -= $optInTimePeriod;
            $prevValue = $savePrevValue;
        }
        $prevGain = 0.0;
        $prevLoss = 0.0;
        $today++;
        for ($i = $optInTimePeriod; $i > 0; $i--) {
            $tempValue1 = $inReal[$today++];
            $tempValue2 = $tempValue1 - $prevValue;
            $prevValue  = $tempValue1;
            if ($tempValue2 < 0) {
                $prevLoss -= $tempValue2;
            } else {
                $prevGain += $tempValue2;
            }
        }
        $prevLoss /= $optInTimePeriod;
        $prevGain /= $optInTimePeriod;
        if ($today > $startIdx) {
            $tempValue1 = $prevGain + $prevLoss;
            if (!(((-0.00000001) < $tempValue1) && ($tempValue1 < 0.00000001))) {
                $outReal[$outIdx++] = 100.0 * ($prevGain / $tempValue1);
            } else {
                $outReal[$outIdx++] = 0.0;
            }
        } else {
            while ($today < $startIdx) {
                $tempValue1 = $inReal[$today];
                $tempValue2 = $tempValue1 - $prevValue;
                $prevValue  = $tempValue1;
                $prevLoss   *= ($optInTimePeriod - 1);
                $prevGain   *= ($optInTimePeriod - 1);
                if ($tempValue2 < 0) {
                    $prevLoss -= $tempValue2;
                } else {
                    $prevGain += $tempValue2;
                }
                $prevLoss /= $optInTimePeriod;
                $prevGain /= $optInTimePeriod;
                $today++;
            }
        }
        while ($today <= $endIdx) {
            $tempValue1 = $inReal[$today++];
            $tempValue2 = $tempValue1 - $prevValue;
            $prevValue  = $tempValue1;
            $prevLoss   *= ($optInTimePeriod - 1);
            $prevGain   *= ($optInTimePeriod - 1);
            if ($tempValue2 < 0) {
                $prevLoss -= $tempValue2;
            } else {
                $prevGain += $tempValue2;
            }
            $prevLoss   /= $optInTimePeriod;
            $prevGain   /= $optInTimePeriod;
            $tempValue1 = $prevGain + $prevLoss;
            if (!(((-0.00000001) < $tempValue1) && ($tempValue1 < 0.00000001))) {
                $outReal[$outIdx++] = 100.0 * ($prevGain / $tempValue1);
            } else {
                $outReal[$outIdx++] = 0.0;
            }
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param array $inClose
     * @param int   $optInFastK_Period
     * @param int   $optInSlowK_Period
     * @param int   $optInSlowK_MAType
     * @param int   $optInSlowD_Period
     * @param int   $optInSlowD_MAType
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outSlowK
     * @param array $outSlowD
     *
     * @return int
     */
    public static function stoch(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInFastK_Period, int $optInSlowK_Period, int $optInSlowK_MAType, int $optInSlowD_Period, int $optInSlowD_MAType, int &$outBegIdx, int &$outNBElement, array &$outSlowK, array &$outSlowD): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInFastK_Period == (PHP_INT_MIN)) {
            $optInFastK_Period = 5;
        } elseif (((int)$optInFastK_Period < 1) || ((int)$optInFastK_Period > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowK_Period == (PHP_INT_MIN)) {
            $optInSlowK_Period = 3;
        } elseif (((int)$optInSlowK_Period < 1) || ((int)$optInSlowK_Period > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowD_Period == (PHP_INT_MIN)) {
            $optInSlowD_Period = 3;
        } elseif (((int)$optInSlowD_Period < 1) || ((int)$optInSlowD_Period > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackK     = $optInFastK_Period - 1;
        $lookbackKSlow = Lookback::movingAverageLookback($optInSlowK_Period, $optInSlowK_MAType);
        $lookbackDSlow = Lookback::movingAverageLookback($optInSlowD_Period, $optInSlowD_MAType);
        $lookbackTotal = $lookbackK + $lookbackDSlow + $lookbackKSlow;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $trailingIdx = $startIdx - $lookbackTotal;
        $today       = $trailingIdx + $lookbackK;
        $lowestIdx   = $highestIdx = -1;
        $diff        = $highest = $lowest = 0.0;
        if (($outSlowK == $inHigh) ||
            ($outSlowK == $inLow) ||
            ($outSlowK == $inClose)) {
            $tempBuffer = $outSlowK;
        } elseif (($outSlowD == $inHigh) ||
                  ($outSlowD == $inLow) ||
                  ($outSlowD == $inClose)) {
            $tempBuffer = $outSlowD;
        } else {
            $tempBuffer = static::double($endIdx - $today + 1);
        }
        while ($today <= $endIdx) {
            $tmp = $inLow[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inLow[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inLow[$i];
                    if ($tmp < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
                $diff = ($highest - $lowest) / 100.0;
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
                $diff      = ($highest - $lowest) / 100.0;
            }
            $tmp = $inHigh[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inHigh[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inHigh[$i];
                    if ($tmp > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
                $diff = ($highest - $lowest) / 100.0;
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
                $diff       = ($highest - $lowest) / 100.0;
            }
            if ($diff != 0.0) {
                $tempBuffer[$outIdx++] = ($inClose[$today] - $lowest) / $diff;
            } else {
                $tempBuffer[$outIdx++] = 0.0;
            }
            $trailingIdx++;
            $today++;
        }
        $ReturnCode = OverlapStudies::movingAverage(
            0, $outIdx - 1,
            $tempBuffer, $optInSlowK_Period,
            $optInSlowK_MAType,
            $outBegIdx, $outNBElement, $tempBuffer
        );
        if (($ReturnCode != ReturnCode::Success) || ((int)$outNBElement == 0)) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        $ReturnCode = OverlapStudies::movingAverage(
            0, (int)$outNBElement - 1,
            $tempBuffer, $optInSlowD_Period,
            $optInSlowD_MAType,
            $outBegIdx, $outNBElement, $outSlowD
        );
        //System::arraycopy($tempBuffer, $lookbackDSlow, $outSlowK, 0, (int)$outNBElement);
        $outSlowK = \array_slice($tempBuffer, $lookbackDSlow, (int)$outNBElement);
        if ($ReturnCode != ReturnCode::Success) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        $outBegIdx = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param array $inClose
     * @param int   $optInFastK_Period
     * @param int   $optInFastD_Period
     * @param int   $optInFastD_MAType
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outFastK
     * @param array $outFastD
     *
     * @return int
     */
    public static function stochF(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInFastK_Period, int $optInFastD_Period, int $optInFastD_MAType, int &$outBegIdx, int &$outNBElement, array &$outFastK, array &$outFastD): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInFastK_Period == (PHP_INT_MIN)) {
            $optInFastK_Period = 5;
        } elseif (((int)$optInFastK_Period < 1) || ((int)$optInFastK_Period > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInFastD_Period == (PHP_INT_MIN)) {
            $optInFastD_Period = 3;
        } elseif (((int)$optInFastD_Period < 1) || ((int)$optInFastD_Period > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackK     = $optInFastK_Period - 1;
        $lookbackFastD = Lookback::movingAverageLookback($optInFastD_Period, $optInFastD_MAType);
        $lookbackTotal = $lookbackK + $lookbackFastD;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $trailingIdx = $startIdx - $lookbackTotal;
        $today       = $trailingIdx + $lookbackK;
        $lowestIdx   = $highestIdx = -1;
        $diff        = $highest = $lowest = 0.0;
        if (($outFastK == $inHigh) ||
            ($outFastK == $inLow) ||
            ($outFastK == $inClose)) {
            $tempBuffer = $outFastK;
        } elseif (($outFastD == $inHigh) ||
                  ($outFastD == $inLow) ||
                  ($outFastD == $inClose)) {
            $tempBuffer = $outFastD;
        } else {
            $tempBuffer = static::double($endIdx - $today + 1);
        }
        while ($today <= $endIdx) {
            $tmp = $inLow[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inLow[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inLow[$i];
                    if ($tmp < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
                $diff = ($highest - $lowest) / 100.0;
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
                $diff      = ($highest - $lowest) / 100.0;
            }
            $tmp = $inHigh[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inHigh[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inHigh[$i];
                    if ($tmp > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
                $diff = ($highest - $lowest) / 100.0;
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
                $diff       = ($highest - $lowest) / 100.0;
            }
            if ($diff != 0.0) {
                $tempBuffer[$outIdx++] = ($inClose[$today] - $lowest) / $diff;
            } else {
                $tempBuffer[$outIdx++] = 0.0;
            }
            $trailingIdx++;
            $today++;
        }
        $ReturnCode = OverlapStudies::movingAverage(
            0, $outIdx - 1,
            $tempBuffer, $optInFastD_Period,
            $optInFastD_MAType,
            $outBegIdx, $outNBElement, $outFastD
        );
        if (($ReturnCode != ReturnCode::Success) || ((int)$outNBElement) == 0) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        //System::arraycopy($tempBuffer, $lookbackFastD, $outFastK, 0, (int)$outNBElement);
        $outFastK = \array_slice($tempBuffer, $lookbackFastD, (int)$outNBElement);
        if ($ReturnCode != ReturnCode::Success) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        $outBegIdx = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $optInFastK_Period
     * @param int   $optInFastD_Period
     * @param int   $optInFastD_MAType
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outFastK
     * @param array $outFastD
     *
     * @return int
     */
    public static function stochRsi(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int $optInFastK_Period, int $optInFastD_Period, int $optInFastD_MAType, int &$outBegIdx, int &$outNBElement, array &$outFastK, array &$outFastD): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $outBegIdx1    = 0;
        $outBegIdx2    = 0;
        $outNbElement1 = 0;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInFastK_Period == (PHP_INT_MIN)) {
            $optInFastK_Period = 5;
        } elseif (((int)$optInFastK_Period < 1) || ((int)$optInFastK_Period > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInFastD_Period == (PHP_INT_MIN)) {
            $optInFastD_Period = 3;
        } elseif (((int)$optInFastD_Period < 1) || ((int)$optInFastD_Period > 100000)) {
            return ReturnCode::BadParam;
        }
        $outBegIdx      = 0;
        $outNBElement   = 0;
        $lookbackSTOCHF = Lookback::stochFLookback($optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $lookbackTotal  = Lookback::rsiLookback($optInTimePeriod) + $lookbackSTOCHF;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outBegIdx     = $startIdx;
        $tempArraySize = ($endIdx - $startIdx) + 1 + $lookbackSTOCHF;
        $tempRSIBuffer = static::double($tempArraySize);
        $ReturnCode    = self::rsi(
            $startIdx - $lookbackSTOCHF,
            $endIdx,
            $inReal,
            $optInTimePeriod,
            $outBegIdx1,
            $outNbElement1,
            $tempRSIBuffer
        );
        if ($ReturnCode != ReturnCode::Success || $outNbElement1 == 0) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }
        $ReturnCode = self::stochF(
            0,
            $tempArraySize - 1,
            $tempRSIBuffer,
            $tempRSIBuffer,
            $tempRSIBuffer,
            $optInFastK_Period,
            $optInFastD_Period,
            $optInFastD_MAType,
            $outBegIdx2,
            $outNBElement,
            $outFastK,
            $outFastD
        );
        if ($ReturnCode != ReturnCode::Success || ((int)$outNBElement) == 0) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return $ReturnCode;
        }

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function trix(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $nbElement = 0;
        $begIdx    = 0;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $emaLookback   = Lookback::emaLookback($optInTimePeriod);
        $rocLookback   = Lookback::rocRLookback(1);
        $totalLookback = ($emaLookback * 3) + $rocLookback;
        if ($startIdx < $totalLookback) {
            $startIdx = $totalLookback;
        }
        if ($startIdx > $endIdx) {
            $outNBElement = 0;
            $outBegIdx    = 0;

            return ReturnCode::Success;
        }
        $outBegIdx         = $startIdx;
        $nbElementToOutput = ($endIdx - $startIdx) + 1 + $totalLookback;
        $tempBuffer        = static::double($nbElementToOutput);
        $k                 = ((double)2.0 / ((double)($optInTimePeriod + 1)));
        $ReturnCode        = static::TA_INT_EMA(
            ($startIdx - $totalLookback), $endIdx, $inReal,
                                          $optInTimePeriod, $k,
                                          $begIdx, $nbElement,
                                          $tempBuffer
        );
        if (($ReturnCode != ReturnCode::Success) || ($nbElement == 0)) {
            $outNBElement = 0;
            $outBegIdx    = 0;

            return $ReturnCode;
        }
        $nbElementToOutput--;
        $nbElementToOutput -= $emaLookback;
        $ReturnCode        = static::TA_INT_EMA(
            0, $nbElementToOutput, $tempBuffer,
            $optInTimePeriod, $k,
            $begIdx, $nbElement,
            $tempBuffer
        );
        if (($ReturnCode != ReturnCode::Success) || ($nbElement == 0)) {
            $outNBElement = 0;
            $outBegIdx    = 0;

            return $ReturnCode;
        }
        $nbElementToOutput -= $emaLookback;
        $ReturnCode        = static::TA_INT_EMA(
            0, $nbElementToOutput, $tempBuffer,
            $optInTimePeriod, $k,
            $begIdx, $nbElement,
            $tempBuffer
        );
        if (($ReturnCode != ReturnCode::Success) || ($nbElement == 0)) {
            $outNBElement = 0;
            $outBegIdx    = 0;

            return $ReturnCode;
        }
        $nbElementToOutput -= $emaLookback;
        $ReturnCode        = self::roc(
            0, $nbElementToOutput,
            $tempBuffer,
            1, $begIdx, $outNBElement,
            $outReal
        );
        if (($ReturnCode != ReturnCode::Success) || ((int)$outNBElement == 0)) {
            $outNBElement = 0;
            $outBegIdx    = 0;

            return $ReturnCode;
        }

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inHigh
     * @param array $inLow
     * @param array $inClose
     * @param int   $optInTimePeriod1
     * @param int   $optInTimePeriod2
     * @param int   $optInTimePeriod3
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function ultOsc(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod1, int $optInTimePeriod2, int $optInTimePeriod3, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        $usedFlag      = \array_pad([], 3, 0);
        $periods       = \array_pad([], 3, 0);
        $sortedPeriods = \array_pad([], 3, 0);
        if ((int)$optInTimePeriod1 == (PHP_INT_MIN)) {
            $optInTimePeriod1 = 7;
        } elseif (((int)$optInTimePeriod1 < 1) || ((int)$optInTimePeriod1 > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInTimePeriod2 == (PHP_INT_MIN)) {
            $optInTimePeriod2 = 14;
        } elseif (((int)$optInTimePeriod2 < 1) || ((int)$optInTimePeriod2 > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInTimePeriod3 == (PHP_INT_MIN)) {
            $optInTimePeriod3 = 28;
        } elseif (((int)$optInTimePeriod3 < 1) || ((int)$optInTimePeriod3 > 100000)) {
            return ReturnCode::BadParam;
        }
        $outBegIdx    = 0;
        $outNBElement = 0;
        $periods[0]   = $optInTimePeriod1;
        $periods[1]   = $optInTimePeriod2;
        $periods[2]   = $optInTimePeriod3;
        $usedFlag[0]  = 0;
        $usedFlag[1]  = 0;
        $usedFlag[2]  = 0;
        for ($i = 0; $i < 3; ++$i) {
            $longestPeriod = 0;
            $longestIndex  = 0;
            for ($j = 0; $j < 3; ++$j) {
                if (($usedFlag[$j] == 0) && ($periods[$j] > $longestPeriod)) {
                    $longestPeriod = $periods[$j];
                    $longestIndex  = $j;
                }
            }
            $usedFlag[$longestIndex] = 1;
            $sortedPeriods[$i]       = $longestPeriod;
        }
        $optInTimePeriod1 = $sortedPeriods[2];
        $optInTimePeriod2 = $sortedPeriods[1];
        $optInTimePeriod3 = $sortedPeriods[0];
        $lookbackTotal    = Lookback::ultOscLookback($optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        {
            $a1Total = 0;
            $b1Total = 0;
            for ($i = $startIdx - $optInTimePeriod1 + 1; $i < $startIdx; ++$i) {
                {
                    $tempLT            = $inLow[$i];
                    $tempHT            = $inHigh[$i];
                    $tempCY            = $inClose[$i - 1];
                    $trueLow           = ((($tempLT) < ($tempCY)) ? ($tempLT) : ($tempCY));
                    $closeMinusTrueLow = $inClose[$i] - $trueLow;
                    $trueRange         = $tempHT - $tempLT;
                    $tempDouble        = abs($tempCY - $tempHT);
                    if ($tempDouble > $trueRange) {
                        $trueRange = $tempDouble;
                    }
                    $tempDouble = abs($tempCY - $tempLT);
                    if ($tempDouble > $trueRange) {
                        $trueRange = $tempDouble;
                    }
                };
                $a1Total += $closeMinusTrueLow;
                $b1Total += $trueRange;
            }
        };
        {
            $a2Total = 0;
            $b2Total = 0;
            for ($i = $startIdx - $optInTimePeriod2 + 1; $i < $startIdx; ++$i) {
                {
                    $tempLT            = $inLow[$i];
                    $tempHT            = $inHigh[$i];
                    $tempCY            = $inClose[$i - 1];
                    $trueLow           = ((($tempLT) < ($tempCY)) ? ($tempLT) : ($tempCY));
                    $closeMinusTrueLow = $inClose[$i] - $trueLow;
                    $trueRange         = $tempHT - $tempLT;
                    $tempDouble        = abs($tempCY - $tempHT);
                    if ($tempDouble > $trueRange) {
                        $trueRange = $tempDouble;
                    }
                    $tempDouble = abs($tempCY - $tempLT);
                    if ($tempDouble > $trueRange) {
                        $trueRange = $tempDouble;
                    }
                };
                $a2Total += $closeMinusTrueLow;
                $b2Total += $trueRange;
            }
        };
        {
            $a3Total = 0;
            $b3Total = 0;
            for ($i = $startIdx - $optInTimePeriod3 + 1; $i < $startIdx; ++$i) {
                {
                    $tempLT            = $inLow[$i];
                    $tempHT            = $inHigh[$i];
                    $tempCY            = $inClose[$i - 1];
                    $trueLow           = ((($tempLT) < ($tempCY)) ? ($tempLT) : ($tempCY));
                    $closeMinusTrueLow = $inClose[$i] - $trueLow;
                    $trueRange         = $tempHT - $tempLT;
                    $tempDouble        = abs($tempCY - $tempHT);
                    if ($tempDouble > $trueRange) {
                        $trueRange = $tempDouble;
                    }
                    $tempDouble = abs($tempCY - $tempLT);
                    if ($tempDouble > $trueRange) {
                        $trueRange = $tempDouble;
                    }
                };
                $a3Total += $closeMinusTrueLow;
                $b3Total += $trueRange;
            }
        };
        $today        = $startIdx;
        $outIdx       = 0;
        $trailingIdx1 = $today - $optInTimePeriod1 + 1;
        $trailingIdx2 = $today - $optInTimePeriod2 + 1;
        $trailingIdx3 = $today - $optInTimePeriod3 + 1;
        while ($today <= $endIdx) {
            {
                $tempLT            = $inLow[$today];
                $tempHT            = $inHigh[$today];
                $tempCY            = $inClose[$today - 1];
                $trueLow           = ((($tempLT) < ($tempCY)) ? ($tempLT) : ($tempCY));
                $closeMinusTrueLow = $inClose[$today] - $trueLow;
                $trueRange         = $tempHT - $tempLT;
                $tempDouble        = abs($tempCY - $tempHT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
                $tempDouble = abs($tempCY - $tempLT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
            };
            $a1Total += $closeMinusTrueLow;
            $a2Total += $closeMinusTrueLow;
            $a3Total += $closeMinusTrueLow;
            $b1Total += $trueRange;
            $b2Total += $trueRange;
            $b3Total += $trueRange;
            $output  = 0.0;
            if (!(((-0.00000001) < $b1Total) && ($b1Total < 0.00000001))) {
                $output += 4.0 * ($a1Total / $b1Total);
            }
            if (!(((-0.00000001) < $b2Total) && ($b2Total < 0.00000001))) {
                $output += 2.0 * ($a2Total / $b2Total);
            }
            if (!(((-0.00000001) < $b3Total) && ($b3Total < 0.00000001))) {
                $output += $a3Total / $b3Total;
            }
            {
                $tempLT            = $inLow[$trailingIdx1];
                $tempHT            = $inHigh[$trailingIdx1];
                $tempCY            = $inClose[$trailingIdx1 - 1];
                $trueLow           = ((($tempLT) < ($tempCY)) ? ($tempLT) : ($tempCY));
                $closeMinusTrueLow = $inClose[$trailingIdx1] - $trueLow;
                $trueRange         = $tempHT - $tempLT;
                $tempDouble        = abs($tempCY - $tempHT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
                $tempDouble = abs($tempCY - $tempLT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
            };
            $a1Total -= $closeMinusTrueLow;
            $b1Total -= $trueRange;
            {
                $tempLT            = $inLow[$trailingIdx2];
                $tempHT            = $inHigh[$trailingIdx2];
                $tempCY            = $inClose[$trailingIdx2 - 1];
                $trueLow           = ((($tempLT) < ($tempCY)) ? ($tempLT) : ($tempCY));
                $closeMinusTrueLow = $inClose[$trailingIdx2] - $trueLow;
                $trueRange         = $tempHT - $tempLT;
                $tempDouble        = abs($tempCY - $tempHT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
                $tempDouble = abs($tempCY - $tempLT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
            };
            $a2Total -= $closeMinusTrueLow;
            $b2Total -= $trueRange;
            {
                $tempLT            = $inLow[$trailingIdx3];
                $tempHT            = $inHigh[$trailingIdx3];
                $tempCY            = $inClose[$trailingIdx3 - 1];
                $trueLow           = ((($tempLT) < ($tempCY)) ? ($tempLT) : ($tempCY));
                $closeMinusTrueLow = $inClose[$trailingIdx3] - $trueLow;
                $trueRange         = $tempHT - $tempLT;
                $tempDouble        = abs($tempCY - $tempHT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
                $tempDouble = abs($tempCY - $tempLT);
                if ($tempDouble > $trueRange) {
                    $trueRange = $tempDouble;
                }
            };
            $a3Total          -= $closeMinusTrueLow;
            $b3Total          -= $trueRange;
            $outReal[$outIdx] = 100.0 * ($output / 7.0);
            $outIdx++;
            $today++;
            $trailingIdx1++;
            $trailingIdx2++;
            $trailingIdx3++;
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

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
    public static function willR(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $diff        = 0.0;
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $lowestIdx   = $highestIdx = -1;
        $diff        = $highest = $lowest = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inLow[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inLow[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inLow[$i];
                    if ($tmp < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
                $diff = ($highest - $lowest) / (-100.0);
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
                $diff      = ($highest - $lowest) / (-100.0);
            }
            $tmp = $inHigh[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inHigh[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inHigh[$i];
                    if ($tmp > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
                $diff = ($highest - $lowest) / (-100.0);
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
                $diff       = ($highest - $lowest) / (-100.0);
            }
            if ($diff != 0.0) {
                $outReal[$outIdx++] = ($highest - $inClose[$today]) / $diff;
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }
}
