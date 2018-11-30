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

class MathTransform extends Core
{

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inReal
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function acos(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = acos($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inReal
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function asin(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = asin($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inReal
     * @param int     $outBegIdx
     * @param int     $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public static function atan(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = atan($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function ceil(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = ceil($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function cos(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = cos($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function cosh(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = cosh($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function exp(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = exp($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function floor(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = floor($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function ln(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = log($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function log10(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = log10($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function sin(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = sin($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function sinh(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = sinh($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function sqrt(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = sqrt($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function tan(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = tan($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int   $startIdx
     * @param int   $endIdx
     * @param array $inReal
     * @param int   $outBegIdx
     * @param int   $outNBElement
     * @param array $outReal
     *
     * @return int
     */
    public static function tanh(int $startIdx, int $endIdx, array $inReal, int &$outBegIdx, int &$outNBElement, array &$outReal): int
    {
        if ($RetCode = static::validateStartEndIndexes($startIdx, $endIdx)) {
            return $RetCode;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = tanh($inReal[$i]);
        }
        $outNBElement = $outIdx;
        $outBegIdx    = $startIdx;

        return ReturnCode::Success;
    }

}
