<?php

namespace LupeCode\phpTraderNative;

trait TraderOriginal
{
    use TraderCommon;

    public static function trader_ad(array $inHigh, array $inLow, array $inClose, array $inVolume)
    {
        self::compareArrayCount($inHigh, $inLow, $inClose, $inVolume);

        $ad      = 0.0;
        $outReal = [];
        $count   = count($inHigh);

        for ($i = 0; $i < $count; $i++) {
            $high   = $inHigh[$i];
            $low    = $inLow[$i];
            $diff   = $high - $low;
            $close  = $inClose[$i];
            $volume = $inVolume[$i];
            if ($diff > 0.0) {
                $ad += ((($close - $low) - ($high - $close)) / $diff) * ((float)$volume);
            }
            $outReal[$i] = $ad;
        }

        return $outReal;
    }

    public static function trader_acos(array $inReal)
    {
        $outReal = [];
        foreach ($inReal as $key => $value) {
            $outReal[$key] = acos($value);
        }

        return $outReal;
    }

    public static function trader_add(array $real0, array $real1)
    {
        self::compareArrayCount($real0, $real1);

        $outReal = [];
        $count   = count($real0);
        for ($i = 0; $i < $count; $i++) {
            $outReal[$i] = $real0[$i] + $real1[$i];
        }

        return $outReal;
    }

    public static function trader_adosc(array $inHigh, array $inLow, array $inClose, array $inVolume, int $optInFastPeriod = 3, int $optInSlowPeriod = 10)
    {
        self::compareArrayCount($inHigh, $inLow, $inClose, $inVolume);

        if ($optInFastPeriod < $optInSlowPeriod) {
            $slowestPeriod = $optInSlowPeriod;
        } else {
            $slowestPeriod = $optInFastPeriod;
        }

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
            $ad        = static::trader_ad([$inHigh[$today]], [$inLow[$today]], [$inClose[$today]], [$inVolume[$today]])[0];
            $fastEMA   = ($fastK * $ad) + ($OneMinusFastK * $fastEMA);
            $slowEMA   = ($slowK * $ad) + ($OneMinusSlowK * $slowEMA);
            $outReal[$today] = $fastEMA - $slowEMA;
        }

        return $outReal;
    }

}
