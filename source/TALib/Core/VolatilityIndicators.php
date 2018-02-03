<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Classes\CandleSetting;
use LupeCode\phpTraderNative\TALib\Classes\MyInteger;
use LupeCode\phpTraderNative\TALib\Enum\CandleSettingType;
use LupeCode\phpTraderNative\TALib\Enum\Compatibility;
use LupeCode\phpTraderNative\TALib\Enum\RangeType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;
use LupeCode\phpTraderNative\TALib\Enum\UnstablePeriodFunctionID;

class VolatilityIndicators extends Core
{

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inHigh
     * @param array     $inLow
     * @param array     $inClose
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function natr(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //ReturnCode $retCode;
        //int $outIdx, $today, $lookbackTotal;
        //int $nbATR;
        $outBegIdx1    = new MyInteger();
        $outNbElement1 = new MyInteger();
        //double $prevATR, $tempValue;
        //$tempBuffer;
        $prevATRTemp = $this->double(1);
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $outBegIdx->value    = 0;
        $outNBElement->value = 0;
        $lookbackTotal       = (new Lookback())->natrLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        if ($optInTimePeriod <= 1) {
            return $this->trueRange(
                $startIdx, $endIdx,
                $inHigh, $inLow, $inClose,
                $outBegIdx, $outNBElement, $outReal
            );
        }
        $tempBuffer = $this->double($lookbackTotal + ($endIdx - $startIdx) + 1);
        $retCode    = $this->trueRange(
            ($startIdx - $lookbackTotal + 1), $endIdx,
                                              $inHigh, $inLow, $inClose,
                                              $outBegIdx1, $outNbElement1,
                                              $tempBuffer
        );
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $retCode = $this->TA_INT_SMA(
            $optInTimePeriod - 1,
            $optInTimePeriod - 1,
            $tempBuffer, $optInTimePeriod,
            $outBegIdx1, $outNbElement1,
            $prevATRTemp
        );
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $prevATR = $prevATRTemp[0];
        $today   = $optInTimePeriod;
        $outIdx  = ($this->unstablePeriod[UnstablePeriodFunctionID::NATR]);
        while ($outIdx != 0) {
            $prevATR *= $optInTimePeriod - 1;
            $prevATR += $tempBuffer[$today++];
            $prevATR /= $optInTimePeriod;
            $outIdx--;
        }
        $outIdx    = 1;
        $tempValue = $inClose[$today];
        if (!(((-0.00000001) < $tempValue) && ($tempValue < 0.00000001))) {
            $outReal[0] = ($prevATR / $tempValue) * 100.0;
        } else {
            $outReal[0] = 0.0;
        }
        $nbATR = ($endIdx - $startIdx) + 1;
        while (--$nbATR != 0) {
            $prevATR   *= $optInTimePeriod - 1;
            $prevATR   += $tempBuffer[$today++];
            $prevATR   /= $optInTimePeriod;
            $tempValue = $inClose[$today];
            if (!(((-0.00000001) < $tempValue) && ($tempValue < 0.00000001))) {
                $outReal[$outIdx] = ($prevATR / $tempValue) * 100.0;
            } else {
                $outReal[0] = 0.0;
            }
            $outIdx++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return $retCode;
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
    public function trueRange(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $today, $outIdx;
        //double $val2, $val3, $greatest;
        //double $tempCY, $tempLT, $tempHT;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ($startIdx < 1) {
            $startIdx = 1;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outIdx = 0;
        $today  = $startIdx;
        while ($today <= $endIdx) {
            $tempLT   = $inLow[$today];
            $tempHT   = $inHigh[$today];
            $tempCY   = $inClose[$today - 1];
            $greatest = $tempHT - $tempLT;
            $val2     = abs($tempCY - $tempHT);
            if ($val2 > $greatest) {
                $greatest = $val2;
            }
            $val3 = abs($tempCY - $tempLT);
            if ($val3 > $greatest) {
                $greatest = $val3;
            }
            $outReal[$outIdx++] = $greatest;
            $today++;
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }
}
