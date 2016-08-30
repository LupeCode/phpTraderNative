<?php

namespace LupeCode\phpTraderNative;

trait TraderOriginal
{
    public static function trader_ad(array $inHigh, array $inLow, array $inClose, array $inVolume)
    {
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

    public static function trader_add($real0, $real1)
    {
        $outReal = [];
        $count   = count($real0);
        for ($i = 0; $i < $count; $i++) {
            $outReal[$i] = $real0[$i] + $real1[$i];
        }

        return $outReal;
    }

}