<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Classes\MyInteger;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;

class PriceTransform extends Core
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function avgPrice(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        $outIdx = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $outReal[$outIdx++] = ($inHigh[$i] + $inLow[$i] + $inClose[$i] + $inOpen[$i]) / 4;
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inHigh
     * @param array     $inLow
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function medPrice(int $startIdx, int $endIdx, array $inHigh, array $inLow, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx, $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        $outIdx = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $outReal[$outIdx++] = ($inHigh[$i] + $inLow[$i]) / 2.0;
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inHigh
     * @param array     $inLow
     * @param array     $inClose
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function typPrice(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx, $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        $outIdx = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $outReal[$outIdx++] = ($inHigh[$i] +
                                   $inLow[$i] +
                                   $inClose[$i]) / 3.0;
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inHigh
     * @param array     $inLow
     * @param array     $inClose
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function wclPrice(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx, $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        $outIdx = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $outReal[$outIdx++] = ($inHigh[$i] +
                                   $inLow[$i] +
                                   ($inClose[$i] * 2.0)) / 4.0;
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }
}
