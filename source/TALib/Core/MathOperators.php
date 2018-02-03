<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class MathOperators
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal0
     * @param float[]  $inReal1
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function add(int $startIdx, int $endIdx, array $inReal0, array $inReal1, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] + $inReal1[$i];
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

}
