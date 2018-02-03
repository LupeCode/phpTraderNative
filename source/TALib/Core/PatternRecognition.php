<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\ConvertedJava\CandleSettingType;
use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RangeType;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class PatternRecognition extends Core
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
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdl2Crows(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdl2CrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                   = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inOpen[$i] < $inOpen[$i - 1] && $inOpen[$i] > $inClose[$i - 1] &&
                $inClose[$i] > $inOpen[$i - 2] && $inClose[$i] < $inClose[$i - 2]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdl3BlackCrows(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdl3BlackCrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                             = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[2] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == 1 &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] < $inOpen[$i - 2] && $inOpen[$i - 1] > $inClose[$i - 2] &&
                $inOpen[$i] < $inOpen[$i - 1] && $inOpen[$i] > $inClose[$i - 1] &&
                $inHigh[$i - 3] > $inClose[$i - 2] &&
                $inClose[$i - 2] > $inClose[$i - 1] &&
                $inClose[$i - 1] > $inClose[$i]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdl3Inside(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdl3InsideLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) < ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) > ((($inClose[$i - 2]) < ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                ((($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 && ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 && $inClose[$i] < $inOpen[$i - 2])
                 ||
                 (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 && ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 && $inClose[$i] > $inOpen[$i - 2])
                )
            ) {
                $outInteger[$outIdx++] = -($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdl3LineStrike(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdl3LineStrikeLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $NearPeriodTotal[3] = 0;
        $NearPeriodTotal[2] = 0;
        $NearTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $i                  = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[3] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $NearPeriodTotal[2] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                $inOpen[$i - 2] >= ((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[3] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i - 2] <= ((($inOpen[$i - 3]) > ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[3] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] >= ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] <= ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    (
                        ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                        $inClose[$i - 1] > $inClose[$i - 2] && $inClose[$i - 2] > $inClose[$i - 3] &&
                        $inOpen[$i] > $inClose[$i - 1] &&
                        $inClose[$i] < $inOpen[$i - 3]
                    ) ||
                    (
                        ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                        $inClose[$i - 1] < $inClose[$i - 2] && $inClose[$i - 2] < $inClose[$i - 3] &&
                        $inOpen[$i] < $inClose[$i - 1] &&
                        $inClose[$i] > $inOpen[$i - 3]
                    )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 3; $totIdx >= 2; --$totIdx) {
                $NearPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdl3Outside(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdl3OutsideLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                 $inClose[$i - 1] > $inOpen[$i - 2] && $inOpen[$i - 1] < $inClose[$i - 2] &&
                 $inClose[$i] > $inClose[$i - 1]
                )
                ||
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                 $inOpen[$i - 1] > $inClose[$i - 2] && $inClose[$i - 1] < $inOpen[$i - 2] &&
                 $inClose[$i] < $inClose[$i - 1]
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdl3StarsInSouth(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        $ShadowVeryShortPeriodTotal = $this->double(2);
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdl3StarsInSouthLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal           = 0;
        $BodyLongTrailingIdx           = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $ShadowLongPeriodTotal         = 0;
        $ShadowLongTrailingIdx         = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $BodyShortPeriodTotal          = 0;
        $BodyShortTrailingIdx          = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                             = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (abs($inClose[$i - 2] - $inOpen[$i - 2])) &&
                $inOpen[$i - 1] > $inClose[$i - 2] && $inOpen[$i - 1] <= $inHigh[$i - 2] &&
                $inLow[$i - 1] < $inClose[$i - 2] &&
                $inLow[$i - 1] >= $inLow[$i - 2] &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inLow[$i] > $inLow[$i - 1] && $inHigh[$i] < $inHigh[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))
                                      - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 2] - $inOpen[$BodyLongTrailingIdx - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 2] - $inLow[$BodyLongTrailingIdx - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 2] - ($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inClose[$BodyLongTrailingIdx - 2] : $inOpen[$BodyLongTrailingIdx - 2])) + (($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inOpen[$BodyLongTrailingIdx - 2] : $inClose[$BodyLongTrailingIdx - 2]) - $inLow[$BodyLongTrailingIdx - 2]) : 0)));
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))
                                      - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx - 2] - $inOpen[$ShadowLongTrailingIdx - 2])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx - 2] - $inLow[$ShadowLongTrailingIdx - 2]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx - 2] - ($inClose[$ShadowLongTrailingIdx - 2] >= $inOpen[$ShadowLongTrailingIdx - 2] ? $inClose[$ShadowLongTrailingIdx - 2] : $inOpen[$ShadowLongTrailingIdx - 2])) + (($inClose[$ShadowLongTrailingIdx - 2] >= $inOpen[$ShadowLongTrailingIdx - 2] ? $inOpen[$ShadowLongTrailingIdx - 2] : $inClose[$ShadowLongTrailingIdx - 2]) - $inLow[$ShadowLongTrailingIdx - 2]) : 0)));
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                     - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdl3WhiteSoldiers(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        $ShadowVeryShortPeriodTotal = $this->double(3);
        $NearPeriodTotal            = $this->double(3);
        $FarPeriodTotal             = $this->double(3);
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdl3WhiteSoldiersLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $NearPeriodTotal[2]            = 0;
        $NearPeriodTotal[1]            = 0;
        $NearPeriodTotal[0]            = 0;
        $NearTrailingIdx               = $startIdx - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $FarPeriodTotal[2]             = 0;
        $FarPeriodTotal[1]             = 0;
        $FarPeriodTotal[0]             = 0;
        $FarTrailingIdx                = $startIdx - ($this->candleSettings[CandleSettingType::Far]->avgPeriod);
        $BodyShortPeriodTotal          = 0;
        $BodyShortTrailingIdx          = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                             = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[2] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[2] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $NearPeriodTotal[1] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $FarTrailingIdx;
        while ($i < $startIdx) {
            $FarPeriodTotal[2] += (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $FarPeriodTotal[1] += (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] > $inClose[$i - 1] && $inClose[$i - 1] > $inClose[$i - 2] &&
                $inOpen[$i - 1] > $inOpen[$i - 2] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] > $inOpen[$i - 1] &&
                $inOpen[$i] <= $inClose[$i - 1] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (abs($inClose[$i - 2] - $inOpen[$i - 2])) - (($this->candleSettings[CandleSettingType::Far]->factor) * (($this->candleSettings[CandleSettingType::Far]->avgPeriod) != 0.0 ? $FarPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Far]->avgPeriod) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (abs($inClose[$i - 1] - $inOpen[$i - 1])) - (($this->candleSettings[CandleSettingType::Far]->factor) * (($this->candleSettings[CandleSettingType::Far]->avgPeriod) != 0.0 ? $FarPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Far]->avgPeriod) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $FarPeriodTotal[$totIdx]  += (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$FarTrailingIdx - $totIdx] - $inOpen[$FarTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$FarTrailingIdx - $totIdx] - $inLow[$FarTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$FarTrailingIdx - $totIdx] - ($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inClose[$FarTrailingIdx - $totIdx] : $inOpen[$FarTrailingIdx - $totIdx])) + (($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inOpen[$FarTrailingIdx - $totIdx] : $inClose[$FarTrailingIdx - $totIdx]) - $inLow[$FarTrailingIdx - $totIdx]) : 0)));
                $NearPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
            $FarTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param float    $optInPenetration
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlAbandonedBaby(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, float $optInPenetration, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = $this->cdlAbandonedBabyLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyDojiPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyDojiTrailingIdx  = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                  ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                  $inClose[$i] < $inClose[$i - 2] - (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration &&
                  ($inLow[$i - 1] > $inHigh[$i - 2]) &&
                  ($inHigh[$i] < $inLow[$i - 1])
                 )
                 ||
                 (
                     ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                     ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                     $inClose[$i] > $inClose[$i - 2] + (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration &&
                     ($inHigh[$i - 1] < $inLow[$i - 2]) &&
                     ($inLow[$i] > $inHigh[$i - 1])
                 )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlAdvanceBlock(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        $ShadowShortPeriodTotal = $this->double(3);
        $ShadowLongPeriodTotal  = $this->double(2);
        $NearPeriodTotal        = $this->double(3);
        $FarPeriodTotal         = $this->double(3);
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlAdvanceBlockLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowShortPeriodTotal[2] = 0;
        $ShadowShortPeriodTotal[1] = 0;
        $ShadowShortPeriodTotal[0] = 0;
        $ShadowShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod);
        $ShadowLongPeriodTotal[1]  = 0;
        $ShadowLongPeriodTotal[0]  = 0;
        $ShadowLongTrailingIdx     = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $NearPeriodTotal[2]        = 0;
        $NearPeriodTotal[1]        = 0;
        $NearPeriodTotal[0]        = 0;
        $NearTrailingIdx           = $startIdx - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $FarPeriodTotal[2]         = 0;
        $FarPeriodTotal[1]         = 0;
        $FarPeriodTotal[0]         = 0;
        $FarTrailingIdx            = $startIdx - ($this->candleSettings[CandleSettingType::Far]->avgPeriod);
        $BodyLongPeriodTotal       = 0;
        $BodyLongTrailingIdx       = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                         = $ShadowShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowShortPeriodTotal[2] += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowShortPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowLongPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[2] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $NearPeriodTotal[1] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $FarTrailingIdx;
        while ($i < $startIdx) {
            $FarPeriodTotal[2] += (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $FarPeriodTotal[1] += (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 1] && $inClose[$i - 1] > $inClose[$i - 2] &&
                $inOpen[$i - 1] > $inOpen[$i - 2] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] > $inOpen[$i - 1] &&
                $inOpen[$i] <= $inClose[$i - 1] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) < (($this->candleSettings[CandleSettingType::ShadowShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) != 0.0 ? $ShadowShortPeriodTotal[2] / ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    (
                        (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (abs($inClose[$i - 2] - $inOpen[$i - 2])) - (($this->candleSettings[CandleSettingType::Far]->factor) * (($this->candleSettings[CandleSettingType::Far]->avgPeriod) != 0.0 ? $FarPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Far]->avgPeriod) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    ) ||
                    (
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) - (($this->candleSettings[CandleSettingType::Far]->factor) * (($this->candleSettings[CandleSettingType::Far]->avgPeriod) != 0.0 ? $FarPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Far]->avgPeriod) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    ) ||
                    (
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) &&
                        (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (abs($inClose[$i - 2] - $inOpen[$i - 2])) &&
                        (
                            ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) != 0.0 ? $ShadowShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) ||
                            ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::ShadowShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) != 0.0 ? $ShadowShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                        )
                    ) ||
                    (
                        (abs($inClose[$i] - $inOpen[$i])) < (abs($inClose[$i - 1] - $inOpen[$i - 1])) &&
                        ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    )
                )
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                    - (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowShortTrailingIdx - $totIdx] - $inOpen[$ShadowShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowShortTrailingIdx - $totIdx] - $inLow[$ShadowShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowShortTrailingIdx - $totIdx] - ($inClose[$ShadowShortTrailingIdx - $totIdx] >= $inOpen[$ShadowShortTrailingIdx - $totIdx] ? $inClose[$ShadowShortTrailingIdx - $totIdx] : $inOpen[$ShadowShortTrailingIdx - $totIdx])) + (($inClose[$ShadowShortTrailingIdx - $totIdx] >= $inOpen[$ShadowShortTrailingIdx - $totIdx] ? $inOpen[$ShadowShortTrailingIdx - $totIdx] : $inClose[$ShadowShortTrailingIdx - $totIdx]) - $inLow[$ShadowShortTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $ShadowLongPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                   - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx - $totIdx] - $inOpen[$ShadowLongTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx - $totIdx] - $inLow[$ShadowLongTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx - $totIdx] - ($inClose[$ShadowLongTrailingIdx - $totIdx] >= $inOpen[$ShadowLongTrailingIdx - $totIdx] ? $inClose[$ShadowLongTrailingIdx - $totIdx] : $inOpen[$ShadowLongTrailingIdx - $totIdx])) + (($inClose[$ShadowLongTrailingIdx - $totIdx] >= $inOpen[$ShadowLongTrailingIdx - $totIdx] ? $inOpen[$ShadowLongTrailingIdx - $totIdx] : $inClose[$ShadowLongTrailingIdx - $totIdx]) - $inLow[$ShadowLongTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $FarPeriodTotal[$totIdx]  += (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::RealBody ? (abs($inClose[$FarTrailingIdx - $totIdx] - $inOpen[$FarTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::HighLow ? ($inHigh[$FarTrailingIdx - $totIdx] - $inLow[$FarTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::Far]->rangeType) == RangeType::Shadows ? ($inHigh[$FarTrailingIdx - $totIdx] - ($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inClose[$FarTrailingIdx - $totIdx] : $inOpen[$FarTrailingIdx - $totIdx])) + (($inClose[$FarTrailingIdx - $totIdx] >= $inOpen[$FarTrailingIdx - $totIdx] ? $inOpen[$FarTrailingIdx - $totIdx] : $inClose[$FarTrailingIdx - $totIdx]) - $inLow[$FarTrailingIdx - $totIdx]) : 0)));
                $NearPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 2] - $inOpen[$BodyLongTrailingIdx - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 2] - $inLow[$BodyLongTrailingIdx - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 2] - ($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inClose[$BodyLongTrailingIdx - 2] : $inOpen[$BodyLongTrailingIdx - 2])) + (($inClose[$BodyLongTrailingIdx - 2] >= $inOpen[$BodyLongTrailingIdx - 2] ? $inOpen[$BodyLongTrailingIdx - 2] : $inClose[$BodyLongTrailingIdx - 2]) - $inLow[$BodyLongTrailingIdx - 2]) : 0)));
            $i++;
            $ShadowShortTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $NearTrailingIdx++;
            $FarTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlBeltHold(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlBeltHoldLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal        = 0;
        $BodyLongTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                        (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    ) ||
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                        ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    )
                )) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal        += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlBreakaway(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlBreakawayLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                   = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 4] - $inOpen[$i - 4])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) &&
                ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (
                    (($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == -1 &&
                     (((($inOpen[$i - 3]) > ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) < ((($inOpen[$i - 4]) < ($inClose[$i - 4])) ? ($inOpen[$i - 4]) : ($inClose[$i - 4]))) &&
                     $inHigh[$i - 2] < $inHigh[$i - 3] && $inLow[$i - 2] < $inLow[$i - 3] &&
                     $inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] < $inLow[$i - 2] &&
                     $inClose[$i] > $inOpen[$i - 3] && $inClose[$i] < $inClose[$i - 4]
                    )
                    ||
                    (($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == 1 &&
                     (((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) > ((($inOpen[$i - 4]) > ($inClose[$i - 4])) ? ($inOpen[$i - 4]) : ($inClose[$i - 4]))) &&
                     $inHigh[$i - 2] > $inHigh[$i - 3] && $inLow[$i - 2] > $inLow[$i - 3] &&
                     $inHigh[$i - 1] > $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                     $inClose[$i] < $inOpen[$i - 3] && $inClose[$i] > $inClose[$i - 4]
                    )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))
                                    - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 4] - $inOpen[$BodyLongTrailingIdx - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 4] - $inLow[$BodyLongTrailingIdx - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 4] - ($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inClose[$BodyLongTrailingIdx - 4] : $inOpen[$BodyLongTrailingIdx - 4])) + (($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inOpen[$BodyLongTrailingIdx - 4] : $inClose[$BodyLongTrailingIdx - 4]) - $inLow[$BodyLongTrailingIdx - 4]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlClosingMarubozu(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlClosingMarubozuLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal        = 0;
        $BodyLongTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                        ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    ) ||
                    (
                        ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                        (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    )
                )) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal        += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlConcealBabysWall(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        $ShadowVeryShortPeriodTotal = $this->double(4);
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlConcealBabysWallLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal[3] = 0;
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                             = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[3] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $ShadowVeryShortPeriodTotal[2] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == -1 &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[3] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[3] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inHigh[$i - 1] > $inClose[$i - 2] &&
                $inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 3; $totIdx >= 1; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlCounterAttack(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        $BodyLongPeriodTotal = $this->double(2);
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlCounterAttackLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $EqualPeriodTotal       = 0;
        $EqualTrailingIdx       = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $BodyLongPeriodTotal[1] = 0;
        $BodyLongPeriodTotal[0] = 0;
        $BodyLongTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                      = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] <= $inClose[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 1] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inOpen
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param float    $optInPenetration
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param int[]    $outInteger
     *
     * @return int
     */
    public function cdlDarkCloudCover(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, float $optInPenetration, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outInteger): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = $this->cdlDarkCloudCoverLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                   = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inOpen[$i] > $inHigh[$i - 1] &&
                $inClose[$i] > $inOpen[$i - 1] &&
                $inClose[$i] < $inClose[$i - 1] - (abs($inClose[$i - 1] - $inOpen[$i - 1])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlDoji(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyDojiPeriodTotal = 0;
        $BodyDojiTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $i                   = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlDojiStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlDojiStarLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal = 0;
        $BodyDojiPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyDojiTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $i                   = $BodyLongTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && (((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))))
                 ||
                 (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && (((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))))
                )) {
                $outInteger[$outIdx++] = -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlDragonflyDoji(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $ShadowVeryShortPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $ShadowVeryShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlDragonflyDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyDojiPeriodTotal        = 0;
        $BodyDojiTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal        += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlEngulfing(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //int $i, $outIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlEngulfingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 && ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                 $inClose[$i] > $inOpen[$i - 1] && $inOpen[$i] < $inClose[$i - 1]
                )
                ||
                (($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 && ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                 $inOpen[$i] > $inClose[$i - 1] && $inClose[$i] < $inOpen[$i - 1]
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlEveningDojiStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $BodyLongPeriodTotal, $BodyShortPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $BodyLongTrailingIdx, $BodyShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = $this->cdlEveningDojiStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyDojiPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyDojiTrailingIdx  = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inClose[$i] < $inClose[$i - 2] - (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlEveningStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyShortPeriodTotal, $BodyLongPeriodTotal, $BodyShortPeriodTotal2;
        //int $i, $outIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = $this->cdlEveningStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal   = 0;
        $BodyShortPeriodTotal  = 0;
        $BodyShortPeriodTotal2 = 0;
        $BodyLongTrailingIdx   = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortTrailingIdx  = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                     = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $BodyShortPeriodTotal2 += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i + 1] - $inOpen[$i + 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i + 1] - $inLow[$i + 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i + 1] - ($inClose[$i + 1] >= $inOpen[$i + 1] ? $inClose[$i + 1] : $inOpen[$i + 1])) + (($inClose[$i + 1] >= $inOpen[$i + 1] ? $inOpen[$i + 1] : $inClose[$i + 1]) - $inLow[$i + 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal2 / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inClose[$i] < $inClose[$i - 2] - (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $BodyShortPeriodTotal2 += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx + 1] - $inOpen[$BodyShortTrailingIdx + 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx + 1] - $inLow[$BodyShortTrailingIdx + 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx + 1] - ($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inClose[$BodyShortTrailingIdx + 1] : $inOpen[$BodyShortTrailingIdx + 1])) + (($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inOpen[$BodyShortTrailingIdx + 1] : $inClose[$BodyShortTrailingIdx + 1]) - $inLow[$BodyShortTrailingIdx + 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlGapSideSideWhite(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $NearPeriodTotal, $EqualPeriodTotal;
        //int $i, $outIdx, $NearTrailingIdx, $EqualTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlGapSideSideWhiteLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $NearPeriodTotal  = 0;
        $EqualPeriodTotal = 0;
        $NearTrailingIdx  = $startIdx - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $EqualTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $i                = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (
                (
                    ((((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) && (((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))))
                    ||
                    ((((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) && (((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))))
                ) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                (abs($inClose[$i] - $inOpen[$i])) >= (abs($inClose[$i - 1] - $inOpen[$i - 1])) - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= (abs($inClose[$i - 1] - $inOpen[$i - 1])) + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inOpen[$i - 1] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] <= $inOpen[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = ((((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) ? 100 : -100);
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $NearPeriodTotal  += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - 1] - $inOpen[$NearTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - 1] - $inLow[$NearTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - 1] - ($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inClose[$NearTrailingIdx - 1] : $inOpen[$NearTrailingIdx - 1])) + (($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inOpen[$NearTrailingIdx - 1] : $inClose[$NearTrailingIdx - 1]) - $inLow[$NearTrailingIdx - 1]) : 0)));
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $i++;
            $NearTrailingIdx++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlGravestoneDoji(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $ShadowVeryShortPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $ShadowVeryShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlGravestoneDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyDojiPeriodTotal        = 0;
        $BodyDojiTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal        += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHammer(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal, $ShadowLongPeriodTotal, $ShadowVeryShortPeriodTotal, $NearPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $ShadowLongTrailingIdx, $ShadowVeryShortTrailingIdx, $NearTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHammerLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal            = 0;
        $BodyTrailingIdx            = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $ShadowLongPeriodTotal      = 0;
        $ShadowLongTrailingIdx      = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $NearPeriodTotal            = 0;
        $NearTrailingIdx            = $startIdx - 1 - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $i                          = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx - 1) {
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) <= $inLow[$i - 1] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal            += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal      += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $NearPeriodTotal            += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx] - $inOpen[$NearTrailingIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx] - $inLow[$NearTrailingIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx] - ($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inClose[$NearTrailingIdx] : $inOpen[$NearTrailingIdx])) + (($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inOpen[$NearTrailingIdx] : $inClose[$NearTrailingIdx]) - $inLow[$NearTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHangingMan(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal, $ShadowLongPeriodTotal, $ShadowVeryShortPeriodTotal, $NearPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $ShadowLongTrailingIdx, $ShadowVeryShortTrailingIdx, $NearTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHangingManLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal            = 0;
        $BodyTrailingIdx            = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $ShadowLongPeriodTotal      = 0;
        $ShadowLongTrailingIdx      = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $NearPeriodTotal            = 0;
        $NearTrailingIdx            = $startIdx - 1 - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $i                          = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx - 1) {
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) >= $inHigh[$i - 1] - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal            += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal      += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $NearPeriodTotal            += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx] - $inOpen[$NearTrailingIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx] - $inLow[$NearTrailingIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx] - ($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inClose[$NearTrailingIdx] : $inOpen[$NearTrailingIdx])) + (($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inOpen[$NearTrailingIdx] : $inClose[$NearTrailingIdx]) - $inLow[$NearTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHarami(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyShortPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHaramiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i]) > ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) < ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) > ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1]))
            ) {
                $outInteger[$outIdx++] = -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHaramiCross(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHaramiCrossLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal = 0;
        $BodyDojiPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyDojiTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $i                   = $BodyLongTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i]) > ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) < ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                ((($inClose[$i]) < ($inOpen[$i])) ? ($inClose[$i]) : ($inOpen[$i])) > ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1]))
            ) {
                $outInteger[$outIdx++] = -($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHighWave(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal, $ShadowPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $ShadowTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHighWaveLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal   = 0;
        $BodyTrailingIdx   = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $ShadowPeriodTotal = 0;
        $ShadowTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod);
        $i                 = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowTrailingIdx;
        while ($i < $startIdx) {
            $ShadowPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowVeryLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (($this->candleSettings[CandleSettingType::ShadowVeryLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowTrailingIdx] - $inOpen[$ShadowTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowTrailingIdx] - $inLow[$ShadowTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowTrailingIdx] - ($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inClose[$ShadowTrailingIdx] : $inOpen[$ShadowTrailingIdx])) + (($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inOpen[$ShadowTrailingIdx] : $inClose[$ShadowTrailingIdx]) - $inLow[$ShadowTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHikkake(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //int $i, $outIdx, $lookbackTotal, $patternIdx, $patternResult;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHikkakeLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $patternIdx    = 0;
        $patternResult = 0;
        $i             = $startIdx - 3;
        while ($i < $startIdx) {
            if ($inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1])
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1])
                )
            ) {
                $patternResult = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx    = $i;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $patternIdx = 0;
            }
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ($inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1])
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1])
                )
            ) {
                $patternResult         = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx            = $i;
                $outInteger[$outIdx++] = $patternResult;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $outInteger[$outIdx++] = $patternResult + 100 * ($patternResult > 0 ? 1 : -1);
                $patternIdx            = 0;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHikkakeMod(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $NearPeriodTotal;
        //int $i, $outIdx, $NearTrailingIdx, $lookbackTotal, $patternIdx, $patternResult;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHikkakeModLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $NearPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - 3 - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $i               = $NearTrailingIdx;
        while ($i < $startIdx - 3) {
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $patternIdx    = 0;
        $patternResult = 0;
        $i             = $startIdx - 3;
        while ($i < $startIdx) {
            if ($inHigh[$i - 2] < $inHigh[$i - 3] && $inLow[$i - 2] > $inLow[$i - 3] &&
                $inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1] &&
                  $inClose[$i - 2] <= $inLow[$i - 2] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                 )
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1] &&
                  $inClose[$i - 2] >= $inHigh[$i - 2] - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                 )
                )
            ) {
                $patternResult = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx    = $i;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $patternIdx = 0;
            }
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - 2] - $inOpen[$NearTrailingIdx - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - 2] - $inLow[$NearTrailingIdx - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - 2] - ($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inClose[$NearTrailingIdx - 2] : $inOpen[$NearTrailingIdx - 2])) + (($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inOpen[$NearTrailingIdx - 2] : $inClose[$NearTrailingIdx - 2]) - $inLow[$NearTrailingIdx - 2]) : 0)));
            $NearTrailingIdx++;
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ($inHigh[$i - 2] < $inHigh[$i - 3] && $inLow[$i - 2] > $inLow[$i - 3] &&
                $inHigh[$i - 1] < $inHigh[$i - 2] && $inLow[$i - 1] > $inLow[$i - 2] &&
                (($inHigh[$i] < $inHigh[$i - 1] && $inLow[$i] < $inLow[$i - 1] &&
                  $inClose[$i - 2] <= $inLow[$i - 2] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                 )
                 ||
                 ($inHigh[$i] > $inHigh[$i - 1] && $inLow[$i] > $inLow[$i - 1] &&
                  $inClose[$i - 2] >= $inHigh[$i - 2] - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                 )
                )
            ) {
                $patternResult         = 100 * ($inHigh[$i] < $inHigh[$i - 1] ? 1 : -1);
                $patternIdx            = $i;
                $outInteger[$outIdx++] = $patternResult;
            } elseif ($i <= $patternIdx + 3 &&
                      (($patternResult > 0 && $inClose[$i] > $inHigh[$patternIdx - 1])
                       ||
                       ($patternResult < 0 && $inClose[$i] < $inLow[$patternIdx - 1])
                      )
            ) {
                $outInteger[$outIdx++] = $patternResult + 100 * ($patternResult > 0 ? 1 : -1);
                $patternIdx            = 0;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - 2] - $inOpen[$NearTrailingIdx - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - 2] - $inLow[$NearTrailingIdx - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - 2] - ($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inClose[$NearTrailingIdx - 2] : $inOpen[$NearTrailingIdx - 2])) + (($inClose[$NearTrailingIdx - 2] >= $inOpen[$NearTrailingIdx - 2] ? $inOpen[$NearTrailingIdx - 2] : $inClose[$NearTrailingIdx - 2]) - $inLow[$NearTrailingIdx - 2]) : 0)));
            $NearTrailingIdx++;
            $i++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlHomingPigeon(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyShortPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlHomingPigeonLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] < $inOpen[$i - 1] &&
                $inClose[$i] > $inClose[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlIdentical3Crows(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        $ShadowVeryShortPeriodTotal = $this->double(3);
        $EqualPeriodTotal           = $this->double(3);
        //int $i, $outIdx, $totIdx, $ShadowVeryShortTrailingIdx, $EqualTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlIdentical3CrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal[2] = 0;
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $EqualPeriodTotal[2]           = 0;
        $EqualPeriodTotal[1]           = 0;
        $EqualPeriodTotal[0]           = 0;
        $EqualTrailingIdx              = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $i                             = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[2] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $ShadowVeryShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal[2] += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $EqualPeriodTotal[1] += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[2] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i - 2] > $inClose[$i - 1] &&
                $inClose[$i - 1] > $inClose[$i] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] >= $inClose[$i - 2] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] <= $inClose[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inClose[$i - 1] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 0; --$totIdx) {
                $ShadowVeryShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $EqualPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                              - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - $totIdx] - $inOpen[$EqualTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - $totIdx] - $inLow[$EqualTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - $totIdx] - ($inClose[$EqualTrailingIdx - $totIdx] >= $inOpen[$EqualTrailingIdx - $totIdx] ? $inClose[$EqualTrailingIdx - $totIdx] : $inOpen[$EqualTrailingIdx - $totIdx])) + (($inClose[$EqualTrailingIdx - $totIdx] >= $inOpen[$EqualTrailingIdx - $totIdx] ? $inOpen[$EqualTrailingIdx - $totIdx] : $inClose[$EqualTrailingIdx - $totIdx]) - $inLow[$EqualTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlInNeck(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $EqualPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $EqualTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlInNeckLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $EqualPeriodTotal    = 0;
        $EqualTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                   = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] <= $inClose[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 1]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal    += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                    - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlInvertedHammer(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal, $ShadowLongPeriodTotal, $ShadowVeryShortPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $ShadowLongTrailingIdx, $ShadowVeryShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlInvertedHammerLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal            = 0;
        $BodyTrailingIdx            = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $ShadowLongPeriodTotal      = 0;
        $ShadowLongTrailingIdx      = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])))) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal            += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal      += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlKicking(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        $ShadowVeryShortPeriodTotal = $this->double(2);
        $BodyLongPeriodTotal        = $this->double(2);
        //int $i, $outIdx, $totIdx, $ShadowVeryShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlKickingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $BodyLongPeriodTotal[1]        = 0;
        $BodyLongPeriodTotal[0]        = 0;
        $BodyLongTrailingIdx           = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                             = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && ($inLow[$i] > $inHigh[$i - 1]))
                    ||
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && ($inHigh[$i] < $inLow[$i - 1]))
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx]        += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
                $ShadowVeryShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlKickingByLength(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        $ShadowVeryShortPeriodTotal = $this->double(2);
        $BodyLongPeriodTotal        = $this->double(2);
        //int $i, $outIdx, $totIdx, $ShadowVeryShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlKickingByLengthLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal[1] = 0;
        $ShadowVeryShortPeriodTotal[0] = 0;
        $ShadowVeryShortTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $BodyLongPeriodTotal[1]        = 0;
        $BodyLongPeriodTotal[0]        = 0;
        $BodyLongTrailingIdx           = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                             = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal[1] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $ShadowVeryShortPeriodTotal[0] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[1] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal[0] / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 && ($inLow[$i] > $inHigh[$i - 1]))
                    ||
                    (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 && ($inHigh[$i] < $inLow[$i - 1]))
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[((abs($inClose[$i] - $inOpen[$i])) > (abs($inClose[$i - 1] - $inOpen[$i - 1])) ? $i : $i - 1)] >= $inOpen[((abs($inClose[$i] - $inOpen[$i])) > (abs($inClose[$i - 1] - $inOpen[$i - 1])) ? $i : $i - 1)] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx]        += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
                $ShadowVeryShortPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                        - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - $totIdx] - $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - $totIdx] - ($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inClose[$ShadowVeryShortTrailingIdx - $totIdx] : $inOpen[$ShadowVeryShortTrailingIdx - $totIdx])) + (($inClose[$ShadowVeryShortTrailingIdx - $totIdx] >= $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] ? $inOpen[$ShadowVeryShortTrailingIdx - $totIdx] : $inClose[$ShadowVeryShortTrailingIdx - $totIdx]) - $inLow[$ShadowVeryShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlLadderBottom(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $ShadowVeryShortPeriodTotal;
        //int $i, $outIdx, $ShadowVeryShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlLadderBottomLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == -1 && ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == -1 && ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                $inOpen[$i - 4] > $inOpen[$i - 3] && $inOpen[$i - 3] > $inOpen[$i - 2] &&
                $inClose[$i - 4] > $inClose[$i - 3] && $inClose[$i - 3] > $inClose[$i - 2] &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] > $inOpen[$i - 1] &&
                $inClose[$i] > $inHigh[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - 1] - $inOpen[$ShadowVeryShortTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - $inLow[$ShadowVeryShortTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - ($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inClose[$ShadowVeryShortTrailingIdx - 1] : $inOpen[$ShadowVeryShortTrailingIdx - 1])) + (($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inOpen[$ShadowVeryShortTrailingIdx - 1] : $inClose[$ShadowVeryShortTrailingIdx - 1]) - $inLow[$ShadowVeryShortTrailingIdx - 1]) : 0)));
            $i++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlLongLeggedDoji(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $ShadowLongPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $ShadowLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlLongLeggedDojiLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyDojiPeriodTotal   = 0;
        $BodyDojiTrailingIdx   = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $i                     = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ((($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                 ||
                 ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                )
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlLongLine(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal, $ShadowPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $ShadowTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlLongLineLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal   = 0;
        $BodyTrailingIdx   = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $ShadowPeriodTotal = 0;
        $ShadowTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod);
        $i                 = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowTrailingIdx;
        while ($i < $startIdx) {
            $ShadowPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowTrailingIdx] - $inOpen[$ShadowTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowTrailingIdx] - $inLow[$ShadowTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowTrailingIdx] - ($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inClose[$ShadowTrailingIdx] : $inOpen[$ShadowTrailingIdx])) + (($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inOpen[$ShadowTrailingIdx] : $inClose[$ShadowTrailingIdx]) - $inLow[$ShadowTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlMarubozu(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyLongPeriodTotal, $ShadowVeryShortPeriodTotal;
        //int $i, $outIdx, $BodyLongTrailingIdx, $ShadowVeryShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlMarubozuLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal        = 0;
        $BodyLongTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal        += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlMatchingLow(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $EqualPeriodTotal;
        //int $i, $outIdx, $EqualTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlMatchingLowLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $i                = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inClose[$i] <= $inClose[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 1] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlMatHold(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        $BodyPeriodTotal = $this->double(5);
        //int $i, $outIdx, $totIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = $this->cdlMatHoldLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal[4]   = 0;
        $BodyPeriodTotal[3]   = 0;
        $BodyPeriodTotal[2]   = 0;
        $BodyPeriodTotal[1]   = 0;
        $BodyPeriodTotal[0]   = 0;
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $BodyLongTrailingIdx  = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                    = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[3] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $BodyPeriodTotal[2] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $BodyPeriodTotal[1] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[4] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (
                (abs($inClose[$i - 4] - $inOpen[$i - 4])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyPeriodTotal[4] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 3] - $inOpen[$i - 3])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal[3] / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal[2] / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal[1] / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == 1 &&
                ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == -1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                (((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) > ((($inOpen[$i - 4]) > ($inClose[$i - 4])) ? ($inOpen[$i - 4]) : ($inClose[$i - 4]))) &&
                ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) < $inClose[$i - 4] &&
                ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < $inClose[$i - 4] &&
                ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) > $inClose[$i - 4] - (abs($inClose[$i - 4] - $inOpen[$i - 4])) * $optInPenetration &&
                ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > $inClose[$i - 4] - (abs($inClose[$i - 4] - $inOpen[$i - 4])) * $optInPenetration &&
                ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) < $inOpen[$i - 3] &&
                ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) < ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                $inOpen[$i] > $inClose[$i - 1] &&
                $inClose[$i] > (((((($inHigh[$i - 3]) > ($inHigh[$i - 2])) ? ($inHigh[$i - 3]) : ($inHigh[$i - 2]))) > ($inHigh[$i - 1])) ? (((($inHigh[$i - 3]) > ($inHigh[$i - 2])) ? ($inHigh[$i - 3]) : ($inHigh[$i - 2]))) : ($inHigh[$i - 1]))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal[4] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 4] - $inOpen[$BodyLongTrailingIdx - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 4] - $inLow[$BodyLongTrailingIdx - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 4] - ($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inClose[$BodyLongTrailingIdx - 4] : $inOpen[$BodyLongTrailingIdx - 4])) + (($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inOpen[$BodyLongTrailingIdx - 4] : $inClose[$BodyLongTrailingIdx - 4]) - $inLow[$BodyLongTrailingIdx - 4]) : 0)));
            for ($totIdx = 3; $totIdx >= 1; --$totIdx) {
                $BodyPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx - $totIdx] - $inOpen[$BodyShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - $inLow[$BodyShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - ($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inClose[$BodyShortTrailingIdx - $totIdx] : $inOpen[$BodyShortTrailingIdx - $totIdx])) + (($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inOpen[$BodyShortTrailingIdx - $totIdx] : $inClose[$BodyShortTrailingIdx - $totIdx]) - $inLow[$BodyShortTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $BodyShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlMorningDojiStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $BodyLongPeriodTotal, $BodyShortPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $BodyLongTrailingIdx, $BodyShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = $this->cdlMorningDojiStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyDojiPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyDojiTrailingIdx  = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyDojiTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 2] + (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyDojiPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyDojiTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlMorningStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        float $optInPenetration,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyShortPeriodTotal, $BodyLongPeriodTotal, $BodyShortPeriodTotal2;
        //int $i, $outIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = $this->cdlMorningStarLookback($optInPenetration);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal   = 0;
        $BodyShortPeriodTotal  = 0;
        $BodyShortPeriodTotal2 = 0;
        $BodyLongTrailingIdx   = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortTrailingIdx  = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                     = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $BodyShortPeriodTotal2 += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i + 1] - $inOpen[$i + 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i + 1] - $inLow[$i + 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i + 1] - ($inClose[$i + 1] >= $inOpen[$i + 1] ? $inClose[$i + 1] : $inOpen[$i + 1])) + (($inClose[$i + 1] >= $inOpen[$i + 1] ? $inOpen[$i + 1] : $inClose[$i + 1]) - $inLow[$i + 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal2 / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 2] + (abs($inClose[$i - 2] - $inOpen[$i - 2])) * $optInPenetration
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $BodyShortPeriodTotal2 += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx + 1] - $inOpen[$BodyShortTrailingIdx + 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx + 1] - $inLow[$BodyShortTrailingIdx + 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx + 1] - ($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inClose[$BodyShortTrailingIdx + 1] : $inOpen[$BodyShortTrailingIdx + 1])) + (($inClose[$BodyShortTrailingIdx + 1] >= $inOpen[$BodyShortTrailingIdx + 1] ? $inOpen[$BodyShortTrailingIdx + 1] : $inClose[$BodyShortTrailingIdx + 1]) - $inLow[$BodyShortTrailingIdx + 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlOnNeck(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $EqualPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $EqualTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlOnNeckLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $EqualPeriodTotal    = 0;
        $EqualTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                   = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] <= $inLow[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inLow[$i - 1] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal    += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                    - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlPiercing(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        $BodyLongPeriodTotal = $this->double(2);
        //int $i, $outIdx, $totIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlPiercingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal[1] = 0;
        $BodyLongPeriodTotal[0] = 0;
        $BodyLongTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                      = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[1] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $BodyLongPeriodTotal[0] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[0] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] < $inOpen[$i - 1] &&
                $inClose[$i] > $inClose[$i - 1] + (abs($inClose[$i - 1] - $inOpen[$i - 1])) * 0.5
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 1; $totIdx >= 0; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
            }
            $i++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlRickshawMan(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $ShadowLongPeriodTotal, $NearPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $ShadowLongTrailingIdx, $NearTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlRickshawManLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyDojiPeriodTotal   = 0;
        $BodyDojiTrailingIdx   = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $ShadowLongPeriodTotal = 0;
        $ShadowLongTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $NearPeriodTotal       = 0;
        $NearTrailingIdx       = $startIdx - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $i                     = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    ((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i]))
                    <= $inLow[$i] + ($inHigh[$i] - $inLow[$i]) / 2 + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    &&
                    ((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i]))
                    >= $inLow[$i] + ($inHigh[$i] - $inLow[$i]) / 2 - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                )
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $NearPeriodTotal       += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx] - $inOpen[$NearTrailingIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx] - $inLow[$NearTrailingIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx] - ($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inClose[$NearTrailingIdx] : $inOpen[$NearTrailingIdx])) + (($inClose[$NearTrailingIdx] >= $inOpen[$NearTrailingIdx] ? $inOpen[$NearTrailingIdx] : $inClose[$NearTrailingIdx]) - $inLow[$NearTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlRiseFall3Methods(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        $BodyPeriodTotal = $this->double(5);
        //int $i, $outIdx, $totIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlRiseFall3MethodsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal[4]   = 0;
        $BodyPeriodTotal[3]   = 0;
        $BodyPeriodTotal[2]   = 0;
        $BodyPeriodTotal[1]   = 0;
        $BodyPeriodTotal[0]   = 0;
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $BodyLongTrailingIdx  = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                    = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[3] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)));
            $BodyPeriodTotal[2] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $BodyPeriodTotal[1] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal[4] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)));
            $BodyPeriodTotal[0] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (
                (abs($inClose[$i - 4] - $inOpen[$i - 4])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyPeriodTotal[4] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 3] - $inOpen[$i - 3])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal[3] / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 3] - $inOpen[$i - 3])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 3] - $inLow[$i - 3]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 3] - ($inClose[$i - 3] >= $inOpen[$i - 3] ? $inClose[$i - 3] : $inOpen[$i - 3])) + (($inClose[$i - 3] >= $inOpen[$i - 3] ? $inOpen[$i - 3] : $inClose[$i - 3]) - $inLow[$i - 3]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal[2] / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal[1] / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyPeriodTotal[0] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) == -($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) &&
                ($inClose[$i - 3] >= $inOpen[$i - 3] ? 1 : -1) == ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                ((($inOpen[$i - 3]) < ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) < $inHigh[$i - 4] && ((($inOpen[$i - 3]) > ($inClose[$i - 3])) ? ($inOpen[$i - 3]) : ($inClose[$i - 3])) > $inLow[$i - 4] &&
                ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) < $inHigh[$i - 4] && ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])) > $inLow[$i - 4] &&
                ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < $inHigh[$i - 4] && ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > $inLow[$i - 4] &&
                $inClose[$i - 2] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) < $inClose[$i - 3] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) &&
                $inClose[$i - 1] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) < $inClose[$i - 2] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) &&
                $inOpen[$i] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) > $inClose[$i - 1] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) &&
                $inClose[$i] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1) > $inClose[$i - 4] * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1)
            ) {
                $outInteger[$outIdx++] = 100 * ($inClose[$i - 4] >= $inOpen[$i - 4] ? 1 : -1);
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal[4] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 4] - $inOpen[$i - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 4] - $inLow[$i - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 4] - ($inClose[$i - 4] >= $inOpen[$i - 4] ? $inClose[$i - 4] : $inOpen[$i - 4])) + (($inClose[$i - 4] >= $inOpen[$i - 4] ? $inOpen[$i - 4] : $inClose[$i - 4]) - $inLow[$i - 4]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 4] - $inOpen[$BodyLongTrailingIdx - 4])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 4] - $inLow[$BodyLongTrailingIdx - 4]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 4] - ($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inClose[$BodyLongTrailingIdx - 4] : $inOpen[$BodyLongTrailingIdx - 4])) + (($inClose[$BodyLongTrailingIdx - 4] >= $inOpen[$BodyLongTrailingIdx - 4] ? $inOpen[$BodyLongTrailingIdx - 4] : $inClose[$BodyLongTrailingIdx - 4]) - $inLow[$BodyLongTrailingIdx - 4]) : 0)));
            for ($totIdx = 3; $totIdx >= 1; --$totIdx) {
                $BodyPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                             - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx - $totIdx] - $inOpen[$BodyShortTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - $inLow[$BodyShortTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx - $totIdx] - ($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inClose[$BodyShortTrailingIdx - $totIdx] : $inOpen[$BodyShortTrailingIdx - $totIdx])) + (($inClose[$BodyShortTrailingIdx - $totIdx] >= $inOpen[$BodyShortTrailingIdx - $totIdx] ? $inOpen[$BodyShortTrailingIdx - $totIdx] : $inClose[$BodyShortTrailingIdx - $totIdx]) - $inLow[$BodyShortTrailingIdx - $totIdx]) : 0)));
            }
            $BodyPeriodTotal[0] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $i++;
            $BodyShortTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlSeparatingLines(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $ShadowVeryShortPeriodTotal, $BodyLongPeriodTotal, $EqualPeriodTotal;
        //int $i, $outIdx, $ShadowVeryShortTrailingIdx, $BodyLongTrailingIdx, $EqualTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlSeparatingLinesLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $BodyLongPeriodTotal        = 0;
        $BodyLongTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $EqualPeriodTotal           = 0;
        $EqualTrailingIdx           = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $i                          = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                $inOpen[$i] <= $inOpen[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inOpen[$i - 1] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (
                    (($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                     (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    )
                    ||
                    (($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                     ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                    )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $BodyLongPeriodTotal        += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $EqualPeriodTotal           += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $i++;
            $ShadowVeryShortTrailingIdx++;
            $BodyLongTrailingIdx++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlShootingStar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal, $ShadowLongPeriodTotal, $ShadowVeryShortPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $ShadowLongTrailingIdx, $ShadowVeryShortTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlShootingStarLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal            = 0;
        $BodyTrailingIdx            = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $ShadowLongPeriodTotal      = 0;
        $ShadowLongTrailingIdx      = $startIdx - ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $i                          = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (($this->candleSettings[CandleSettingType::ShadowLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) != 0.0 ? $ShadowLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])))) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal            += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowLongPeriodTotal      += (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowLongTrailingIdx] - $inOpen[$ShadowLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowLongTrailingIdx] - $inLow[$ShadowLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowLongTrailingIdx] - ($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inClose[$ShadowLongTrailingIdx] : $inOpen[$ShadowLongTrailingIdx])) + (($inClose[$ShadowLongTrailingIdx] >= $inOpen[$ShadowLongTrailingIdx] ? $inOpen[$ShadowLongTrailingIdx] : $inClose[$ShadowLongTrailingIdx]) - $inLow[$ShadowLongTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowLongTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlShortLine(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal, $ShadowPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $ShadowTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlShortLineLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal   = 0;
        $BodyTrailingIdx   = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $ShadowPeriodTotal = 0;
        $ShadowTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod);
        $i                 = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowTrailingIdx;
        while ($i < $startIdx) {
            $ShadowPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) < (($this->candleSettings[CandleSettingType::ShadowShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) != 0.0 ? $ShadowPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal   += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $ShadowPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowTrailingIdx] - $inOpen[$ShadowTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowTrailingIdx] - $inLow[$ShadowTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowTrailingIdx] - ($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inClose[$ShadowTrailingIdx] : $inOpen[$ShadowTrailingIdx])) + (($inClose[$ShadowTrailingIdx] >= $inOpen[$ShadowTrailingIdx] ? $inOpen[$ShadowTrailingIdx] : $inClose[$ShadowTrailingIdx]) - $inLow[$ShadowTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
            $ShadowTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlSpinningTop(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlSpinningTopLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i               = $BodyTrailingIdx;
        while ($i < $startIdx) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) > (abs($inClose[$i] - $inOpen[$i])) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (abs($inClose[$i] - $inOpen[$i]))
            ) {
                $outInteger[$outIdx++] = ($inClose[$i] >= $inOpen[$i] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlStalledPattern(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        $BodyLongPeriodTotal = $this->double(3);
        $NearPeriodTotal     = $this->double(3);
        //double $BodyShortPeriodTotal, $ShadowVeryShortPeriodTotal;
        //int $i, $outIdx, $totIdx, $BodyLongTrailingIdx, $BodyShortTrailingIdx, $ShadowVeryShortTrailingIdx, $NearTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlStalledPatternLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal[2]     = 0;
        $BodyLongPeriodTotal[1]     = 0;
        $BodyLongPeriodTotal[0]     = 0;
        $BodyLongTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortPeriodTotal       = 0;
        $BodyShortTrailingIdx       = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $NearPeriodTotal[2]         = 0;
        $NearPeriodTotal[1]         = 0;
        $NearPeriodTotal[0]         = 0;
        $NearTrailingIdx            = $startIdx - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $i                          = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal[2] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $BodyLongPeriodTotal[1] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal[2] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $NearPeriodTotal[1] += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inClose[$i] > $inClose[$i - 1] && $inClose[$i - 1] > $inClose[$i - 2] &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[2] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal[1] / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i - 1] > $inOpen[$i - 2] &&
                $inOpen[$i - 1] <= $inClose[$i - 2] + (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[2] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inOpen[$i] >= $inClose[$i - 1] - (abs($inClose[$i] - $inOpen[$i])) - (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal[1] / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            for ($totIdx = 2; $totIdx >= 1; --$totIdx) {
                $BodyLongPeriodTotal[$totIdx] += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - $totIdx] - $inOpen[$BodyLongTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - $inLow[$BodyLongTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - $totIdx] - ($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inClose[$BodyLongTrailingIdx - $totIdx] : $inOpen[$BodyLongTrailingIdx - $totIdx])) + (($inClose[$BodyLongTrailingIdx - $totIdx] >= $inOpen[$BodyLongTrailingIdx - $totIdx] ? $inOpen[$BodyLongTrailingIdx - $totIdx] : $inClose[$BodyLongTrailingIdx - $totIdx]) - $inLow[$BodyLongTrailingIdx - $totIdx]) : 0)));
                $NearPeriodTotal[$totIdx]     += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - $totIdx] - $inOpen[$i - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - $totIdx] - $inLow[$i - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - $totIdx] - ($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inClose[$i - $totIdx] : $inOpen[$i - $totIdx])) + (($inClose[$i - $totIdx] >= $inOpen[$i - $totIdx] ? $inOpen[$i - $totIdx] : $inClose[$i - $totIdx]) - $inLow[$i - $totIdx]) : 0)))
                                                 - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - $totIdx] - $inOpen[$NearTrailingIdx - $totIdx])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - $totIdx] - $inLow[$NearTrailingIdx - $totIdx]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - $totIdx] - ($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inClose[$NearTrailingIdx - $totIdx] : $inOpen[$NearTrailingIdx - $totIdx])) + (($inClose[$NearTrailingIdx - $totIdx] >= $inOpen[$NearTrailingIdx - $totIdx] ? $inOpen[$NearTrailingIdx - $totIdx] : $inClose[$NearTrailingIdx - $totIdx]) - $inLow[$NearTrailingIdx - $totIdx]) : 0)));
            }
            $BodyShortPeriodTotal       += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx - 1] - $inOpen[$ShadowVeryShortTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - $inLow[$ShadowVeryShortTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx - 1] - ($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inClose[$ShadowVeryShortTrailingIdx - 1] : $inOpen[$ShadowVeryShortTrailingIdx - 1])) + (($inClose[$ShadowVeryShortTrailingIdx - 1] >= $inOpen[$ShadowVeryShortTrailingIdx - 1] ? $inOpen[$ShadowVeryShortTrailingIdx - 1] : $inClose[$ShadowVeryShortTrailingIdx - 1]) - $inLow[$ShadowVeryShortTrailingIdx - 1]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlStickSandwich(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $EqualPeriodTotal;
        //int $i, $outIdx, $EqualTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlStickSandwichLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $EqualPeriodTotal = 0;
        $EqualTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $i                = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inLow[$i - 1] > $inClose[$i - 2] &&
                $inClose[$i] <= $inClose[$i - 2] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] >= $inClose[$i - 2] - (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 2] - $inOpen[$EqualTrailingIdx - 2])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 2] - $inLow[$EqualTrailingIdx - 2]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 2] - ($inClose[$EqualTrailingIdx - 2] >= $inOpen[$EqualTrailingIdx - 2] ? $inClose[$EqualTrailingIdx - 2] : $inOpen[$EqualTrailingIdx - 2])) + (($inClose[$EqualTrailingIdx - 2] >= $inOpen[$EqualTrailingIdx - 2] ? $inOpen[$EqualTrailingIdx - 2] : $inClose[$EqualTrailingIdx - 2]) - $inLow[$EqualTrailingIdx - 2]) : 0)));
            $i++;
            $EqualTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlTakuri(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyDojiPeriodTotal, $ShadowVeryShortPeriodTotal, $ShadowVeryLongPeriodTotal;
        //int $i, $outIdx, $BodyDojiTrailingIdx, $ShadowVeryShortTrailingIdx, $ShadowVeryLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlTakuriLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyDojiPeriodTotal        = 0;
        $BodyDojiTrailingIdx        = $startIdx - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $ShadowVeryShortPeriodTotal = 0;
        $ShadowVeryShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod);
        $ShadowVeryLongPeriodTotal  = 0;
        $ShadowVeryLongTrailingIdx  = $startIdx - ($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod);
        $i                          = $BodyDojiTrailingIdx;
        while ($i < $startIdx) {
            $BodyDojiPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryShortTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $ShadowVeryLongTrailingIdx;
        while ($i < $startIdx) {
            $ShadowVeryLongPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $outIdx = 0;
        do {
            if ((abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyDojiPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) < (($this->candleSettings[CandleSettingType::ShadowVeryShort]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) != 0.0 ? $ShadowVeryShortPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) > (($this->candleSettings[CandleSettingType::ShadowVeryLong]->factor) * (($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod) != 0.0 ? $ShadowVeryLongPeriodTotal / ($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyDojiPeriodTotal        += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyDojiTrailingIdx] - $inOpen[$BodyDojiTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyDojiTrailingIdx] - $inLow[$BodyDojiTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyDojiTrailingIdx] - ($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inClose[$BodyDojiTrailingIdx] : $inOpen[$BodyDojiTrailingIdx])) + (($inClose[$BodyDojiTrailingIdx] >= $inOpen[$BodyDojiTrailingIdx] ? $inOpen[$BodyDojiTrailingIdx] : $inClose[$BodyDojiTrailingIdx]) - $inLow[$BodyDojiTrailingIdx]) : 0)));
            $ShadowVeryShortPeriodTotal += (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryShortTrailingIdx] - $inOpen[$ShadowVeryShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryShortTrailingIdx] - $inLow[$ShadowVeryShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryShortTrailingIdx] - ($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inClose[$ShadowVeryShortTrailingIdx] : $inOpen[$ShadowVeryShortTrailingIdx])) + (($inClose[$ShadowVeryShortTrailingIdx] >= $inOpen[$ShadowVeryShortTrailingIdx] ? $inOpen[$ShadowVeryShortTrailingIdx] : $inClose[$ShadowVeryShortTrailingIdx]) - $inLow[$ShadowVeryShortTrailingIdx]) : 0)));
            $ShadowVeryLongPeriodTotal  += (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))
                                           - (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$ShadowVeryLongTrailingIdx] - $inOpen[$ShadowVeryLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::HighLow ? ($inHigh[$ShadowVeryLongTrailingIdx] - $inLow[$ShadowVeryLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->rangeType) == RangeType::Shadows ? ($inHigh[$ShadowVeryLongTrailingIdx] - ($inClose[$ShadowVeryLongTrailingIdx] >= $inOpen[$ShadowVeryLongTrailingIdx] ? $inClose[$ShadowVeryLongTrailingIdx] : $inOpen[$ShadowVeryLongTrailingIdx])) + (($inClose[$ShadowVeryLongTrailingIdx] >= $inOpen[$ShadowVeryLongTrailingIdx] ? $inOpen[$ShadowVeryLongTrailingIdx] : $inClose[$ShadowVeryLongTrailingIdx]) - $inLow[$ShadowVeryLongTrailingIdx]) : 0)));
            $i++;
            $BodyDojiTrailingIdx++;
            $ShadowVeryShortTrailingIdx++;
            $ShadowVeryLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlTasukiGap(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $NearPeriodTotal;
        //int $i, $outIdx, $NearTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlTasukiGapLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $NearPeriodTotal = 0;
        $NearTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::Near]->avgPeriod);
        $i               = $NearTrailingIdx;
        while ($i < $startIdx) {
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (
                (
                    (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                    ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == 1 &&
                    ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                    $inOpen[$i] < $inClose[$i - 1] && $inOpen[$i] > $inOpen[$i - 1] &&
                    $inClose[$i] < $inOpen[$i - 1] &&
                    $inClose[$i] > ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                    abs((abs($inClose[$i - 1] - $inOpen[$i - 1])) - (abs($inClose[$i] - $inOpen[$i]))) < (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                ) ||
                (
                    (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                    ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                    ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                    $inOpen[$i] < $inOpen[$i - 1] && $inOpen[$i] > $inClose[$i - 1] &&
                    $inClose[$i] > $inOpen[$i - 1] &&
                    $inClose[$i] < ((($inClose[$i - 2]) < ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                    abs((abs($inClose[$i - 1] - $inOpen[$i - 1])) - (abs($inClose[$i] - $inOpen[$i]))) < (($this->candleSettings[CandleSettingType::Near]->factor) * (($this->candleSettings[CandleSettingType::Near]->avgPeriod) != 0.0 ? $NearPeriodTotal / ($this->candleSettings[CandleSettingType::Near]->avgPeriod) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $NearPeriodTotal += (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::RealBody ? (abs($inClose[$NearTrailingIdx - 1] - $inOpen[$NearTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::HighLow ? ($inHigh[$NearTrailingIdx - 1] - $inLow[$NearTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Near]->rangeType) == RangeType::Shadows ? ($inHigh[$NearTrailingIdx - 1] - ($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inClose[$NearTrailingIdx - 1] : $inOpen[$NearTrailingIdx - 1])) + (($inClose[$NearTrailingIdx - 1] >= $inOpen[$NearTrailingIdx - 1] ? $inOpen[$NearTrailingIdx - 1] : $inClose[$NearTrailingIdx - 1]) - $inLow[$NearTrailingIdx - 1]) : 0)));
            $i++;
            $NearTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlThrusting(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $EqualPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $EqualTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlThrustingLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $EqualPeriodTotal    = 0;
        $EqualTrailingIdx    = $startIdx - ($this->candleSettings[CandleSettingType::Equal]->avgPeriod);
        $BodyLongPeriodTotal = 0;
        $BodyLongTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $i                   = $EqualTrailingIdx;
        while ($i < $startIdx) {
            $EqualPeriodTotal += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i = $BodyLongTrailingIdx;
        while ($i < $startIdx) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] < $inLow[$i - 1] &&
                $inClose[$i] > $inClose[$i - 1] + (($this->candleSettings[CandleSettingType::Equal]->factor) * (($this->candleSettings[CandleSettingType::Equal]->avgPeriod) != 0.0 ? $EqualPeriodTotal / ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                $inClose[$i] <= $inClose[$i - 1] + (abs($inClose[$i - 1] - $inOpen[$i - 1])) * 0.5
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $EqualPeriodTotal    += (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::RealBody ? (abs($inClose[$EqualTrailingIdx - 1] - $inOpen[$EqualTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::HighLow ? ($inHigh[$EqualTrailingIdx - 1] - $inLow[$EqualTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::Equal]->rangeType) == RangeType::Shadows ? ($inHigh[$EqualTrailingIdx - 1] - ($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inClose[$EqualTrailingIdx - 1] : $inOpen[$EqualTrailingIdx - 1])) + (($inClose[$EqualTrailingIdx - 1] >= $inOpen[$EqualTrailingIdx - 1] ? $inOpen[$EqualTrailingIdx - 1] : $inClose[$EqualTrailingIdx - 1]) - $inLow[$EqualTrailingIdx - 1]) : 0)));
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))
                                    - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx - 1] - $inOpen[$BodyLongTrailingIdx - 1])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx - 1] - $inLow[$BodyLongTrailingIdx - 1]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx - 1] - ($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inClose[$BodyLongTrailingIdx - 1] : $inOpen[$BodyLongTrailingIdx - 1])) + (($inClose[$BodyLongTrailingIdx - 1] >= $inOpen[$BodyLongTrailingIdx - 1] ? $inOpen[$BodyLongTrailingIdx - 1] : $inClose[$BodyLongTrailingIdx - 1]) - $inLow[$BodyLongTrailingIdx - 1]) : 0)));
            $i++;
            $EqualTrailingIdx++;
            $BodyLongTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlTristar(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyPeriodTotal;
        //int $i, $outIdx, $BodyTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlTristarLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyPeriodTotal = 0;
        $BodyTrailingIdx = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
        $i               = $BodyTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (abs($inClose[$i] - $inOpen[$i])) <= (($this->candleSettings[CandleSettingType::BodyDoji]->factor) * (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) != 0.0 ? $BodyPeriodTotal / ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? 2.0 : 1.0))) {
                $outInteger[$outIdx] = 0;
                if ((((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                    &&
                    ((($inOpen[$i]) > ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) < ((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))
                ) {
                    $outInteger[$outIdx] = -100;
                }
                if ((((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                    &&
                    ((($inOpen[$i]) < ($inClose[$i])) ? ($inOpen[$i]) : ($inClose[$i])) > ((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1]))
                ) {
                    $outInteger[$outIdx] = +100;
                }
                $outIdx++;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyPeriodTotal += (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyTrailingIdx] - $inOpen[$BodyTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyTrailingIdx] - $inLow[$BodyTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyDoji]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyTrailingIdx] - ($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inClose[$BodyTrailingIdx] : $inOpen[$BodyTrailingIdx])) + (($inClose[$BodyTrailingIdx] >= $inOpen[$BodyTrailingIdx] ? $inOpen[$BodyTrailingIdx] : $inClose[$BodyTrailingIdx]) - $inLow[$BodyTrailingIdx]) : 0)));
            $i++;
            $BodyTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlUnique3River(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyShortPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlUnique3RiverLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if ((abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                $inClose[$i - 1] > $inClose[$i - 2] && $inOpen[$i - 1] <= $inOpen[$i - 2] &&
                $inLow[$i - 1] < $inLow[$i - 2] &&
                (abs($inClose[$i] - $inOpen[$i])) < (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == 1 &&
                $inOpen[$i] > $inLow[$i - 1]
            ) {
                $outInteger[$outIdx++] = 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlUpsideGap2Crows(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //double $BodyShortPeriodTotal, $BodyLongPeriodTotal;
        //int $i, $outIdx, $BodyShortTrailingIdx, $BodyLongTrailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlUpsideGap2CrowsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $BodyLongPeriodTotal  = 0;
        $BodyShortPeriodTotal = 0;
        $BodyLongTrailingIdx  = $startIdx - 2 - ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod);
        $BodyShortTrailingIdx = $startIdx - 1 - ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
        $i                    = $BodyLongTrailingIdx;
        while ($i < $startIdx - 2) {
            $BodyLongPeriodTotal += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i = $BodyShortTrailingIdx;
        while ($i < $startIdx - 1) {
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i] - $inOpen[$i])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i] - $inLow[$i]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i] - ($inClose[$i] >= $inOpen[$i] ? $inClose[$i] : $inOpen[$i])) + (($inClose[$i] >= $inOpen[$i] ? $inOpen[$i] : $inClose[$i]) - $inLow[$i]) : 0)));
            $i++;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                (abs($inClose[$i - 2] - $inOpen[$i - 2])) > (($this->candleSettings[CandleSettingType::BodyLong]->factor) * (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) != 0.0 ? $BodyLongPeriodTotal / ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -1 &&
                (abs($inClose[$i - 1] - $inOpen[$i - 1])) <= (($this->candleSettings[CandleSettingType::BodyShort]->factor) * (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) != 0.0 ? $BodyShortPeriodTotal / ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0)))) / (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? 2.0 : 1.0)) &&
                (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2]))) &&
                ($inClose[$i] >= $inOpen[$i] ? 1 : -1) == -1 &&
                $inOpen[$i] > $inOpen[$i - 1] && $inClose[$i] < $inClose[$i - 1] &&
                $inClose[$i] > $inClose[$i - 2]
            ) {
                $outInteger[$outIdx++] = -100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $BodyLongPeriodTotal  += (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 2] - $inOpen[$i - 2])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 2] - $inLow[$i - 2]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 2] - ($inClose[$i - 2] >= $inOpen[$i - 2] ? $inClose[$i - 2] : $inOpen[$i - 2])) + (($inClose[$i - 2] >= $inOpen[$i - 2] ? $inOpen[$i - 2] : $inClose[$i - 2]) - $inLow[$i - 2]) : 0))) - (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyLongTrailingIdx] - $inOpen[$BodyLongTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyLongTrailingIdx] - $inLow[$BodyLongTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyLong]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyLongTrailingIdx] - ($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inClose[$BodyLongTrailingIdx] : $inOpen[$BodyLongTrailingIdx])) + (($inClose[$BodyLongTrailingIdx] >= $inOpen[$BodyLongTrailingIdx] ? $inOpen[$BodyLongTrailingIdx] : $inClose[$BodyLongTrailingIdx]) - $inLow[$BodyLongTrailingIdx]) : 0)));
            $BodyShortPeriodTotal += (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$i - 1] - $inOpen[$i - 1])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$i - 1] - $inLow[$i - 1]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$i - 1] - ($inClose[$i - 1] >= $inOpen[$i - 1] ? $inClose[$i - 1] : $inOpen[$i - 1])) + (($inClose[$i - 1] >= $inOpen[$i - 1] ? $inOpen[$i - 1] : $inClose[$i - 1]) - $inLow[$i - 1]) : 0))) - (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::RealBody ? (abs($inClose[$BodyShortTrailingIdx] - $inOpen[$BodyShortTrailingIdx])) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::HighLow ? ($inHigh[$BodyShortTrailingIdx] - $inLow[$BodyShortTrailingIdx]) : (($this->candleSettings[CandleSettingType::BodyShort]->rangeType) == RangeType::Shadows ? ($inHigh[$BodyShortTrailingIdx] - ($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inClose[$BodyShortTrailingIdx] : $inOpen[$BodyShortTrailingIdx])) + (($inClose[$BodyShortTrailingIdx] >= $inOpen[$BodyShortTrailingIdx] ? $inOpen[$BodyShortTrailingIdx] : $inClose[$BodyShortTrailingIdx]) - $inLow[$BodyShortTrailingIdx]) : 0)));
            $i++;
            $BodyLongTrailingIdx++;
            $BodyShortTrailingIdx++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public function cdlXSideGap3Methods(
        int $startIdx,
        int $endIdx,
        array $inOpen,
        array $inHigh,
        array $inLow,
        array $inClose,
        MInteger &$outBegIdx,
        MInteger &$outNBElement,
        array &$outInteger
    )
    {
        //int $i, $outIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $lookbackTotal = $this->cdlXSideGap3MethodsLookback();
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $i      = $startIdx;
        $outIdx = 0;
        do {
            if (($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) &&
                ($inClose[$i - 1] >= $inOpen[$i - 1] ? 1 : -1) == -($inClose[$i] >= $inOpen[$i] ? 1 : -1) &&
                $inOpen[$i] < ((($inClose[$i - 1]) > ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                $inOpen[$i] > ((($inClose[$i - 1]) < ($inOpen[$i - 1])) ? ($inClose[$i - 1]) : ($inOpen[$i - 1])) &&
                $inClose[$i] < ((($inClose[$i - 2]) > ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                $inClose[$i] > ((($inClose[$i - 2]) < ($inOpen[$i - 2])) ? ($inClose[$i - 2]) : ($inOpen[$i - 2])) &&
                ((
                     ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == 1 &&
                     (((($inOpen[$i - 1]) < ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) > ((($inOpen[$i - 2]) > ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                 ) ||
                 (
                     ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) == -1 &&
                     (((($inOpen[$i - 1]) > ($inClose[$i - 1])) ? ($inOpen[$i - 1]) : ($inClose[$i - 1])) < ((($inOpen[$i - 2]) < ($inClose[$i - 2])) ? ($inOpen[$i - 2]) : ($inClose[$i - 2])))
                 )
                )
            ) {
                $outInteger[$outIdx++] = ($inClose[$i - 2] >= $inOpen[$i - 2] ? 1 : -1) * 100;
            } else {
                $outInteger[$outIdx++] = 0;
            }
            $i++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }
}
