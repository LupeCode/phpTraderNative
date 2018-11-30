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

class StatisticFunctions extends Core
{

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inReal0
     * @param float[] $inReal1
     * @param int     $optInTimePeriod
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function beta(int $startIdx, int $endIdx, array $inReal0, array $inReal1, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = $optInTimePeriod;
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $trailingIdx  = $startIdx - $nbInitialElementNeeded;
        $last_price_x = $trailing_last_price_x = $inReal0[$trailingIdx];
        $last_price_y = $trailing_last_price_y = $inReal1[$trailingIdx];
        $i            = ++$trailingIdx;
        $S_xx         = $S_xy = $S_x = $S_y = 0;
        while ($i < $startIdx) {
            $tmp_real = $inReal0[$i];
            if (!(((-0.00000001) < $last_price_x) && ($last_price_x < 0.00000001))) {
                $x = ($tmp_real - $last_price_x) / $last_price_x;
            } else {
                $x = 0.0;
            }
            $last_price_x = $tmp_real;
            $tmp_real     = $inReal1[$i++];
            if (!(((-0.00000001) < $last_price_y) && ($last_price_y < 0.00000001))) {
                $y = ($tmp_real - $last_price_y) / $last_price_y;
            } else {
                $y = 0.0;
            }
            $last_price_y = $tmp_real;
            $S_xx         += $x * $x;
            $S_xy         += $x * $y;
            $S_x          += $x;
            $S_y          += $y;
        }
        $outIdx = 0;
        $n      = (double)$optInTimePeriod;
        do {
            $tmp_real = $inReal0[$i];
            if (!(((-0.00000001) < $last_price_x) && ($last_price_x < 0.00000001))) {
                $x = ($tmp_real - $last_price_x) / $last_price_x;
            } else {
                $x = 0.0;
            }
            $last_price_x = $tmp_real;
            $tmp_real     = $inReal1[$i++];
            if (!(((-0.00000001) < $last_price_y) && ($last_price_y < 0.00000001))) {
                $y = ($tmp_real - $last_price_y) / $last_price_y;
            } else {
                $y = 0.0;
            }
            $last_price_y = $tmp_real;
            $S_xx         += $x * $x;
            $S_xy         += $x * $y;
            $S_x          += $x;
            $S_y          += $y;
            $tmp_real     = $inReal0[$trailingIdx];
            if (!(((-0.00000001) < $trailing_last_price_x) && ($trailing_last_price_x < 0.00000001))) {
                $x = ($tmp_real - $trailing_last_price_x) / $trailing_last_price_x;
            } else {
                $x = 0.0;
            }
            $trailing_last_price_x = $tmp_real;
            $tmp_real              = $inReal1[$trailingIdx++];
            if (!(((-0.00000001) < $trailing_last_price_y) && ($trailing_last_price_y < 0.00000001))) {
                $y = ($tmp_real - $trailing_last_price_y) / $trailing_last_price_y;
            } else {
                $y = 0.0;
            }
            $trailing_last_price_y = $tmp_real;
            $tmp_real              = ($n * $S_xx) - ($S_x * $S_x);
            if (!(((-0.00000001) < $tmp_real) && ($tmp_real < 0.00000001))) {
                $outReal[$outIdx++] = (($n * $S_xy) - ($S_x * $S_y)) / $tmp_real;
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            $S_xx -= $x * $x;
            $S_xy -= $x * $y;
            $S_x  -= $x;
            $S_y  -= $y;
        } while ($i <= $endIdx);
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal0
     * @param array $inReal1
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function correl(int $startIdx, int $endIdx, array $inReal0, array $inReal1, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = $optInTimePeriod - 1;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outBegIdx   = $startIdx;
        $trailingIdx = $startIdx - $lookbackTotal;
        $sumXY       = $sumX = $sumY = $sumX2 = $sumY2 = 0.0;
        for ($today = $trailingIdx; $today <= $startIdx; $today++) {
            $x     = $inReal0[$today];
            $sumX  += $x;
            $sumX2 += $x * $x;
            $y     = $inReal1[$today];
            $sumXY += $x * $y;
            $sumY  += $y;
            $sumY2 += $y * $y;
        }
        $trailingX = $inReal0[$trailingIdx];
        $trailingY = $inReal1[$trailingIdx++];
        $tempReal  = ($sumX2 - (($sumX * $sumX) / $optInTimePeriod)) * ($sumY2 - (($sumY * $sumY) / $optInTimePeriod));
        if (!($tempReal < 0.00000001)) {
            $outReal[0] = ($sumXY - (($sumX * $sumY) / $optInTimePeriod)) / sqrt($tempReal);
        } else {
            $outReal[0] = 0.0;
        }
        $outIdx = 1;
        while ($today <= $endIdx) {
            $sumX      -= $trailingX;
            $sumX2     -= $trailingX * $trailingX;
            $sumXY     -= $trailingX * $trailingY;
            $sumY      -= $trailingY;
            $sumY2     -= $trailingY * $trailingY;
            $x         = $inReal0[$today];
            $sumX      += $x;
            $sumX2     += $x * $x;
            $y         = $inReal1[$today++];
            $sumXY     += $x * $y;
            $sumY      += $y;
            $sumY2     += $y * $y;
            $trailingX = $inReal0[$trailingIdx];
            $trailingY = $inReal1[$trailingIdx++];
            $tempReal  = ($sumX2 - (($sumX * $sumX) / $optInTimePeriod)) * ($sumY2 - (($sumY * $sumY) / $optInTimePeriod));
            if (!($tempReal < 0.00000001)) {
                $outReal[$outIdx++] = ($sumXY - (($sumX * $sumY) / $optInTimePeriod)) / sqrt($tempReal);
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
     * @param array $inReal
     * @param int   $optInTimePeriod
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function linearReg(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::linearRegLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx  = 0;
        $today   = $startIdx;
        $SumX    = $optInTimePeriod * ($optInTimePeriod - 1) * 0.5;
        $SumXSqr = $optInTimePeriod * ($optInTimePeriod - 1) * (2 * $optInTimePeriod - 1) / 6;
        $Divisor = $SumX * $SumX - $optInTimePeriod * $SumXSqr;
        while ($today <= $endIdx) {
            $SumXY = 0;
            $SumY  = 0;
            for ($i = $optInTimePeriod; $i-- != 0;) {
                $SumY  += $tempValue1 = $inReal[$today - $i];
                $SumXY += (double)$i * $tempValue1;
            }
            $m                  = ($optInTimePeriod * $SumXY - $SumX * $SumY) / $Divisor;
            $b                  = ($SumY - $m * $SumX) / (double)$optInTimePeriod;
            $outReal[$outIdx++] = $b + $m * (double)($optInTimePeriod - 1);
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
    public static function linearRegAngle(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::linearRegAngleLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx  = 0;
        $today   = $startIdx;
        $SumX    = $optInTimePeriod * ($optInTimePeriod - 1) * 0.5;
        $SumXSqr = $optInTimePeriod * ($optInTimePeriod - 1) * (2 * $optInTimePeriod - 1) / 6;
        $Divisor = $SumX * $SumX - $optInTimePeriod * $SumXSqr;
        while ($today <= $endIdx) {
            $SumXY = 0;
            $SumY  = 0;
            for ($i = $optInTimePeriod; $i-- != 0;) {
                $SumY  += $tempValue1 = $inReal[$today - $i];
                $SumXY += (double)$i * $tempValue1;
            }
            $m                  = ($optInTimePeriod * $SumXY - $SumX * $SumY) / $Divisor;
            $outReal[$outIdx++] = atan($m) * (180.0 / 3.14159265358979323846);
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
    public static function linearRegIntercept(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::linearRegInterceptLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx  = 0;
        $today   = $startIdx;
        $SumX    = $optInTimePeriod * ($optInTimePeriod - 1) * 0.5;
        $SumXSqr = $optInTimePeriod * ($optInTimePeriod - 1) * (2 * $optInTimePeriod - 1) / 6;
        $Divisor = $SumX * $SumX - $optInTimePeriod * $SumXSqr;
        while ($today <= $endIdx) {
            $SumXY = 0;
            $SumY  = 0;
            for ($i = $optInTimePeriod; $i-- != 0;) {
                $SumY  += $tempValue1 = $inReal[$today - $i];
                $SumXY += (double)$i * $tempValue1;
            }
            $m                  = ($optInTimePeriod * $SumXY - $SumX * $SumY) / $Divisor;
            $outReal[$outIdx++] = ($SumY - $m * $SumX) / (double)$optInTimePeriod;
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
    public static function linearRegSlope(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::linearRegSlopeLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx  = 0;
        $today   = $startIdx;
        $SumX    = $optInTimePeriod * ($optInTimePeriod - 1) * 0.5;
        $SumXSqr = $optInTimePeriod * ($optInTimePeriod - 1) * (2 * $optInTimePeriod - 1) / 6;
        $Divisor = $SumX * $SumX - $optInTimePeriod * $SumXSqr;
        while ($today <= $endIdx) {
            $SumXY = 0;
            $SumY  = 0;
            for ($i = $optInTimePeriod; $i-- != 0;) {
                $SumY  += $tempValue1 = $inReal[$today - $i];
                $SumXY += (double)$i * $tempValue1;
            }
            $outReal[$outIdx++] = ($optInTimePeriod * $SumXY - $SumX * $SumY) / $Divisor;
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
     * @param float $optInNbDev
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function stdDev(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, float $optInNbDev, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInNbDev == (-4e+37)) {
            $optInNbDev = 1.000000e+0;
        } elseif (($optInNbDev < -3.000000e+37) || ($optInNbDev > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }
        $retCode = static::TA_INT_VAR(
            $startIdx, $endIdx,
            $inReal, $optInTimePeriod,
            $outBegIdx, $outNBElement, $outReal
        );
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        if ($optInNbDev != 1.0) {
            for ($i = 0; $i < (int)$outNBElement; $i++) {
                $outReal[$i] = sqrt($outReal[$i]) * $optInNbDev;
            }
        } else {
            for ($i = 0; $i < (int)$outNBElement; $i++) {
                $outReal[$i] = sqrt($outReal[$i]);
            }
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
    public static function tsf(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = Lookback::tsfLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx    = 0;
            $outNBElement = 0;

            return ReturnCode::Success;
        }
        $outIdx  = 0;
        $today   = $startIdx;
        $SumX    = $optInTimePeriod * ($optInTimePeriod - 1) * 0.5;
        $SumXSqr = $optInTimePeriod * ($optInTimePeriod - 1) * (2 * $optInTimePeriod - 1) / 6;
        $Divisor = $SumX * $SumX - $optInTimePeriod * $SumXSqr;
        while ($today <= $endIdx) {
            $SumXY = 0;
            $SumY  = 0;
            for ($i = $optInTimePeriod; $i-- != 0;) {
                $SumY  += $tempValue1 = $inReal[$today - $i];
                $SumXY += (double)$i * $tempValue1;
            }
            $m                  = ($optInTimePeriod * $SumXY - $SumX * $SumY) / $Divisor;
            $b                  = ($SumY - $m * $SumX) / (double)$optInTimePeriod;
            $outReal[$outIdx++] = $b + $m * (double)$optInTimePeriod;
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
     * @param float $optInNbDev
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function variance(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, float $optInNbDev, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInNbDev == (-4e+37)) {
            $optInNbDev = 1.000000e+0;
        } elseif (($optInNbDev < -3.000000e+37) || ($optInNbDev > 3.000000e+37)) {
            return ReturnCode::BadParam;
        }

        return static::TA_INT_VAR(
            $startIdx, $endIdx, $inReal,
            $optInTimePeriod,
            $outBegIdx, $outNBElement, $outReal
        );
    }
}
