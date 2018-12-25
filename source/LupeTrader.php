<?php

namespace LupeCode\phpTraderNative;

use LupeCode\phpTraderNative\LupeTrader\Core\MomentumIndicators;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;

class LupeTrader extends Trader
{

    /**
     * @param array $real
     * @param int   $rsi_period
     * @param int   $fastK_Period
     * @param int   $slowK_Period
     * @param int   $slowK_MAType
     * @param int   $slowD_Period
     * @param int   $slowD_MAType
     *
     * @return array
     * @throws \Exception
     */
    public static function slowstochrsi(array $real, int $rsi_period = 14, int $fastK_Period = 5, int $slowK_Period = 3, int $slowK_MAType = MovingAverageType::SMA, int $slowD_Period = 3, int $slowD_MAType = MovingAverageType::SMA): array
    {
        $real   = \array_values($real);
        $endIdx = count($real) - 1;
        $rsi    = [];
        self::checkForError(self::getMomentumIndicators()::rsi(0, $endIdx, $real, $rsi_period, self::$outBegIdx, self::$outNBElement, $rsi));
        $rsi      = array_values($rsi);
        $endIdx   = self::verifyArrayCounts([&$rsi]);
        $outSlowK = [];
        $outSlowD = [];
        self::checkForError(self::getMomentumIndicators()::stoch(0, $endIdx, $rsi, $rsi, $rsi, $fastK_Period, $slowK_Period, $slowK_MAType, $slowD_Period, $slowD_MAType, self::$outBegIdx, self::$outNBElement, $outSlowK, $outSlowD));

        return [
            'SlowK' => self::adjustIndexes($outSlowK, self::$outBegIdx),
            'SlowD' => self::adjustIndexes($outSlowD, self::$outBegIdx),
        ];
    }

}
