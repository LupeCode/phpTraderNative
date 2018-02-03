<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Classes\CandleSetting;
use LupeCode\phpTraderNative\TALib\Classes\MyInteger;
use LupeCode\phpTraderNative\TALib\Enum\CandleSettingType;
use LupeCode\phpTraderNative\TALib\Enum\Compatibility;
use LupeCode\phpTraderNative\TALib\Enum\RangeType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;
use LupeCode\phpTraderNative\TALib\Enum\UnstablePeriodFunctionID;

class VolumeIndicators extends Core
{
    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param float[] $inClose
     * @param float[] $inVolume
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public function ad(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        $nbBar               = $endIdx - $startIdx + 1;
        $outNBElement->value = $nbBar;
        $outBegIdx->value    = $startIdx;
        $currentBar          = $startIdx;
        $outIdx              = 0;
        $ad                  = 0.0;
        while ($nbBar != 0) {
            $high  = $inHigh[$currentBar];
            $low   = $inLow[$currentBar];
            $tmp   = $high - $low;
            $close = $inClose[$currentBar];
            if ($tmp > 0.0) {
                $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$currentBar]);
            }
            $outReal[$outIdx++] = $ad;
            $currentBar++;
            $nbBar--;
        }

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param float[] $inClose
     * @param float[] $inVolume
     * @param int     $optInFastPeriod
     * @param int     $optInSlowPeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public function adOsc(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, int $optInFastPeriod, int $optInSlowPeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 3;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 10;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        if ($optInFastPeriod < $optInSlowPeriod) {
            $slowestPeriod = $optInSlowPeriod;
        } else {
            $slowestPeriod = $optInFastPeriod;
        }
        $lookbackTotal = (new Lookback())->emaLookback($slowestPeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outBegIdx->value = $startIdx;
        $today            = $startIdx - $lookbackTotal;
        $ad               = 0.0;
        $fastK            = ((double)2.0 / ((double)($optInFastPeriod + 1)));
        $one_minus_fastK  = 1.0 - $fastK;
        $slowK            = ((double)2.0 / ((double)($optInSlowPeriod + 1)));
        $one_minus_slowK  = 1.0 - $slowK;
        {
            $high  = $inHigh[$today];
            $low   = $inLow[$today];
            $tmp   = $high - $low;
            $close = $inClose[$today];
            if ($tmp > 0.0) {
                $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$today]);
            }
            $today++;
        };
        $fastEMA = $ad;
        $slowEMA = $ad;
        while ($today < $startIdx) {
            {
                $high  = $inHigh[$today];
                $low   = $inLow[$today];
                $tmp   = $high - $low;
                $close = $inClose[$today];
                if ($tmp > 0.0) {
                    $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$today]);
                }
                $today++;
            };
            $fastEMA = ($fastK * $ad) + ($one_minus_fastK * $fastEMA);
            $slowEMA = ($slowK * $ad) + ($one_minus_slowK * $slowEMA);
        }
        $outIdx = 0;
        while ($today <= $endIdx) {
            {
                $high  = $inHigh[$today];
                $low   = $inLow[$today];
                $tmp   = $high - $low;
                $close = $inClose[$today];
                if ($tmp > 0.0) {
                    $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((double)$inVolume[$today]);
                }
                $today++;
            };
            $fastEMA            = ($fastK * $ad) + ($one_minus_fastK * $fastEMA);
            $slowEMA            = ($slowK * $ad) + ($one_minus_slowK * $slowEMA);
            $outReal[$outIdx++] = $fastEMA - $slowEMA;
        }
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int     $startIdx
     * @param int     $endIdx
     * @param float[] $inHigh
     * @param float[] $inLow
     * @param float[] $inClose
     * @param int     $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[] $outReal
     *
     * @return int
     */
    public function atr(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        $outBegIdx1    = new MyInteger();
        $outNbElement1 = new MyInteger();
        $prevATRTemp   = $this->double(1);
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
        $lookbackTotal       = (new Lookback())->atrLookback($optInTimePeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            return ReturnCode::Success;
        }
        if ($optInTimePeriod <= 1) {
            return (new VolatilityIndicators())->trueRange($startIdx, $endIdx, $inHigh, $inLow, $inClose, $outBegIdx, $outNBElement, $outReal);
        }
        $tempBuffer = $this->double($lookbackTotal + ($endIdx - $startIdx) + 1);
        $retCode    = (new VolatilityIndicators())->trueRange(($startIdx - $lookbackTotal + 1), $endIdx, $inHigh, $inLow, $inClose, $outBegIdx1, $outNbElement1, $tempBuffer);
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $retCode = $this->TA_INT_SMA($optInTimePeriod - 1, $optInTimePeriod - 1, $tempBuffer, $optInTimePeriod, $outBegIdx1, $outNbElement1, $prevATRTemp);
        if ($retCode != ReturnCode::Success) {
            return $retCode;
        }
        $prevATR = $prevATRTemp[0];
        $today   = $optInTimePeriod;
        $outIdx  = ($this->unstablePeriod[UnstablePeriodFunctionID::ATR]);
        while ($outIdx != 0) {
            $prevATR *= $optInTimePeriod - 1;
            $prevATR += $tempBuffer[$today++];
            $prevATR /= $optInTimePeriod;
            $outIdx--;
        }
        $outIdx     = 1;
        $outReal[0] = $prevATR;
        $nbATR      = ($endIdx - $startIdx) + 1;
        while (--$nbATR != 0) {
            $prevATR            *= $optInTimePeriod - 1;
            $prevATR            += $tempBuffer[$today++];
            $prevATR            /= $optInTimePeriod;
            $outReal[$outIdx++] = $prevATR;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return $retCode;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param array     $inVolume
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function obv(int $startIdx, int $endIdx, array $inReal, array &$inVolume, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $i;
        //int $outIdx;
        //double $prevReal, $tempReal, $prevOBV;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        $prevOBV  = $inVolume[$startIdx];
        $prevReal = $inReal[$startIdx];
        $outIdx   = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $tempReal = $inReal[$i];
            if ($tempReal > $prevReal) {
                $prevOBV += $inVolume[$i];
            } elseif ($tempReal < $prevReal) {
                $prevOBV -= $inVolume[$i];
            }
            $outReal[$outIdx++] = $prevOBV;
            $prevReal           = $tempReal;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }
}
