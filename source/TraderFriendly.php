<?php

namespace LupeCode\phpTraderNative;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\Trader;

class TraderFriendly
{

    /**
     * Vector arc cosine
     *
     * Calculates the arc cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathArcCosine(array $real): array
    {
        return Trader::acos($real);
    }

    /**
     * Chaikin Accumulation Distribution Line
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
        return Trader::ad($high, $low, $close, $volume);
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
    public static function mathAddition(array $real0, array $real1): array
    {
        return Trader::add($real0, $real1);
    }

    /**
     * Chaikin Accumulation Distribution Oscillator
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
    public static function chaikinAccumulationDistributionOscillator(array $high, array $low, array $close, array $volume, int $fastPeriod = 3, int $slowPeriod = 10): array
    {
        return Trader::adosc($high, $low, $close, $volume, $fastPeriod, $slowPeriod);
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
        return Trader::adx($high, $low, $close, $timePeriod);
    }

    /**
     * Average Directional Movement Index Rating
     *
     * The Average Directional Movement Index Rating (ADXR) measures the strength of the Average Directional Movement Index (ADX).
     * It's calculated by taking the average of the current ADX and the ADX from one time period before (time periods can vary, but the most typical period used is 14 days).
     *
     * Like the ADX, the ADXR ranges from values of 0 to 100 and reflects strengthening and weakening trends.
     * However, because it represents an average of ADX, values don't fluctuate as dramatically and some analysts believe the indicator helps better display trends in volatile markets.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function averageDirectionalMovementIndexRating(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::adxr($high, $low, $close, $timePeriod);
    }

    /**
     * Absolute Price Oscillator
     *
     * The Absolute Price Oscillator (APO) is based on the absolute differences between two moving averages of different lengths, a ‘Fast’ and a ‘Slow’ moving average.
     * A positive indicator value indicates an upward movement, while negative readings signal a downward trend.
     *
     * Divergences form when a new high or low in price is not confirmed by the Absolute Price Oscillator (APO).
     * A bullish divergence forms when price make a lower low, but the APO forms a higher low.
     * This indicates less downward momentum that could foreshadow a bullish reversal.
     * A bearish divergence forms when price makes a higher high, but the APO forms a lower high.
     * This shows less upward momentum that could foreshadow a bearish reversal.
     *
     * @param array $real       Array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function absolutePriceOscillator(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $mAType = MovingAverageType::SMA): array
    {
        return Trader::apo($real, $fastPeriod, $slowPeriod, $mAType);
    }

    /**
     * Aroon
     *
     * The Aroon indicator was developed by Tushar Chande in 1995.
     *
     * Both the Aroon up and the Aroon down fluctuate between zero and 100, with values close to 100 indicating a strong trend, and zero indicating a weak trend.
     * The lower the Aroon up, the weaker the uptrend and the stronger the downtrend, and vice versa.
     * The main assumption underlying this indicator is that a stock's price will close at record highs in an uptrend, and record lows in a downtrend.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns a 2D array with calculated data. [AroonDown => [...], AroonUp => [...]]
     * @throws \Exception
     */
    public static function aroonIndicator(array $high, array $low, int $timePeriod = 14): array
    {
        return Trader::aroon($high, $low, $timePeriod);
    }

    /**
     * Aroon Oscillator
     *
     * The Aroon oscillator is calculated by subtracting Aroon down from Aroon up.
     * Readings above zero indicate that an uptrend is present, while readings below zero indicate that a downtrend is present.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function aroonOscillator(array $high, array $low, int $timePeriod = 14): array
    {
        return Trader::aroonosc($high, $low, $timePeriod);
    }

    /**
     * Vector Trigonometric ASin
     *
     * Calculates the arc sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathArcSine(array $real): array
    {
        return Trader::asin($real);
    }

    /**
     * Vector Trigonometric ATan
     *
     * Calculates the arc tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathArcTangent(array $real): array
    {
        return Trader::atan($real);
    }

    /**
     * Average True Range
     *
     * The average true range (ATR) is a measure of volatility introduced by Welles Wilder in his book, "New Concepts in Technical Trading Systems."
     * The true range indicator is the greatest of the following:
     *      current high less the current low,
     *      the absolute value of the current high less the previous close,
     *      and the absolute value of the current low less the previous close.
     * The average true range is a moving average, generally 14 days, of the true ranges.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function averageTrueRange(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::atr($high, $low, $close, $timePeriod);
    }

    /**
     * Average Price
     *
     * An average price is a representative measure of a range of prices that is calculated by taking the sum of the values and dividing it by the number of prices being examined.
     * The average price reduces the range into a single value, which can then be compared to any point to determine if the value is higher or lower than what would be expected.
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function averagePrice(array $open, array $high, array $low, array $close): array
    {
        return Trader::avgprice($open, $high, $low, $close);
    }

    /**
     * Bollinger Bands
     *
     * A Bollinger Band® is a band plotted two standard deviations away from a simple moving average, developed by famous technical trader John Bollinger.
     *
     * Because standard deviation is a measure of volatility, Bollinger Bands® adjust themselves to the market conditions.
     * When the markets become more volatile, the bands widen (move further away from the average), and during less volatile periods, the bands contract (move closer to the average).
     * The tightening of the bands is often used by technical traders as an early indication that the volatility is about to increase sharply.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDevUp    [OPTIONAL] [DEFAULT 2.0, SUGGESTED -2.0-2.0 INCREMENT 0.2] Deviation multiplier for upper band. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param float $nbDevDn    [OPTIONAL] [DEFAULT 2.0, SUGGESTED -2.0-2.0 INCREMENT 0.2] Deviation multiplier for lower band. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns a 2D array with calculated data. [UpperBand => [...], MiddleBand => [...], LowerBand => [...]]
     * @throws \Exception
     */
    public static function bollingerBands(array $real, int $timePeriod = 5, float $nbDevUp = 2.0, float $nbDevDn = 2.0, int $mAType = MovingAverageType::SMA): array
    {
        return Trader::bbands($real, $timePeriod, $nbDevUp, $nbDevDn, $mAType);
    }

    /**
     * Beta
     *
     * Beta is a measure of the volatility, or systematic risk, of a security or a portfolio in comparison to the market as a whole.
     * Beta is used in the capital asset pricing model (CAPM), which calculates the expected return of an asset based on its beta and expected market returns.
     * Beta is also known as the beta coefficient.
     *
     * A beta of 1 indicates that the security's price moves with the market.
     * A beta of less than 1 means that the security is theoretically less volatile than the market.
     * A beta of greater than 1 indicates that the security's price is theoretically more volatile than the market.
     *
     * @param array $real0      Array of real values.
     * @param array $real1      Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function betaVolatility(array $real0, array $real1, int $timePeriod = 5): array
    {
        return Trader::beta($real0, $real1, $timePeriod);
    }

    /**
     * Balance Of Power
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function balanceOfPower(array $open, array $high, array $low, array $close): array
    {
        return Trader::bop($open, $high, $low, $close);
    }

    /**
     * Commodity Channel Index
     *
     * An oscillator used in technical analysis to help determine when an investment vehicle has been overbought and oversold.
     * The Commodity Channel Index, first developed by Donald Lambert, quantifies the relationship between the asset's price, a moving average (MA) of the asset's price, and normal deviations (D) from that average.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function commodityChannelIndex(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::cci($high, $low, $close, $timePeriod);
    }

    /**
     * Two Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleTwoCrows(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdl2crows($open, $high, $low, $close);
    }

    /**
     * Three Black Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleThreeBlackCrows(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdl3blackcrows($open, $high, $low, $close);
    }

    /**
     * Three Inside Up/Down
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleThreeInsideUpDown(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdl3inside($open, $high, $low, $close);
    }

    /**
     * Three-Line Strike
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleThreeLineStrike(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdl3linestrike($open, $high, $low, $close);
    }

    /**
     * Three Outside Up/Down
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleThreeOutsideUpDown(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdl3outside($open, $high, $low, $close);
    }

    /**
     * Three Stars In The South
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleThreeStarsInTheSouth(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdl3starsinsouth($open, $high, $low, $close);
    }

    /**
     * Three Advancing White Soldiers
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleThreeWhiteSoldiers(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdl3whitesoldiers($open, $high, $low, $close);
    }

    /**
     * Abandoned Baby
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleAbandonedBaby(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        return Trader::cdlabandonedbaby($open, $high, $low, $close, $penetration);
    }

    /**
     * Advance Block
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleAdvanceBlock(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdladvanceblock($open, $high, $low, $close);
    }

    /**
     * Belt-hold
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleBeltHold(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlbelthold($open, $high, $low, $close);
    }

    /**
     * Breakaway
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleBreakaway(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlbreakaway($open, $high, $low, $close);
    }

    /**
     * Closing Marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleClosingMarubozu(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlclosingmarubozu($open, $high, $low, $close);
    }

    /**
     * Concealing Baby Swallow
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleConcealingBabySwallow(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlconcealbabyswall($open, $high, $low, $close);
    }

    /**
     * Counterattack
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleCounterattack(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlcounterattack($open, $high, $low, $close);
    }

    /**
     * Dark Cloud Cover
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.5] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleDarkCloudCover(array $open, array $high, array $low, array $close, float $penetration = 0.5): array
    {
        return Trader::cdldarkcloudcover($open, $high, $low, $close, $penetration);
    }

    /**
     * Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleDoji(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdldoji($open, $high, $low, $close);
    }

    /**
     * Doji Star
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleDojiStar(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdldojistar($open, $high, $low, $close);
    }

    /**
     * Dragonfly Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleDragonflyDoji(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdldragonflydoji($open, $high, $low, $close);
    }

    /**
     * Engulfing Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleEngulfingPattern(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlengulfing($open, $high, $low, $close);
    }

    /**
     * Evening Doji Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleEveningDojiStar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        return Trader::cdleveningdojistar($open, $high, $low, $close, $penetration);
    }

    /**
     * Evening Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleEveningStar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        return Trader::cdleveningstar($open, $high, $low, $close, $penetration);
    }

    /**
     * Up/Down-gap side-by-side white lines
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleUpDownGapsSideBySideWhiteLines(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlgapsidesidewhite($open, $high, $low, $close);
    }

    /**
     * Gravestone Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleGravestoneDoji(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlgravestonedoji($open, $high, $low, $close);
    }

    /**
     * Hammer
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleHammer(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlhammer($open, $high, $low, $close);
    }

    /**
     * Hanging Man
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleHangingMan(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlhangingman($open, $high, $low, $close);
    }

    /**
     * Harami Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleHarami(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlharami($open, $high, $low, $close);
    }

    /**
     * Harami Cross Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleHaramiCross(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlharamicross($open, $high, $low, $close);
    }

    /**
     * High-Wave Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleHighWave(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlhighwave($open, $high, $low, $close);
    }

    /**
     * Hikkake Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleHikkake(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlhikkake($open, $high, $low, $close);
    }

    /**
     * Modified Hikkake Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleModifiedHikkake(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlhikkakemod($open, $high, $low, $close);
    }

    /**
     * Homing Pigeon
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleHomingPigeon(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlhomingpigeon($open, $high, $low, $close);
    }

    /**
     * Identical Three Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleIdenticalThreeCrows(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlidentical3crows($open, $high, $low, $close);
    }

    /**
     * In-Neck Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleInNeck(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlinneck($open, $high, $low, $close);
    }

    /**
     * Inverted Hammer
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleInvertedHammer(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlinvertedhammer($open, $high, $low, $close);
    }

    /**
     * Kicking
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleKicking(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlkicking($open, $high, $low, $close);
    }

    /**
     * Kicking - bull/bear determined by the longer marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleKickingByLength(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlkickingbylength($open, $high, $low, $close);
    }

    /**
     * Ladder Bottom
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleLadderBottom(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlladderbottom($open, $high, $low, $close);
    }

    /**
     * Long Legged Doji
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleLongLeggedDoji(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdllongleggeddoji($open, $high, $low, $close);
    }

    /**
     * Long Line Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleLongLine(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdllongline($open, $high, $low, $close);
    }

    /**
     * Marubozu
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleMarubozu(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlmarubozu($open, $high, $low, $close);
    }

    /**
     * Matching Low
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleMatchingLow(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlmatchinglow($open, $high, $low, $close);
    }

    /**
     * Mat Hold
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.5] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleMatHold(array $open, array $high, array $low, array $close, float $penetration = 0.5): array
    {
        return Trader::cdlmathold($open, $high, $low, $close, $penetration);
    }

    /**
     * Morning Doji Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleMorningDojiStar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        return Trader::cdlmorningdojistar($open, $high, $low, $close, $penetration);
    }

    /**
     * Morning Star
     *
     * @param array $open        Opening price, array of real values.
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param float $penetration [OPTIONAL] [DEFAULT 0.3] Percentage of penetration of a candle within another candle.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleMorningStar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        return Trader::cdlmorningstar($open, $high, $low, $close, $penetration);
    }

    /**
     * On-Neck Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleOnNeck(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlonneck($open, $high, $low, $close);
    }

    /**
     * Piercing Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candlePiercing(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlpiercing($open, $high, $low, $close);
    }

    /**
     * Rickshaw Man
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleRickshawMan(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlrickshawman($open, $high, $low, $close);
    }

    /**
     * Rising/Falling Three Methods
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleRisingFallingThreeMethods(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlrisefall3methods($open, $high, $low, $close);
    }

    /**
     * Separating Lines
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleSeparatingLines(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlseparatinglines($open, $high, $low, $close);
    }

    /**
     * Shooting Star
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleShootingStar(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlshootingstar($open, $high, $low, $close);
    }

    /**
     * Short Line Candle
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleShortLine(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlshortline($open, $high, $low, $close);
    }

    /**
     * Spinning Top
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleSpinningTop(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlspinningtop($open, $high, $low, $close);
    }

    /**
     * Stalled Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleStalled(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlstalledpattern($open, $high, $low, $close);
    }

    /**
     * Stick Sandwich
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleStickSandwich(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlsticksandwich($open, $high, $low, $close);
    }

    /**
     * Takuri (Dragonfly Doji with very long lower shadow)
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleTakuri(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdltakuri($open, $high, $low, $close);
    }

    /**
     * Tasuki Gap
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleTasukiGap(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdltasukigap($open, $high, $low, $close);
    }

    /**
     * Thrusting Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleThrusting(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlthrusting($open, $high, $low, $close);
    }

    /**
     * Tristar Pattern
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleTristar(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdltristar($open, $high, $low, $close);
    }

    /**
     * Unique 3 River
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleUniqueThreeRiver(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlunique3river($open, $high, $low, $close);
    }

    /**
     * Upside Gap Two Crows
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleUpsideGapTwoCrows(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlupsidegap2crows($open, $high, $low, $close);
    }

    /**
     * Upside/Downside Gap Three Methods
     *
     * @param array $open  Opening price, array of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function candleUpsideDownsideGapThreeMethods(array $open, array $high, array $low, array $close): array
    {
        return Trader::cdlxsidegap3methods($open, $high, $low, $close);
    }

    /**
     * Vector Ceil
     *
     * Calculates the next highest integer for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathCeiling(array $real): array
    {
        return Trader::ceil($real);
    }

    /**
     * Chande Momentum Oscillator
     *
     * A technical momentum indicator invented by the technical analyst Tushar Chande.
     * It is created by calculating the difference between the sum of all recent gains and the sum of all recent losses and then dividing the result by the sum of all price movement over the period.
     * This oscillator is similar to other momentum indicators such as the Relative Strength Index and the Stochastic Oscillator because it is range bounded (+100 and -100).
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function chandeMomentumOscillator(array $real, int $timePeriod): array
    {
        return Trader::cmo($real, $timePeriod);
    }

    /**
     * Pearson's Correlation Coefficient (r)
     *
     * A type of correlation coefficient that represents the relationship between two variables that are measured on the same interval or ratio scale.
     *
     * @param array $real0      Array of real values.
     * @param array $real1      Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function pearsonCorrelationCoefficient(array $real0, array $real1, int $timePeriod = 30): array
    {
        return Trader::correl($real0, $real1, $timePeriod);
    }

    /**
     * Vector Trigonometric Cos
     *
     * Calculates the cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathCosine(array $real): array
    {
        return Trader::cos($real);
    }

    /**
     * Vector Trigonometric Cosh
     *
     * Calculates the hyperbolic cosine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathHyperbolicCosine(array $real): array
    {
        return Trader::cosh($real);
    }

    /**
     * Double Exponential Moving Average
     *
     * A technical indicator developed by Patrick Mulloy that first appeared in the February, 1994 Technical Analysis of Stocks & Commodities.
     * The DEMA is a calculation based on both a single exponential moving average (EMA) and a double EMA.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 3, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function doubleExponentialMovingAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::dema($real, $timePeriod);
    }

    /**
     * Vector Arithmetic Div
     *
     * Divides each value from real0 by the corresponding value from real1 and returns the resulting array.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathDivision(array $real0, array $real1): array
    {
        return Trader::div($real0, $real1);
    }

    /**
     * Directional Movement Index
     *
     * The directional movement index (DMI) is an indicator developed by J. Welles Wilder for identifying when a definable trend is present in an instrument.
     * That is, the DMI tells whether an instrument is trending or not.
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array  Returns an array with calculated data.
     * @throws \Exception
     */
    public static function directionalMovementIndex(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::dx($high, $low, $close, $timePeriod);
    }

    /**
     * Exponential Moving Average
     *
     * An exponential moving average (EMA) is a type of moving average that is similar to a simple moving average, except that more weight is given to the latest data.
     * It's also known as the exponentially weighted moving average.
     * This type of moving average reacts faster to recent price changes than a simple moving average.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function exponentialMovingAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::ema($real, $timePeriod);
    }

    /**
     * Vector Arithmetic Exp
     *
     * Calculates e raised to the power of each value in real. Returns an array with the calculated data.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathExponent(array $real): array
    {
        return Trader::exp($real);
    }

    /**
     * Vector Floor
     *
     * Calculates the next lowest integer for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathFloor(array $real): array
    {
        return Trader::floor($real);
    }

    /**
     * Hilbert Transform - Dominant Cycle Period
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function hilbertTransformDominantCyclePeriod(array $real): array
    {
        return Trader::ht_dcperiod($real);
    }

    /**
     * Hilbert Transform - Dominant Cycle Phase
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function hilbertTransformDominantCyclePhase(array $real): array
    {
        return Trader::ht_dcphase($real);
    }

    /**
     * Hilbert Transform - Phasor Components
     *
     * @param array $real    Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function hilbertTransformPhasorComponents(array $real): array
    {
        return Trader::ht_phasor($real);
    }

    /**
     * Hilbert Transform - SineWave
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function hilbertTransformSineWave(array $real): array
    {
        return Trader::ht_sine($real);
    }

    /**
     * Hilbert Transform - Instantaneous TrendLine
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function hilbertTransformInstantaneousTrendLine(array $real): array
    {
        return Trader::ht_trendline($real);
    }

    /**
     * Hilbert Transform - Trend vs Cycle Mode
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function hilbertTransformTrendVsCycleMode(array $real): array
    {
        return Trader::ht_trendmode($real);
    }

    /**
     * Kaufman Adaptive Moving Average
     *
     * Developed by Perry Kaufman, Kaufman's Adaptive Moving Average (KAMA) is a moving average designed to account for market noise or volatility.
     * KAMA will closely follow prices when the price swings are relatively small and the noise is low.
     * KAMA will adjust when the price swings widen and follow prices from a greater distance.
     * This trend-following indicator can be used to identify the overall trend, time turning points and filter price movements.
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function kaufmanAdaptiveMovingAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::kama($real, $timePeriod);
    }

    /**
     * Linear Regression Angle
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearRegressionAngle(array $real, int $timePeriod = 14): array
    {
        return Trader::linearreg_angle($real, $timePeriod);
    }

    /**
     * Linear Regression Intercept
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearRegressionIntercept(array $real, int $timePeriod = 14): array
    {
        return Trader::linearreg_intercept($real, $timePeriod);
    }

    /**
     * Linear Regression Slope
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearRegressionSlope(array $real, int $timePeriod = 14): array
    {
        return Trader::linearreg_slope($real, $timePeriod);
    }

    /**
     * Linear Regression
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function linearRegression(array $real, int $timePeriod = 14): array
    {
        return Trader::linearreg($real, $timePeriod);
    }

    /**
     * Vector Log Natural
     *
     * Calculates the natural logarithm for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathLogarithmNatural(array $real): array
    {
        return Trader::ln($real);
    }

    /**
     * Vector Log10
     *
     * Calculates the base-10 logarithm for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathLogarithmBase10(array $real): array
    {
        return Trader::log10($real);
    }

    /**
     * Moving average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function movingAverage(array $real, int $timePeriod = 30, int $mAType = MovingAverageType::SMA): array
    {
        return Trader::ma($real, $timePeriod, $mAType);
    }

    /**
     * Moving Average Convergence/Divergence
     *
     * @param array $real         Array of real values.
     * @param int   $fastPeriod   [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod   [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function movingAverageConvergenceDivergence(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $signalPeriod = 9): array
    {
        return Trader::macd($real, $fastPeriod, $slowPeriod, $signalPeriod);
    }

    /**
     * Moving Average Convergence/Divergence with controllable Moving Average type
     *
     * @param array $real         Array of real values.
     * @param int   $fastPeriod   [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $fastMAType   [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MovingAverageType::* series of constants should be used.
     * @param int   $slowPeriod   [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $slowMAType   [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MovingAverageType::* series of constants should be used.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     * @param int   $signalMAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for fast MA. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function movingAverageConvergenceDivergenceExtended(array $real, int $fastPeriod = 12, int $fastMAType = MovingAverageType::SMA, int $slowPeriod = 26, int $slowMAType = MovingAverageType::SMA, int $signalPeriod = 9, int $signalMAType = MovingAverageType::SMA): array
    {
        return Trader::macdext($real, $fastPeriod, $fastMAType, $slowPeriod, $slowMAType, $signalPeriod, $signalMAType);
    }

    /**
     * Moving Average Convergence/Divergence Fix 12/26
     *
     * @param array $real         Array of real values.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function movingAverageConvergenceDivergenceFixed(array $real, int $signalPeriod = 9): array
    {
        return Trader::macdfix($real, $signalPeriod);
    }

    /**
     * MESA Adaptive Moving Average
     *
     * @param array $real      Array of real values.
     * @param float $fastLimit [OPTIONAL] [DEFAULT 0.5, SUGGESTED 0.21-0.80] Upper limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     * @param float $slowLimit [OPTIONAL] [DEFAULT 0.05, SUGGESTED 0.01-0.60] Lower limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mesaAdaptiveMovingAverage(array $real, float $fastLimit = 0.5, float $slowLimit = 0.05): array
    {
        return Trader::mama($real, $fastLimit, $slowLimit);
    }

    /**
     * Moving average with variable period
     *
     * @param array $real      Array of real values.
     * @param array $periods   Array of real values.
     * @param int   $minPeriod [OPTIONAL] [DEFAULT 2, SUGGESTED 4-200] Value less than minimum will be changed to Minimum period. Valid range from 2 to 100000
     * @param int   $maxPeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Value higher than maximum will be changed to Maximum period. Valid range from 2 to 100000
     * @param int   $mAType    [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function movingAverageVariablePeriod(array $real, array $periods, int $minPeriod = 2, int $maxPeriod = 30, int $mAType = MovingAverageType::SMA): array
    {
        return Trader::mavp($real, $periods, $minPeriod, $maxPeriod, $mAType);
    }

    /**
     * Highest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathMax(array $real, int $timePeriod = 30): array
    {
        return Trader::max($real, $timePeriod);
    }

    /**
     * Index of highest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathMaxIndex(array $real, int $timePeriod = 30): array
    {
        return Trader::maxindex($real, $timePeriod);
    }

    /**
     * Median Price
     *
     * @param array $high High price, array of real values.
     * @param array $low  Low price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathMedianPrice(array $high, array $low): array
    {
        return Trader::medprice($high, $low);
    }

    /**
     * Money Flow Index
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param array $volume     Volume traded, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function moneyFlowIndex(array $high, array $low, array $close, array $volume, int $timePeriod = 14): array
    {
        return Trader::mfi($high, $low, $close, $volume, $timePeriod);
    }

    /**
     * MidPoint over period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function middlePoint(array $real, int $timePeriod = 14): array
    {
        return Trader::midpoint($real, $timePeriod);
    }

    /**
     * Midpoint Price over period
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function middlePointPrice(array $high, array $low, int $timePeriod = 14)
    {
        return Trader::midprice($high, $low, $timePeriod);
    }

    /**
     * Lowest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathMin(array $real, int $timePeriod = 30): array
    {
        return Trader::min($real, $timePeriod);
    }

    /**
     * Index of lowest value over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathMinIndex(array $real, int $timePeriod = 30): array
    {
        return Trader::minindex($real, $timePeriod);
    }

    /**
     * Lowest and highest values over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data. [Min => [...], Max => [...]]
     * @throws \Exception
     */
    public static function mathMinMax(array $real, int $timePeriod = 30): array
    {
        return Trader::minmax($real, $timePeriod);
    }

    /**
     * Indexes of lowest and highest values over a specified period
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data. [Min => [...], Max => [...]]
     * @throws \Exception
     */
    public static function mathMinMaxIndex(array $real, int $timePeriod = 30): array
    {
        return Trader::minmaxindex($real, $timePeriod);
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
    public static function minusDirectionalIndicator(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::minus_di($high, $low, $close, $timePeriod);
    }

    /**
     * Minus Directional Movement
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function minusDirectionalMovement(array $high, array $low, int $timePeriod = 14): array
    {
        return Trader::minus_dm($high, $low, $timePeriod);
    }

    /**
     * Momentum
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function momentum(array $real, int $timePeriod = 10): array
    {
        return Trader::mom($real, $timePeriod);
    }

    /**
     * Vector Arithmetic Mult
     *
     * Calculates the vector dot product of real0 with real1 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathMultiply(array $real0, array $real1): array
    {
        return Trader::mult($real0, $real1);
    }

    /**
     * Normalized Average True Range
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function normalizedAverageTrueRange(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::natr($high, $low, $close, $timePeriod);
    }

    /**
     * On Balance Volume
     *
     * @param array $real   Array of real values.
     * @param array $volume Volume traded, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function onBalanceVolume(array $real, array $volume): array
    {
        return Trader::obv($real, $volume);
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
    public static function plusDirectionalIndicator(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::plus_di($high, $low, $close, $timePeriod);
    }

    /**
     * Plus Directional Movement
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function plusDirectionalMovement(array $high, array $low, int $timePeriod = 14): array
    {
        return Trader::plus_dm($high, $low, $timePeriod);
    }

    /**
     * Percentage Price Oscillator
     *
     * @param array $real       Array of real values.
     * @param int   $fastPeriod [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $mAType     [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function percentagePriceOscillator(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $mAType = MovingAverageType::SMA): array
    {
        return Trader::ppo($real, $fastPeriod, $slowPeriod, $mAType);
    }

    /**
     * Rate of change : ((price/prevPrice)-1)*100
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rateOfChange(array $real, int $timePeriod = 10): array
    {
        return Trader::roc($real, $timePeriod);
    }

    /**
     * Rate of change Percentage: (price-prevPrice)/prevPrice
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rateOfChangePercentage(array $real, int $timePeriod = 10): array
    {
        return Trader::rocp($real, $timePeriod);
    }

    /**
     * Rate of change ratio 100 scale: (price/prevPrice)*100
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rateOfChangeRatio100(array $real, int $timePeriod = 10): array
    {
        return Trader::rocr100($real, $timePeriod);
    }

    /**
     * Rate of change ratio: (price/prevPrice)
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 10, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function rateOfChangeRatio(array $real, int $timePeriod = 10): array
    {
        return Trader::rocr($real, $timePeriod);
    }

    /**
     * Relative Strength Index
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function relativeStrengthIndex(array $real, int $timePeriod = 14): array
    {
        return Trader::rsi($real, $timePeriod);
    }

    /**
     * Parabolic SAR
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param float $acceleration [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.20] Acceleration Factor used up to the Maximum value. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $maximum      [OPTIONAL] [DEFAULT 0.2, SUGGESTED 0.20-0.40] Acceleration Factor Maximum value. Valid range from 0 to TRADER_REAL_MAX.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function parabolicSAR(array $high, array $low, float $acceleration = 0.02, float $maximum = 0.2): array
    {
        return Trader::sar($high, $low, $acceleration, $maximum);
    }

    /**
     * Parabolic SAR - Extended
     *
     * @param array $high                  High price, array of real values.
     * @param array $low                   Low price, array of real values.
     * @param float $startValue            [OPTIONAL] [DEFAULT 0.0] Start value and direction. 0 for Auto, >0 for Long, <0 for Short. Valid range from TRADER_REAL_MIN to TRADER_REAL_MAX.
     * @param float $offsetOnReverse       [OPTIONAL] [DEFAULT 0.0, SUGGESTED 0.01-0.15] Percent offset added/removed to initial stop on short/long reversal. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationInitLong  [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.19] Acceleration Factor initial value for the Long direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationLong      [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.20] Acceleration Factor for the Long direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationMaxLong   [OPTIONAL] [DEFAULT 0.2, SUGGESTED 0.20-0.40] Acceleration Factor maximum value for the Long direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationInitShort [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.19] Acceleration Factor initial value for the Short direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationShort     [OPTIONAL] [DEFAULT 0.02, SUGGESTED 0.01-0.20] Acceleration Factor for the Short direction. Valid range from 0 to TRADER_REAL_MAX.
     * @param float $accelerationMaxShort  [OPTIONAL] [DEFAULT 0.2, SUGGESTED 0.20-0.40] Acceleration Factor maximum value for the Short direction. Valid range from 0 to TRADER_REAL_MAX.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function parabolicSARExtended(array $high, array $low, float $startValue = 0.0, float $offsetOnReverse = 0.0, float $accelerationInitLong = 0.02, float $accelerationLong = 0.02, float $accelerationMaxLong = 0.2, float $accelerationInitShort = 0.02, float $accelerationShort = 0.02, float $accelerationMaxShort = 0.2): array
    {
        return Trader::sarext($high, $low, $startValue, $offsetOnReverse, $accelerationInitLong, $accelerationLong, $accelerationMaxLong, $accelerationInitShort, $accelerationShort, $accelerationMaxShort);
    }

    /**
     * Vector Trigonometric Sin
     *
     * Calculates the sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathSine(array $real): array
    {
        return Trader::sin($real);
    }

    /**
     * Vector Trigonometric Sinh
     *
     * Calculates the hyperbolic sine for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathHyperbolicSine(array $real): array
    {
        return Trader::sinh($real);
    }

    /**
     * Simple Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function simpleMovingAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::sma($real, $timePeriod);
    }

    /**
     * Vector Square Root
     *
     * Calculates the square root of each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathSquareRoot(array $real): array
    {
        return Trader::sqrt($real);
    }

    /**
     * Standard Deviation
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDev      [OPTIONAL] [DEFAULT 1.0, SUGGESTED -2-2] Number of deviations
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function standardDeviation(array $real, int $timePeriod = 5, float $nbDev = 1.0): array
    {
        return Trader::stddev($real, $timePeriod, $nbDev);
    }

    /**
     * Stochastic
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param array $close        Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $slowK_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-K line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $slowK_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-K. MovingAverageType::* series of constants should be used.
     * @param int   $slowD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Slow-D line. Valid range from 1 to 100000.
     * @param int   $slowD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Slow-D. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function stochastic(array $high, array $low, array $close, int $fastK_Period = 5, int $slowK_Period = 3, int $slowK_MAType = MovingAverageType::SMA, int $slowD_Period = 3, int $slowD_MAType = MovingAverageType::SMA): array
    {
        return Trader::stoch($high, $low, $close, $fastK_Period, $slowK_Period, $slowK_MAType, $slowD_Period, $slowD_MAType);
    }

    /**
     * Stochastic Fast
     *
     * @param array $high         High price, array of real values.
     * @param array $low          Low price, array of real values.
     * @param array $close        Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Fast-D line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $fastD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Fast-D. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function stochasticFast(array $high, array $low, array $close, int $fastK_Period = 5, int $fastD_Period = 3, int $fastD_MAType = MovingAverageType::SMA): array
    {
        return Trader::stochf($high, $low, $close, $fastK_Period, $fastD_Period, $fastD_MAType);
    }

    /**
     * Stochastic Relative Strength Index
     *
     * @param array $real         Array of real values.
     * @param int   $timePeriod   [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param int   $fastK_Period [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Time period for building the Fast-K line. Valid range from 1 to 100000.
     * @param int   $fastD_Period [OPTIONAL] [DEFAULT 3, SUGGESTED 1-200] Smoothing for making the Fast-D line. Valid range from 1 to 100000, usually set to 3.
     * @param int   $fastD_MAType [OPTIONAL] [DEFAULT TRADER_MA_TYPE_SMA] Type of Moving Average for Fast-D. MovingAverageType::* series of constants should be used.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function stochasticRelativeStrengthIndex(array $real, int $timePeriod = 14, int $fastK_Period = 5, int $fastD_Period = 3, int $fastD_MAType = MovingAverageType::SMA): array
    {
        return Trader::stochrsi($real, $timePeriod, $fastK_Period, $fastD_Period, $fastD_MAType);
    }

    /**
     * Vector Arithmetic Subtraction
     *
     * Calculates the vector subtraction of real1 from real0 and returns the resulting vector.
     *
     * @param array $real0 Array of real values.
     * @param array $real1 Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathSubtraction(array $real0, array $real1): array
    {
        return Trader::sub($real0, $real1);
    }

    /**
     * Summation
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathSummation(array $real, int $timePeriod = 30): array
    {
        return Trader::sum($real, $timePeriod);
    }

    /**
     * Triple Exponential Moving Average (T3)
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     * @param float $vFactor    [OPTIONAL] [DEFAULT 0.7, SUGGESTED 0.01-1.00] Volume Factor. Valid range from 1 to 0.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function tripleExponentialMovingAverageT3(array $real, int $timePeriod = 5, float $vFactor = 0.7): array
    {
        return Trader::t3($real, $timePeriod, $vFactor);
    }

    /**
     * Vector Trigonometric Tan
     *
     * Calculates the tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathTangent(array $real): array
    {
        return Trader::tan($real);
    }

    /**
     * Vector Trigonometric Tanh
     *
     * Calculates the hyperbolic tangent for each value in real and returns the resulting array.
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function mathHyperbolicTangent(array $real): array
    {
        return Trader::tanh($real);
    }

    /**
     * Triple Exponential Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function tripleExponentialMovingAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::tema($real, $timePeriod);
    }

    /**
     * True Range
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function trueRange(array $high, array $low, array $close): array
    {
        return Trader::trange($high, $low, $close);
    }

    /**
     * Triangular Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function triangularMovingAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::trima($real, $timePeriod);
    }

    /**
     * 1-day Rate-Of-Change (ROC) of a Triple Smooth EMA
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 1-200] Number of period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function tripleExponentialAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::trix($real, $timePeriod);
    }

    /**
     * Time Series Forecast
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function timeSeriesForecast(array $real, int $timePeriod = 14): array
    {
        return Trader::tsf($real, $timePeriod);
    }

    /**
     * Typical Price
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function typicalPrice(array $high, array $low, array $close): array
    {
        return Trader::typprice($high, $low, $close);
    }

    /**
     * Ultimate Oscillator
     *
     * @param array $high        High price, array of real values.
     * @param array $low         Low price, array of real values.
     * @param array $close       Closing price, array of real values.
     * @param int   $timePeriod1 [OPTIONAL] [DEFAULT 7, SUGGESTED 1-200] Number of bars for 1st period. Valid range from 1 to 100000.
     * @param int   $timePeriod2 [OPTIONAL] [DEFAULT 14, SUGGESTED 1-200] Number of bars for 2nd period. Valid range from 1 to 100000.
     * @param int   $timePeriod3 [OPTIONAL] [DEFAULT 28, SUGGESTED 1-200] Number of bars for 3rd period. Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ultimateOscillator(array $high, array $low, array $close, int $timePeriod1 = 7, int $timePeriod2 = 14, int $timePeriod3 = 28): array
    {
        return Trader::ultosc($high, $low, $close, $timePeriod1, $timePeriod2, $timePeriod3);
    }

    /**
     * Variance
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 5, SUGGESTED 1-200] Number of period. Valid range from 2 to 100000.
     * @param float $nbDev      [OPTIONAL] [DEFAULT 1.0, SUGGESTED -2-2] Number of deviations
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function variance(array $real, int $timePeriod = 5, float $nbDev = 1.0): array
    {
        return Trader::var($real, $timePeriod, $nbDev);
    }

    /**
     * Weighted Close Price
     *
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function weightedClosePrice(array $high, array $low, array $close): array
    {
        return Trader::wclprice($high, $low, $close);
    }

    /**
     * Williams' %R
     *
     * @param array $high       High price, array of real values.
     * @param array $low        Low price, array of real values.
     * @param array $close      Closing price, array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function williamsR(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        return Trader::willr($high, $low, $close, $timePeriod);
    }

    /**
     * Weighted Moving Average
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 30, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function weightedMovingAverage(array $real, int $timePeriod = 30): array
    {
        return Trader::wma($real, $timePeriod);
    }

}
