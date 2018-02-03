<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\ConvertedJava\FuncUnstId;
use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use LupeCode\phpTraderNative\ConvertedJava\RetCode;

class MomentumIndicators
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param int      $optInTimePeriod
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function adx(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = (2 * $optInTimePeriod) + ($this->unstablePeriod[FuncUnstId::ADX]) - 1;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $outBegIdx->value = $today = $startIdx;
        $prevMinusDM      = 0.0;
        $prevPlusDM       = 0.0;
        $prevTR           = 0.0;
        $today            = $startIdx - $lookbackTotal;
        $prevHigh         = $inHigh[$today];
        $prevLow          = $inLow[$today];
        $prevClose        = $inClose[$today];
        $i                = $optInTimePeriod - 1;
        while ($i-- > 0) {
            $today++;
            $tempReal = $inHigh[$today];
            $diffP    = $tempReal - $prevHigh;
            $prevHigh = $tempReal;
            $tempReal = $inLow[$today];
            $diffM    = $prevLow - $tempReal;
            $prevLow  = $tempReal;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    += $tempReal;
            $prevClose = $inClose[$today];
        }
        $sumDX = 0.0;
        $i     = $optInTimePeriod;
        while ($i-- > 0) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
                $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
                $tempReal = $minusDI + $plusDI;
                if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                    $sumDX += (100.0 * (abs($minusDI - $plusDI) / $tempReal));
                }
            }
        }
        $prevADX = ($sumDX / $optInTimePeriod);
        $i       = ($this->unstablePeriod[FuncUnstId::ADX]);
        while ($i-- > 0) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
                $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
                $tempReal = $minusDI + $plusDI;
                if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                    $tempReal = (100.0 * (abs($minusDI - $plusDI) / $tempReal));
                    $prevADX  = ((($prevADX * ($optInTimePeriod - 1)) + $tempReal) / $optInTimePeriod);
                }
            }
        }
        $outReal[0] = $prevADX;
        $outIdx     = 1;
        while ($today < $endIdx) {
            $today++;
            $tempReal    = $inHigh[$today];
            $diffP       = $tempReal - $prevHigh;
            $prevHigh    = $tempReal;
            $tempReal    = $inLow[$today];
            $diffM       = $prevLow - $tempReal;
            $prevLow     = $tempReal;
            $prevMinusDM -= $prevMinusDM / $optInTimePeriod;
            $prevPlusDM  -= $prevPlusDM / $optInTimePeriod;
            if (($diffM > 0) && ($diffP < $diffM)) {
                $prevMinusDM += $diffM;
            } elseif (($diffP > 0) && ($diffP > $diffM)) {
                $prevPlusDM += $diffP;
            }
            {
                $tempReal  = $prevHigh - $prevLow;
                $tempReal2 = abs($prevHigh - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
                $tempReal2 = abs($prevLow - $prevClose);
                if ($tempReal2 > $tempReal) {
                    $tempReal = $tempReal2;
                }
            };
            $prevTR    = $prevTR - ($prevTR / $optInTimePeriod) + $tempReal;
            $prevClose = $inClose[$today];
            if (!(((-0.00000001) < $prevTR) && ($prevTR < 0.00000001))) {
                $minusDI  = (100.0 * ($prevMinusDM / $prevTR));
                $plusDI   = (100.0 * ($prevPlusDM / $prevTR));
                $tempReal = $minusDI + $plusDI;
                if (!(((-0.00000001) < $tempReal) && ($tempReal < 0.00000001))) {
                    $tempReal = (100.0 * (abs($minusDI - $plusDI) / $tempReal));
                    $prevADX  = ((($prevADX * ($optInTimePeriod - 1)) + $tempReal) / $optInTimePeriod);
                }
            }
            $outReal[$outIdx++] = $prevADX;
        }
        $outNBElement->value = $outIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param int      $optInTimePeriod
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function adxr(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return RetCode::BadParam;
        }
        $adxrLookback = $this->adxrLookback($optInTimePeriod);
        if ($startIdx < $adxrLookback) {
            $startIdx = $adxrLookback;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $adx     = $this->double($endIdx - $startIdx + $optInTimePeriod);
        $retCode = $this->adx($startIdx - ($optInTimePeriod - 1), $endIdx, $inHigh, $inLow, $inClose, $optInTimePeriod, $outBegIdx, $outNBElement, $adx);
        if ($retCode != RetCode::Success) {
            return $retCode;
        }
        $i         = $optInTimePeriod - 1;
        $j         = 0;
        $outIdx    = 0;
        $nbElement = $endIdx - $startIdx + 2;
        while (--$nbElement != 0) {
            $outReal[$outIdx++] = (($adx[$i++] + $adx[$j++]) / 2.0);
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal
     * @param int      $optInFastPeriod
     * @param int      $optInSlowPeriod
     * @param int      $optInMAType
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function apo(int $startIdx, int $endIdx, array $inReal, int $optInFastPeriod, int $optInSlowPeriod, int $optInMAType, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return RetCode::BadParam;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return RetCode::BadParam;
        }
        $tempBuffer = $this->double($endIdx - $startIdx + 1);
        $retCode    = $this->TA_INT_PO($startIdx, $endIdx, $inReal, $optInFastPeriod, $optInSlowPeriod, $optInMAType, $outBegIdx, $outNBElement, $outReal, $tempBuffer, false);

        return $retCode;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param int      $optInTimePeriod
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outAroonDown
     * @param float[]  $outAroonUp
     *
     * @return int
     */
    public function aroon(int $startIdx, int $endIdx, array $inHigh, array $inLow, int $optInTimePeriod, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outAroonDown, array &$outAroonUp): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return RetCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        $lowestIdx   = -1;
        $highestIdx  = -1;
        $lowest      = 0.0;
        $highest     = 0.0;
        $factor      = (double)100.0 / (double)$optInTimePeriod;
        while ($today <= $endIdx) {
            $tmp = $inLow[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inLow[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inLow[$i];
                    if ($tmp <= $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $tmp = $inHigh[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inHigh[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inHigh[$i];
                    if ($tmp >= $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $outAroonUp[$outIdx]   = $factor * ($optInTimePeriod - ($today - $highestIdx));
            $outAroonDown[$outIdx] = $factor * ($optInTimePeriod - ($today - $lowestIdx));
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param int      $optInTimePeriod
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function aroonOsc(int $startIdx, int $endIdx, array $inHigh, array $inLow, int $optInTimePeriod, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return RetCode::BadParam;
        }
        if ($startIdx < $optInTimePeriod) {
            $startIdx = $optInTimePeriod;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $optInTimePeriod;
        $lowestIdx   = -1;
        $highestIdx  = -1;
        $lowest      = 0.0;
        $highest     = 0.0;
        $factor      = (double)100.0 / (double)$optInTimePeriod;
        while ($today <= $endIdx) {
            $tmp = $inLow[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inLow[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inLow[$i];
                    if ($tmp <= $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $tmp = $inHigh[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inHigh[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inHigh[$i];
                    if ($tmp >= $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $aroon            = $factor * ($highestIdx - $lowestIdx);
            $outReal[$outIdx] = $aroon;
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

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
     * @param float[]  $outReal
     *
     * @return int
     */
    public function bop(int $startIdx, int $endIdx, array $inOpen, array $inHigh, array $inLow, array $inClose, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        $outIdx = 0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $tempReal = $inHigh[$i] - $inLow[$i];
            if (($tempReal < 0.00000001)) {
                $outReal[$outIdx++] = 0.0;
            } else {
                $outReal[$outIdx++] = ($inClose[$i] - $inOpen[$i]) / $tempReal;
            }
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inHigh
     * @param float[]  $inLow
     * @param float[]  $inClose
     * @param int      $optInTimePeriod
     * @param MInteger $outBegIdx
     * @param MInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function cci(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, int $optInTimePeriod, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        $circBuffer_Idx = 0;
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return RetCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return RetCode::BadParam;
        }
        $lookbackTotal = ($optInTimePeriod - 1);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        {
            if ($optInTimePeriod <= 0) {
                return RetCode::AllocError;
            }
            $circBuffer        = $this->double($optInTimePeriod);
            $maxIdx_circBuffer = ($optInTimePeriod - 1);
        };
        $i = $startIdx - $lookbackTotal;
        if ($optInTimePeriod > 1) {
            while ($i < $startIdx) {
                $circBuffer[$circBuffer_Idx] = ($inHigh[$i] + $inLow[$i] + $inClose[$i]) / 3;
                $i++;
                {
                    $circBuffer_Idx++;
                    if ($circBuffer_Idx > $maxIdx_circBuffer) {
                        $circBuffer_Idx = 0;
                    }
                };
            }
        }
        $outIdx = 0;
        do {
            $lastValue                   = ($inHigh[$i] + $inLow[$i] + $inClose[$i]) / 3;
            $circBuffer[$circBuffer_Idx] = $lastValue;
            $theAverage                  = 0;
            for ($j = 0; $j < $optInTimePeriod; $j++) {
                $theAverage += $circBuffer[$j];
            }
            $theAverage /= $optInTimePeriod;
            $tempReal2  = 0;
            for ($j = 0; $j < $optInTimePeriod; $j++) {
                $tempReal2 += abs($circBuffer[$j] - $theAverage);
            }
            $tempReal = $lastValue - $theAverage;
            if (($tempReal != 0.0) && ($tempReal2 != 0.0)) {
                $outReal[$outIdx++] = $tempReal / (0.015 * ($tempReal2 / $optInTimePeriod));
            } else {
                $outReal[$outIdx++] = 0.0;
            }
            {
                $circBuffer_Idx++;
                if ($circBuffer_Idx > $maxIdx_circBuffer) {
                    $circBuffer_Idx = 0;
                }
            };
            $i++;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

}
