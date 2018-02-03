<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class MathTransform
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function acos(int $startIdx, int $endIdx, array $inReal, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = acos($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function asin(int $startIdx, int $endIdx, array $inReal, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = asin($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function atan(int $startIdx, int $endIdx, array $inReal, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = atan($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

}
