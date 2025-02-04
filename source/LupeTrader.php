<?php

namespace LupeCode\phpTraderNative;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;

class LupeTrader extends Trader
{
    /**
     * Vector arc cosine
     *
     * Calculates the arc cosine for each value in input and returns the resulting array.
     *
     * @param array $real Array of values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function acos(array $real): array
    {
        return array_map(fn($x) => acos($x), $real);
    }

    /**
     * Chaikin A/D Line
     *
     * This indicator is a volume based indicator developed by Marc Chaikin which measures the cumulative flow of money into and out of an instrument.
     * The A/D line is calculated by multiplying the specific period’s volume with a multiplier that is based on the relationship of the closing price to the high-low range.
     * The A/D Line is formed by the running total of the Money Flow Volume. This indicator can be used to assert an underlying trend or to predict reversals.
     *
     * The combination of a high positive multiplier value and high volume indicates buying pressure.
     * So even with a downtrend in prices when there is an uptrend in the Accumulation Distribution Line there is indication for buying pressure (accumulation) that may result to a bullish reversal.
     *
     * Conversely a low negative multiplier value combined with, again, high volumes indicates selling pressure (distribution).
     *
     * @param array $high   High price, array of real values.
     * @param array $low    Low price, array of real values.
     * @param array $close  Closing price, array of real values.
     * @param array $volume Volume traded, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function chaikinAccumulationDistributionLine(array $high, array $low, array $close, array $volume): array
    {
        $count = self::verifyArrayCounts([$high, $low, $close, $volume]);
        $result = new \SplFixedArray($count + 1);
        $moneyFlowVolume = 0.0;
        for ($i = 0; $i <= $count; $i++) {
            $denominator = $high[$i] - $low[$i];
            if ($denominator > 0) {
                $moneyFlowMultiplier = (($close[$i] - $low[$i]) - ($high[$i] - $close[$i])) / $denominator;
                $moneyFlowVolume += ($moneyFlowMultiplier * $volume[$i]);
            }
            $result[$i] = $moneyFlowVolume;
        }

        return $result->toArray();
    }

    public static function ad(array $high, array $low, array $close, array $volume): array
    {
        return self::chaikinAccumulationDistributionLine($high, $low, $close, $volume);
    }

    /**
     * Calculates the vector addition of real0 to real1 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function add(array $real0, array $real1): array
    {
        return array_map(fn($x, $y) => $x + $y, $real0, $real1);
    }

    /**
     * Chaikin A/D Oscillator
     *
     * Chaikin Oscillator is positive when the 3-day EMA moves higher than the 10-day EMA and vice versa.
     *
     * The Chaikin Oscillator is the continuation of the Chaikin A/D Line and is used to observe changes in the A/D Line.
     *
     * The oscillator is based on the difference between the 3-day Exponential Moving Average (EMA) of the A/D Line and the 10-day EMA of the A/D Line and hence adds momentum to the A/D Line.
     * It is helpful for investors to use the Oscillator in order to determine the appropriate timing of trend reversals.
     *
     * When the Chaikin Oscillator turns positive there is indication that the A/D Line will increase and hence a Bullish (buy) signal will be generated. On the other hand a move into negative territory indicates a Bearish (sell) signal.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param array $volume     Volume traded, array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 3, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function chaikinOscillator(array $high, array $low, array $close, array $volume, int $fastPeriod = 3, int $slowPeriod = 10): array
    {
        $count = self::verifyArrayCounts([$high, $low, $close, $volume]);
        $fastK = 2 / ($fastPeriod + 1);
        $slowK = 2 / ($slowPeriod + 1);
        $oneMinusFastK = 1 - $fastK;
        $oneMinusSlowK = 1 - $slowK;

        $ad = self::ad($high, $low, $close, $volume);
        $fastEma = $slowEma = $ad[0];
        $output = [];

        for ($i = 1; $i <= $count; $i++) {
            $fastEma = ($fastK * $ad[$i]) + ($oneMinusFastK * $fastEma);
            $slowEma = ($slowK * $ad[$i]) + ($oneMinusSlowK * $slowEma);
            $output[$i] = $fastEma - $slowEma;
        }

        return self::adjustIndexes(array_slice($output, $slowPeriod - 2), $slowPeriod - 1);
    }

    public static function adosc(array $high, array $low, array $close, array $volume, int $fastPeriod = 3, int $slowPeriod = 10): array
    {
        return self::chaikinOscillator($high, $low, $close, $volume, $fastPeriod, $slowPeriod);
    }

    /**
     * Average Directional Movement Index
     *
     * Developed by J. Welles Wilder and described in his book “New Concepts in Technical Trading Systems”, the Average Directional Movement Index (ADX) is a technical indicator that describes if a market or a financial instrument is trending or not.
     *
     * The ADX is a combination of two other indicators developed by Wilder, the positive directional indicator (+DI) and the negative directional indicator (-DI).
     *
     * Wilder recommends buying when +DI is higher than -DI, and selling when +DI is lower than -DI.
     *
     * The ADX indicates trend strength, not trend direction, and it is a lagging indicator.
     *
     * ADX range is between 0 and 100. Generally ADX readings below 20 indicate trend weakness, and readings above 40 indicate trend strength.
     * An extremely strong trend is indicated by readings above 50.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function averageDirectionalMovementIndex(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $count = self::verifyArrayCounts([$high, $low, $close]);
        $plus = self::plusDI($high, $low, $close, $timePeriod);
        $minus = self::minusDI($high, $low, $close, $timePeriod);
        $sum = self::add($plus, $minus);
        $result = new \SplFixedArray($count + 1);
        for ($i = 0; $i <= $count; $i++) {
            $result[$i] = $sum[$i] > 0 ? 100 * abs($plus[$i] - $minus[$i]) / $sum[$i] : 0;
        }

        return $result->toArray();
    }

    /**
     * Plus Directional Indicator
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function plusDI(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return self::plus_di($high, $low, $close, $timePeriod);
    }

    /**
     * Minus Directional Indicator
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function minusDI(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return self::minus_di($high, $low, $close, $timePeriod);
    }

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
    public static function slowstochrsi(
        array $real,
        int $rsi_period = 14,
        int $fastK_Period = 5,
        int $slowK_Period = 3,
        int $slowK_MAType = MovingAverageType::SMA->value,
        int $slowD_Period = 3,
        int $slowD_MAType = MovingAverageType::SMA->value
    ): array {
        $real = \array_values($real);
        $endIdx = count($real) - 1;
        $rsi = [];
        self::checkForError(self::getMomentumIndicators()::rsi(0, $endIdx, $real, $rsi_period, self::$outBegIdx, self::$outNBElement, $rsi));
        $rsi = array_values($rsi);
        $endIdx = self::verifyArrayCounts([&$rsi]);
        $outSlowK = [];
        $outSlowD = [];
        self::checkForError(
            self::getMomentumIndicators()::stoch(
                0,
                $endIdx,
                $rsi,
                $rsi,
                $rsi,
                $fastK_Period,
                $slowK_Period,
                $slowK_MAType,
                $slowD_Period,
                $slowD_MAType,
                self::$outBegIdx,
                self::$outNBElement,
                $outSlowK,
                $outSlowD
            )
        );

        return [
            'SlowK' => self::adjustIndexes($outSlowK, self::$outBegIdx),
            'SlowD' => self::adjustIndexes($outSlowD, self::$outBegIdx),
        ];
    }

}
