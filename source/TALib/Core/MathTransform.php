<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Classes\MyInteger;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;

class MathTransform extends Core
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function acos(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = acos($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function asin(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = asin($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function atan(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = atan($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function ceil(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = ceil($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function cos(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = cos($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function cosh(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = cosh($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function exp(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = exp($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function floor(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = floor($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function ln(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = log($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function log10(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = log10($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function sin(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = sin($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function sinh(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = sinh($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function sqrt(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = sqrt($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function tan(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = tan($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function tanh(int $startIdx, int $endIdx, array $inReal, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = tanh($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

}
