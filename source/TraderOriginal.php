<?php

namespace LupeCode\phpTraderNative;

trait TraderOriginal
{
    use TraderCommon;

    protected static $unstablePeriod = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    public static function getUnstablePeriod(int $id): int
    {
        return static::$unstablePeriod[$id];
    }

    public static function setUnstablePeriod(int $id, int $period): int
    {
        if ($id > FuncUnstId::ALL) {
            return RetCode::BadParam;
        }
        static::$unstablePeriod[$id] = $period;

        return RetCode::Success;
    }

    protected static function checkStartEnd(int $startIdx, int $endIdx)
    {
        if ($startIdx < 0) {
            return RetCode::OutOfRangeStartIndex;
        }
        if ($endIdx < 0 || $endIdx < $startIdx) {
            return RetCode::OutOfRangeEndIndex;
        }

        return 0;
    }

    public static function trader_acos(int $startIdx, int $endIdx, array $inReal, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal): int
    {
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }

        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = acos($inReal[$i]);
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public static function trader_ad(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal)
    {
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }

        $nbBar               = $endIdx - $startIdx + 1;
        $outNBElement->value = $nbBar;
        $outBegIdx->value    = $startIdx;
        $currentBar          = $startIdx;
        $outIdx              = 0;
        $ad                  = 0.;
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

        return RetCode::Success;
    }

    public static function trader_add(int $startIdx, int $endIdx, array $inReal0, array $inReal1, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal)
    {
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }

        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] + $inReal1[$i];
        }

        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return RetCode::Success;
    }

    public static function trader_adosc(int $startIdx, int $endIdx, array $inHigh, array $inLow, array $inClose, array $inVolume, int $optInFastPeriod = 3, int $optInSlowPeriod = 10, MInteger &$outBegIdx, MInteger &$outNBElement, array &$outReal)
    {
        $error = self::checkStartEnd($startIdx, $endIdx);
        if ($error) {
            return $error;
        }

        if ((int)$optInFastPeriod == PHP_INT_MIN) {
            $optInFastPeriod = 3;
        } elseif ((int)$optInFastPeriod < 2 || (int)$optInFastPeriod > 1000) {
            return RetCode::BadParam;
        }
        if ((int)$optInSlowPeriod == PHP_INT_MIN) {
            $optInSlowPeriod = 3;
        } elseif ((int)$optInSlowPeriod < 2 || (int)$optInSlowPeriod > 1000) {
            return RetCode::BadParam;
        }
        if ($optInFastPeriod < $optInSlowPeriod) {
            $slowestPeriod = $optInSlowPeriod;
        } else {
            $slowestPeriod = $optInFastPeriod;
        }

        $lookbackTotal = self::trader_emaLookback($slowestPeriod);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return RetCode::Success;
        }

        $outBegIdx->value=$startIdx;
        $today = $startIdx-$lookbackTotal;
        $ad=0.;


        $fastK         = self::PeriodToK($optInFastPeriod);
        $slowK         = self::PeriodToK($optInSlowPeriod);
        $OneMinusFastK = 1.0 - $fastK;
        $OneMinusSlowK = 1.0 - $slowK;

        $today   = 0;
        $ad      = static::trader_ad([$inHigh[0]], [$inLow[0]], [$inClose[0]], [$inVolume[0]])[0];
        $fastEMA = $ad;
        $slowEMA = $ad;
        $outReal = [];

        for (; $today < $slowestPeriod; $today++) {
            $ad      = static::trader_ad([$inHigh[$today]], [$inLow[$today]], [$inClose[$today]], [$inVolume[$today]])[0];
            $fastEMA = ($fastK * $ad) + ($OneMinusFastK * $fastEMA);
            $slowEMA = ($slowK * $ad) + ($OneMinusSlowK * $slowEMA);
        }

        $count = count($inHigh);

        for (; $today < $count; $today++) {
            $ad              = static::trader_ad([$inHigh[$today]], [$inLow[$today]], [$inClose[$today]], [$inVolume[$today]])[0];
            $fastEMA         = ($fastK * $ad) + ($OneMinusFastK * $fastEMA);
            $slowEMA         = ($slowK * $ad) + ($OneMinusSlowK * $slowEMA);
            $outReal[$today] = $fastEMA - $slowEMA;
        }

        return $outReal;
    }

    public static function trader_emaLookback(int $optInTimePeriod)
    {
        if ((int)$optInTimePeriod == PHP_INT_MIN) {
            $optInTimePeriod = 30;
        } elseif ((int)$optInTimePeriod < 2 || (int)$optInTimePeriod > 100000) {
            return -1;
        }

        return $optInTimePeriod - 1 + static::$unstablePeriod[FuncUnstId::EMA];
    }

}
