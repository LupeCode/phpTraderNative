<?php

namespace LupeCode\phpTraderNative;

/**
 * Trait TraderOriginal
 *
 * This trait contains the transformation from the Java version of TA-LIB.
 * For comparison, the Java version is included in parts of this code.
 *
 * @package LupeCode\phpTraderNative
 */
trait TraderOriginal
{
    use TraderCommon;

    protected $unstablePeriod;
    protected $compatibility;
    /** @var \LupeCode\phpTraderNative\CandleSetting[] */
    protected $candleSettings;
    /** @var \LupeCode\phpTraderNative\CandleSetting[] */
    protected $TA_CandleDefaultSettings;

    /**
     * TraderOriginal constructor.
     */
    public function __construct()
    {
        $this->unstablePeriod           = array_pad([], FuncUnstId::ALL, 0);
        $this->compatibility            = Compatibility::Default;
        $this->TA_CandleDefaultSettings = [
            new CandleSetting(CandleSettingType::BodyLong, RangeType::RealBody, 10, 1.0),
            new CandleSetting(CandleSettingType::BodyVeryLong, RangeType::RealBody, 10, 3.0),
            new CandleSetting(CandleSettingType::BodyShort, RangeType::RealBody, 10, 1.0),
            new CandleSetting(CandleSettingType::BodyDoji, RangeType::HighLow, 10, 0.1),
            new CandleSetting(CandleSettingType::ShadowLong, RangeType::RealBody, 0, 1.0),
            new CandleSetting(CandleSettingType::ShadowVeryLong, RangeType::RealBody, 0, 2.0),
            new CandleSetting(CandleSettingType::ShadowShort, RangeType::Shadows, 10, 1.0),
            new CandleSetting(CandleSettingType::ShadowVeryShort, RangeType::HighLow, 10, 0.1),
            new CandleSetting(CandleSettingType::Near, RangeType::HighLow, 5, 0.2),
            new CandleSetting(CandleSettingType::Far, RangeType::HighLow, 5, 0.6),
            new CandleSetting(CandleSettingType::Equal, RangeType::HighLow, 5, 0.05),
        ];
        for ($i = 0; $i < CandleSettingType::AllCandleSettings; $i++) {
            $this->candleSettings[$i] = $this->TA_CandleDefaultSettings[$i];
        }
    }

    public function setCandleSettings(int $settingType, int $rangeType, int $avgPeriod, float $factor): int
    {
        if ($settingType >= CandleSettingType::AllCandleSettings) {
            return RetCode::BadParam;
        }
        $this->candleSettings[$settingType]->settingType = $settingType;
        $this->candleSettings[$settingType]->rangeType   = $rangeType;
        $this->candleSettings[$settingType]->avgPeriod   = $avgPeriod;
        $this->candleSettings[$settingType]->factor      = $factor;

        return RetCode::Success;
    }

    public function restoreCandleDefaultSettings(int $settingType): int
    {
        if ($settingType > CandleSettingType::AllCandleSettings) {
            return RetCode::BadParam;
        }
        if ($settingType === CandleSettingType::AllCandleSettings) {
            for ($i = 0; $i < CandleSettingType::AllCandleSettings; $i++) {
                $this->candleSettings[$i]->copyFrom($this->TA_CandleDefaultSettings[$i]);
            }
        } else {
            $this->candleSettings[$settingType]->copyFrom($this->TA_CandleDefaultSettings[$settingType]);
        }

        return RetCode::Success;
    }

    public function getUnstablePeriod(int $id): int
    {
        return $this->unstablePeriod[$id];
    }

    public function setUnstablePeriod(int $id, int $period): int
    {
        if ($id > FuncUnstId::ALL) {
            return RetCode::BadParam;
        }
        $this->unstablePeriod[$id] = $period;

        return RetCode::Success;
    }

    public function getCompatibility(): int
    {
        return $this->compatibility;
    }

    public function setCompatibility(int $compatibility)
    {
        $this->compatibility = $compatibility;
    }

    protected static function checkStartEnd(int $startIdx, int $endIdx): int
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if ($endIdx < 0 || $endIdx < $startIdx) {
            return RetCode::OutOfRangeEndIndex;
        }

        return 0;
    }

    protected function acosLookback(): int
    {
        return 0;
    }

    public function trader_acos(int $startIdx, int $endIdx, array $inReal, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        //int outIdx, i;
        //if (startIdx < 0)
        //    return RetCode.OutOfRangeStartIndex;
        //if ((endIdx < 0) || (endIdx < startIdx))
        //    return RetCode.OutOfRangeEndIndex;
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }
        //for (i = startIdx, outIdx = 0; i <= endIdx; i++, outIdx++) {
        //    outReal[outIdx] = Math.acos(inReal[i]);
        //}
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = acos($inReal[$i]);
        }
        //outNBElement.value = outIdx;
        //outBegIdx.value = startIdx;
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        //return RetCode.Success;
        return RetCode::Success;
    }

    protected function adLookback(): int
    {
        return 0;
    }

    public function trader_ad(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        //int nbBar, currentBar, outIdx;
        //double high, low, close, tmp, ad;
        //if (startIdx < 0)
        //    return RetCode.OutOfRangeStartIndex;
        //if ((endIdx < 0) || (endIdx < startIdx))
        //    return RetCode.OutOfRangeEndIndex;
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }
        //nbBar = endIdx - startIdx + 1;
        //outNBElement.value = nbBar;
        //outBegIdx.value = startIdx;
        //currentBar = startIdx;
        //outIdx = 0;
        //ad = 0.0;
        $nbBar               = $endIdx - $startIdx + 1;
        $outNBElement->value = $nbBar;
        $outBegIdx->value    = $startIdx;
        $currentBar          = $startIdx;
        $outIdx              = 0;
        $ad                  = 0.;
        //while (nbBar != 0) {
        //    high = inHigh[currentBar];
        //    low = inLow[currentBar];
        //    tmp = high - low;
        //    close = inClose[currentBar];
        //    if (tmp > 0.0)
        //        ad += (((close - low) - (high - close)) / tmp) * ((double) inVolume[currentBar]);
        //    outReal[outIdx++] = ad;
        //    currentBar++;
        //    nbBar--;
        //}
        while ($nbBar !== 0) {
            $high  = $inHigh[$currentBar];
            $low   = $inLow[$currentBar];
            $tmp   = $high - $low;
            $close = $inClose[$currentBar];
            if ($tmp > 0.) {
                $ad += ((($close - $low) - ($high - $close)) / $tmp) * ((float)$inVolume[$currentBar]);
            }
            $outReal[$outIdx++] = $ad;
            $currentBar++;
            $nbBar--;
        }

        //return RetCode.Success;
        return RetCode::Success;
    }

    protected function addLookback()
    {
        return 0;
    }

    public function trader_add(int $startIdx, int $endIdx, array $inReal0, array $inReal1, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal)
    {
        //int outIdx, i;
        //if (startIdx < 0)
        //    return RetCode.OutOfRangeStartIndex;
        //if ((endIdx < 0) || (endIdx < startIdx))
        //    return RetCode.OutOfRangeEndIndex;
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }
        //for (i = startIdx, outIdx = 0; i <= endIdx; i++, outIdx++) {
        //    outReal[outIdx] = inReal0[i] + inReal1[i];
        //}
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] + $inReal1[$i];
        }
        //outNBElement.value = outIdx;
        //outBegIdx.value = startIdx;
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        //return RetCode.Success;
        return RetCode::Success;
    }

    protected function adOscLookback(int $optInFastPeriod, int $optInSlowPeriod)
    {
        //int slowestPeriod;
        //if ((int) optInFastPeriod == (Integer.MIN_VALUE))
        //    optInFastPeriod = 3;
        //else if (((int) optInFastPeriod < 2) || ((int) optInFastPeriod > 100000))
        //    return -1;
        if ($optInFastPeriod == PHP_INT_MIN) {
            $optInFastPeriod = 3;
        } elseif ($optInFastPeriod < 2 || $optInFastPeriod > 100000) {
            return -1;
        }
        //if ((int) optInSlowPeriod == (Integer.MIN_VALUE))
        //    optInSlowPeriod = 10;
        //else if (((int) optInSlowPeriod < 2) || ((int) optInSlowPeriod > 100000))
        //    return -1;
        if ($optInSlowPeriod == PHP_INT_MIN) {
            $optInSlowPeriod = 10;
        } elseif ($optInSlowPeriod < 2 || $optInSlowPeriod > 100000) {
            return -1;
        }
        //if (optInFastPeriod < optInSlowPeriod)
        //    slowestPeriod = optInSlowPeriod;
        //else
        //    slowestPeriod = optInFastPeriod;
        if ($optInFastPeriod < $optInSlowPeriod) {
            $slowestPeriod = $optInSlowPeriod;
        } else {
            $slowestPeriod = $optInFastPeriod;
        }

        //return emaLookback(slowestPeriod);
        return $this->emaLookback($slowestPeriod);
    }

    public function trader_adosc(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, int $optInFastPeriod = 3, int $optInSlowPeriod = 10, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        //int today, outIdx, lookbackTotal, slowestPeriod;
        //double high, low, close, tmp, slowEMA, slowk, one_minus_slowk, fastEMA, fastk, one_minus_fastk, ad;
        //if (startIdx < 0)
        //    return RetCode.OutOfRangeStartIndex;
        //if ((endIdx < 0) || (endIdx < startIdx))
        //    return RetCode.OutOfRangeEndIndex;
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }
        //if ((int) optInFastPeriod == (Integer.MIN_VALUE))
        //    optInFastPeriod = 3;
        //else if (((int) optInFastPeriod < 2) || ((int) optInFastPeriod > 100000))
        //    return RetCode.BadParam;
        if ((int)$optInFastPeriod == PHP_INT_MIN) {
            $optInFastPeriod = 3;
        } elseif ((int)$optInFastPeriod < 2 || (int)$optInFastPeriod > 1000) {
            return RetCode::BadParam;
        }
        //if ((int) optInSlowPeriod == (Integer.MIN_VALUE))
        //    optInSlowPeriod = 10;
        //else if (((int) optInSlowPeriod < 2) || ((int) optInSlowPeriod > 100000))
        //    return RetCode.BadParam;
        if ((int)$optInSlowPeriod == PHP_INT_MIN) {
            $optInSlowPeriod = 3;
        } elseif ((int)$optInSlowPeriod < 2 || (int)$optInSlowPeriod > 1000) {
            return RetCode::BadParam;
        }
        //if (optInFastPeriod < optInSlowPeriod)
        //    slowestPeriod = optInSlowPeriod;
        //else
        //    slowestPeriod = optInFastPeriod;
        if ($optInFastPeriod < $optInSlowPeriod) {
            $slowestPeriod = $optInSlowPeriod;
        } else {
            $slowestPeriod = $optInFastPeriod;
        }
        //lookbackTotal = emaLookback(slowestPeriod);
        $lookbackTotal = $this->emaLookback($slowestPeriod);
        //if (startIdx < lookbackTotal)
        //    startIdx = lookbackTotal;
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        //if (startIdx > endIdx) {
        //    outBegIdx.value = 0;
        //    outNBElement.value = 0;
        //    return RetCode.Success;
        //}
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }
        //outBegIdx.value = startIdx;
        //today = startIdx - lookbackTotal;
        //ad = 0.0;
        $outBegIdx->value = $startIdx;
        $today            = $startIdx - $lookbackTotal;
        $ad               = 0.;
        //fastk = ((double) 2.0 / ((double) (optInFastPeriod + 1)));
        //one_minus_fastk = 1.0 - fastk;
        //slowk = ((double) 2.0 / ((double) (optInSlowPeriod + 1)));
        //one_minus_slowk = 1.0 - slowk;
        $fastK         = 2.0 / $optInFastPeriod + 1;
        $oneMinusFastK = 1.0 - $fastK;
        $slowK         = 2.0 / $optInSlowPeriod + 1;
        $oneMinusSlowK = 1.0 - $slowK;
        //{
        //    high = inHigh[today];
        //    low = inLow[today];
        //    tmp = high - low;
        //    close = inClose[today];
        //    if (tmp > 0.0) ad += (((close - low) - (high - close)) / tmp) * ((double) inVolume[today]);
        //    today++;
        //}
        //;
        {
            $high  = $inHigh[$today];
            $low   = $inLow[$today];
            $tmp   = $high - $low;
            $close = $inClose[$today];
            if ($tmp > 0.0) {
                $ad += ((($close - $low) - ($high - $close)) / $tmp) * $inVolume[$today];
            }
            $today++;
        }
        $outReal = [];
        //fastEMA = ad;
        //slowEMA = ad;
        $fastEMA = $ad;
        $slowEMA = $ad;
        //while (today < startIdx) {
        //    {
        //        high = inHigh[today];
        //        low = inLow[today];
        //        tmp = high - low;
        //        close = inClose[today];
        //        if (tmp > 0.0) ad += (((close - low) - (high - close)) / tmp) * ((double) inVolume[today]);
        //        today++;
        //    }
        //    ;
        //    fastEMA = (fastk * ad) + (one_minus_fastk * fastEMA);
        //    slowEMA = (slowk * ad) + (one_minus_slowk * slowEMA);
        //}
        while ($today < $startIdx) {
            {
                $high  = $inHigh[$today];
                $low   = $inLow[$today];
                $tmp   = $high - $low;
                $close = $inClose[$today];
                if ($tmp > 0.0) {
                    $ad += ((($close - $low) - ($high - $close)) / $tmp) * $inVolume[$today];
                }
                $today++;
            }
            $fastEMA = ($fastK * $ad) + ($oneMinusFastK * $fastEMA);
            $slowEMA = ($slowK * $ad) + ($oneMinusSlowK * $slowEMA);
        }
        //outIdx = 0;
        $outIdx = 0;
        //while (today <= endIdx) {
        //    {
        //        high = inHigh[today];
        //        low = inLow[today];
        //        tmp = high - low;
        //        close = inClose[today];
        //        if (tmp > 0.0) ad += (((close - low) - (high - close)) / tmp) * ((double) inVolume[today]);
        //        today++;
        //    }
        //    ;
        //    fastEMA = (fastk * ad) + (one_minus_fastk * fastEMA);
        //    slowEMA = (slowk * ad) + (one_minus_slowk * slowEMA);
        //    outReal[outIdx++] = fastEMA - slowEMA;
        //}
        while ($today <= $endIdx) {
            {
                $high  = $inHigh[$today];
                $low   = $inLow[$today];
                $tmp   = $high - $low;
                $close = $inClose[$today];
                if ($tmp > 0.0) {
                    $ad += ((($close - $low) - ($high - $close)) / $tmp) * $inVolume[$today];
                }
                $today++;
            }
            $fastEMA            = ($fastK * $ad) + ($oneMinusFastK * $fastEMA);
            $slowEMA            = ($slowK * $ad) + ($oneMinusSlowK * $slowEMA);
            $outReal[$outIdx++] = $fastEMA - $slowEMA;
        }
        //outNBElement.value = outIdx;
        $outNBElement->value = $outIdx;

        //return RetCode.Success;
        return RetCode::Success;
    }

    protected function emaLookback(int $optInTimePeriod)
    {
        if ((int)$optInTimePeriod == PHP_INT_MIN) {
            $optInTimePeriod = 30;
        } elseif ((int)$optInTimePeriod < 2 || (int)$optInTimePeriod > 100000) {
            return -1;
        }

        return $optInTimePeriod - 1 + $this->unstablePeriod[FuncUnstId::EMA];
    }

}
