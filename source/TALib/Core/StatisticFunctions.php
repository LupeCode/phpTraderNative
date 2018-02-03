<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class StatisticFunctions
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal0
     * @param float[]  $inReal1
     * @param int      $optInTimePeriod
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function beta(int $startIdx, int $endIdx, array $inReal0, array $inReal1, int $optInTimePeriod, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return RetCode::BadParam;
        }
        $nbInitialElementNeeded = $optInTimePeriod;
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
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
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

}
