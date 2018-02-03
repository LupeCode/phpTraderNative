<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class PriceTransform
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function avgPrice(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $outIdx = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $outReal[$outIdx++] = ($inHigh[$i] + $inLow[$i] + $inClose[$i] + $inOpen[$i]) / 4;
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

}
