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

class MathOperators extends Core
{

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inReal0
     * @param float[] $inReal1
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function add(int $startIdx, int $endIdx, array $inReal0, array $inReal1, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] + $inReal1[$i];
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal0
     * @param array $inReal1
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function div(int $startIdx, int $endIdx, array $inReal0, array $inReal1, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] / $inReal1[$i];
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
    public static function max(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
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
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $outReal[$outIdx++] = $highest;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
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
     * @param array $outInteger
     *
     * @return int
     */
    public static function maxIndex(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
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
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $outInteger[$outIdx++] = $highestIdx;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
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
    public static function min(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
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
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $outReal[$outIdx++] = $lowest;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
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
     * @param array $outInteger
     *
     * @return int
     */
    public static function minIndex(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outInteger): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
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
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $outInteger[$outIdx++] = $lowestIdx;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
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
     * @param array $outMin
     * @param array $outMax
     *
     * @return int
     */
    public static function minMax(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outMin, array &$outMax): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
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
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmpLow = $tmpHigh = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmpHigh = $inReal[$i];
                    if ($tmpHigh > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmpHigh;
                    }
                }
            } elseif ($tmpHigh >= $highest) {
                $highestIdx = $today;
                $highest    = $tmpHigh;
            }
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmpLow = $inReal[$i];
                    if ($tmpLow < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmpLow;
                    }
                }
            } elseif ($tmpLow <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmpLow;
            }
            $outMax[$outIdx] = $highest;
            $outMin[$outIdx] = $lowest;
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
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
     * @param array $outMinIdx
     * @param array $outMaxIdx
     *
     * @return int
     */
    public static function minMaxIndex(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outMinIdx, array &$outMaxIdx): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
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
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmpLow = $tmpHigh = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmpHigh = $inReal[$i];
                    if ($tmpHigh > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmpHigh;
                    }
                }
            } elseif ($tmpHigh >= $highest) {
                $highestIdx = $today;
                $highest    = $tmpHigh;
            }
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmpLow = $inReal[$i];
                    if ($tmpLow < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmpLow;
                    }
                }
            } elseif ($tmpLow <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmpLow;
            }
            $outMaxIdx[$outIdx] = $highestIdx;
            $outMinIdx[$outIdx] = $lowestIdx;
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx    = $startIdx;
        $outNBElement = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal0
     * @param array $inReal1
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function mult(int $startIdx, int $endIdx, array $inReal0, array $inReal1, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] * $inReal1[$i];
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal0
     * @param array $inReal1
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function sub(int $startIdx, int $endIdx, array $inReal0, array $inReal1, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] - $inReal1[$i];
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
    public static function sum(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
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
        $periodTotal = 0;
        $trailingIdx = $startIdx - $lookbackTotal;
        $i           = $trailingIdx;
        if ($optInTimePeriod > 1) {
            while ($i < $startIdx) {
                $periodTotal += $inReal[$i++];
            }
        }
        $outIdx = 0;
        do {
            $periodTotal        += $inReal[$i++];
            $tempReal           = $periodTotal;
            $periodTotal        -= $inReal[$trailingIdx++];
            $outReal[$outIdx++] = $tempReal;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

}
