<?php

namespace LupeCode\phpTraderNative;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;

class LupeTraderFriendly extends TraderFriendly
{

    /**
     * Slow Stochastic Relative Strength Index
     *
     * @param array $real         Array of real values.
     * @param int   $rsi_period   [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $slowK_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-K line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $slowK_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-K. MovingAverageType::* series of constants should be used.
     * @param int   $slowD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-D line. Valid range from 1 to 100000.
     * @param int   $slowD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-D. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data. [SlowK => [...], SlowD => [...]]
     * @throws \Exception
     */
    public static function slowStochasticRelativeStrengthIndex(array $real, int $rsi_period = 14, int $fastK_Period = 5, int $slowK_Period = 3, int $slowK_MAType = MovingAverageType::SMA, int $slowD_Period = 3, int $slowD_MAType = MovingAverageType::SMA)
    {
        return LupeTrader::slowstochrsi($real, $rsi_period, $fastK_Period, $slowK_Period, $slowK_MAType, $slowD_Period, $slowD_MAType);
    }
}
