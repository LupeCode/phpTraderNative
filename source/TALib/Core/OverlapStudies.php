<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class OverlapStudies
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param int      $optInTimePeriod
     * @param float    $optInNbDevUp
     * @param float    $optInNbDevDn
     * @param int      $optInMAType
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outRealUpperBand
     * @param float[]  $outRealMiddleBand
     * @param float[]  $outRealLowerBand
     *
     * @return int
     */
    public function bbands(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, float $optInNbDevUp, float $optInNbDevDn, int $optInMAType, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outRealUpperBand, array &$outRealMiddleBand, array &$outRealLowerBand): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return RetCode::BadParam;
        }
        if ($optInNbDevUp == (-4e+37)) {
            $optInNbDevUp = 2.000000e+0;
        } elseif (($optInNbDevUp < -3.000000e+37) || ($optInNbDevUp > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        if ($optInNbDevDn == (-4e+37)) {
            $optInNbDevDn = 2.000000e+0;
        } elseif (($optInNbDevDn < -3.000000e+37) || ($optInNbDevDn > 3.000000e+37)) {
            return RetCode::BadParam;
        }
        if ($inReal == $outRealUpperBand) {
            $tempBuffer1 = $outRealMiddleBand;
            $tempBuffer2 = $outRealLowerBand;
        } elseif ($inReal == $outRealLowerBand) {
            $tempBuffer1 = $outRealMiddleBand;
            $tempBuffer2 = $outRealUpperBand;
        } elseif ($inReal == $outRealMiddleBand) {
            $tempBuffer1 = $outRealLowerBand;
            $tempBuffer2 = $outRealUpperBand;
        } else {
            $tempBuffer1 = $outRealMiddleBand;
            $tempBuffer2 = $outRealUpperBand;
        }
        if (($tempBuffer1 == $inReal) || ($tempBuffer2 == $inReal)) {
            return RetCode::BadParam;
        }
        $retCode = $this->movingAverage($startIdx, $endIdx, $inReal, $optInTimePeriod, $optInMAType, $outBegIdx, $outNBElement, $tempBuffer1);
        if (($retCode != RetCode::Success) || ((int)$outNBElement->value == 0)) {
            $outNBElement->value = 0;

            return $retCode;
        }
        if ($optInMAType == MAType::SMA) {
            $this->TA_INT_stddev_using_precalc_ma($inReal, $tempBuffer1, (int)$outBegIdx->value, (int)$outNBElement->value, $optInTimePeriod, $tempBuffer2);
        } else {
            $retCode = $this->stdDev((int)$outBegIdx->value, $endIdx, $inReal, $optInTimePeriod, 1.0, $outBegIdx, $outNBElement, $tempBuffer2);
            if ($retCode != RetCode::Success) {
                $outNBElement->value = 0;

                return $retCode;
            }
        }
        if ($tempBuffer1 != $outRealMiddleBand) {
            $outRealMiddleBand = \array_slice($tempBuffer1, 0, $outNBElement->value);
        }
        if ($optInNbDevUp == $optInNbDevDn) {
            if ($optInNbDevUp == 1.0) {
                for ($i = 0; $i < (int)$outNBElement->value; $i++) {
                    $tempReal             = $tempBuffer2[$i];
                    $tempReal2            = $outRealMiddleBand[$i];
                    $outRealUpperBand[$i] = $tempReal2 + $tempReal;
                    $outRealLowerBand[$i] = $tempReal2 - $tempReal;
                }
            } else {
                for ($i = 0; $i < (int)$outNBElement->value; $i++) {
                    $tempReal             = $tempBuffer2[$i] * $optInNbDevUp;
                    $tempReal2            = $outRealMiddleBand[$i];
                    $outRealUpperBand[$i] = $tempReal2 + $tempReal;
                    $outRealLowerBand[$i] = $tempReal2 - $tempReal;
                }
            }
        } elseif ($optInNbDevUp == 1.0) {
            for ($i = 0; $i < (int)$outNBElement->value; $i++) {
                $tempReal             = $tempBuffer2[$i];
                $tempReal2            = $outRealMiddleBand[$i];
                $outRealUpperBand[$i] = $tempReal2 + $tempReal;
                $outRealLowerBand[$i] = $tempReal2 - ($tempReal * $optInNbDevDn);
            }
        } elseif ($optInNbDevDn == 1.0) {
            for ($i = 0; $i < (int)$outNBElement->value; $i++) {
                $tempReal             = $tempBuffer2[$i];
                $tempReal2            = $outRealMiddleBand[$i];
                $outRealLowerBand[$i] = $tempReal2 - $tempReal;
                $outRealUpperBand[$i] = $tempReal2 + ($tempReal * $optInNbDevUp);
            }
        } else {
            for ($i = 0; $i < (int)$outNBElement->value; $i++) {
                $tempReal             = $tempBuffer2[$i];
                $tempReal2            = $outRealMiddleBand[$i];
                $outRealUpperBand[$i] = $tempReal2 + ($tempReal * $optInNbDevUp);
                $outRealLowerBand[$i] = $tempReal2 - ($tempReal * $optInNbDevDn);
            }
        }

        return RetCode::Success;
    }

}
