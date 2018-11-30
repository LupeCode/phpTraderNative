<?php

namespace LupeCode\phpTraderNative;

use LupeCode\phpTraderNative\TALib\Core\CycleIndicators;
use LupeCode\phpTraderNative\TALib\Core\MathOperators;
use LupeCode\phpTraderNative\TALib\Core\MathTransform;
use LupeCode\phpTraderNative\TALib\Core\MomentumIndicators;
use LupeCode\phpTraderNative\TALib\Core\OverlapStudies;
use LupeCode\phpTraderNative\TALib\Core\PatternRecognition;
use LupeCode\phpTraderNative\TALib\Core\PriceTransform;
use LupeCode\phpTraderNative\TALib\Core\StatisticFunctions;
use LupeCode\phpTraderNative\TALib\Core\VolatilityIndicators;
use LupeCode\phpTraderNative\TALib\Core\VolumeIndicators;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;

class Trader
{

    /** @var CycleIndicators */
    protected static $cycleIndicators = null;
    /** @var MathOperators */
    protected static $mathOperators = null;
    /** @var MathTransform */
    protected static $mathTransform = null;
    /** @var MomentumIndicators */
    protected static $momentumIndicators = null;
    /** @var OverlapStudies */
    protected static $overlapStudies = null;
    /** @var PatternRecognition */
    protected static $patternRecognition = null;
    /** @var PriceTransform */
    protected static $priceTransform = null;
    /** @var StatisticFunctions */
    protected static $statisticFunctions = null;
    /** @var VolatilityIndicators */
    protected static $volatilityIndicators = null;
    /** @var VolumeIndicators */
    protected static $volumeIndicators = null;

    /** @var int */
    protected static $outBegIdx;
    /** @var int */
    protected static $outNBElement;

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\CycleIndicators
     */
    protected static function getCycleIndicators()
    {
        self::prep();
        if (\is_null(self::$cycleIndicators)) {
            self::$cycleIndicators = new CycleIndicators();
            self::$cycleIndicators::construct();
        }

        return self::$cycleIndicators;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\MathOperators
     */
    protected static function getMathOperators()
    {
        self::prep();
        if (\is_null(self::$mathOperators)) {
            self::$mathOperators = new MathOperators();
            self::$mathOperators::construct();
        }

        return self::$mathOperators;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\MathTransform
     */
    protected static function getMathTransform()
    {
        self::prep();
        if (\is_null(self::$mathTransform)) {
            self::$mathTransform = new MathTransform();
            self::$mathTransform::construct();
        }

        return self::$mathTransform;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\MomentumIndicators
     */
    protected static function getMomentumIndicators()
    {
        self::prep();
        if (\is_null(self::$momentumIndicators)) {
            self::$momentumIndicators = new MomentumIndicators();
            self::$momentumIndicators::construct();
        }

        return self::$momentumIndicators;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\OverlapStudies
     */
    protected static function getOverlapStudies()
    {
        self::prep();
        if (\is_null(self::$overlapStudies)) {
            self::$overlapStudies = new OverlapStudies();
            self::$overlapStudies::construct();
        }

        return self::$overlapStudies;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\PatternRecognition
     */
    protected static function getPatternRecognition()
    {
        self::prep();
        if (\is_null(self::$patternRecognition)) {
            self::$patternRecognition = new PatternRecognition();
            self::$patternRecognition::construct();
        }

        return self::$patternRecognition;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\PriceTransform
     */
    protected static function getPriceTransform()
    {
        self::prep();
        if (\is_null(self::$priceTransform)) {
            self::$priceTransform = new PriceTransform();
            self::$priceTransform::construct();
        }

        return self::$priceTransform;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\StatisticFunctions
     */
    protected static function getStatisticFunctions()
    {
        self::prep();
        if (\is_null(self::$statisticFunctions)) {
            self::$statisticFunctions = new StatisticFunctions();
            self::$statisticFunctions::construct();
        }

        return self::$statisticFunctions;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\VolatilityIndicators
     */
    protected static function getVolatilityIndicators()
    {
        self::prep();
        if (\is_null(self::$volatilityIndicators)) {
            self::$volatilityIndicators = new VolatilityIndicators();
            self::$volatilityIndicators::construct();
        }

        return self::$volatilityIndicators;
    }

    /**
     * @return \LupeCode\phpTraderNative\TALib\Core\VolumeIndicators
     */
    protected static function getVolumeIndicators()
    {
        self::prep();
        if (\is_null(self::$volumeIndicators)) {
            self::$volumeIndicators = new VolumeIndicators();
            self::$volumeIndicators::construct();
        }

        return self::$volumeIndicators;
    }

    protected static function prep()
    {
        self::$outBegIdx    = 0;
        self::$outNBElement = 0;
    }

    /**
     * @param int $ReturnCode
     *
     * @throws \Exception
     */
    protected static function checkForError(int $ReturnCode)
    {
        switch ($ReturnCode) {
            case ReturnCode::Success:
                return;
            default:
                throw new \Exception(ReturnCode::Messages[$ReturnCode], $ReturnCode);
        }
    }

    /**
     * @param array $arrays
     *
     * @return int
     * @throws \Exception
     */
    protected static function verifyArrayCounts(array $arrays)
    {
        $count = count($arrays[0]);
        foreach ($arrays as &$array) {
            if (count($array) !== $count) {
                throw new \Exception(ReturnCode::Messages[ReturnCode::UnevenParameters], ReturnCode::UnevenParameters);
            }
            $array = \array_values($array);
        }

        return $count - 1;
    }

    /**
     * @param array $outReal
     * @param int   $offset
     *
     * @return array
     */
    protected static function adjustIndexes(array $outReal, int $offset): array
    {
        $newOutReal = [];
        $outReal    = \array_values($outReal);
        foreach ($outReal as $index => $inDouble) {
            $newOutReal[$index + $offset] = $inDouble;
        }

        return $newOutReal;
    }

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
    public static function acos(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::acos(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function ad(array $high, array $low, array $close, array $volume): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close, &$volume]);
        $outReal = [];
        self::checkForError(self::getVolumeIndicators()::ad(0, $endIdx, $high, $low, $close, $volume, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::checkForError(self::getMathOperators()::add(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function adosc(array $high, array $low, array $close, array $volume, int $fastPeriod = 3, int $slowPeriod = 10): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close, &$volume]);
        $outReal = [];
        self::checkForError(self::getVolumeIndicators()::adOsc(0, $endIdx, $high, $low, $close, $volume, $fastPeriod, $slowPeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function adx(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::adx(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function adxr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::adxr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function apo(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::apo(0, $endIdx, $real, $fastPeriod, $slowPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function aroon(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx       = self::verifyArrayCounts([&$high, &$low]);
        $outAroonDown = [];
        $outAroonUp   = [];
        self::checkForError(self::getMomentumIndicators()::aroon(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outAroonDown, $outAroonUp));

        return ['AroonDown' => self::adjustIndexes($outAroonDown, self::$outBegIdx), 'AroonUp' => self::adjustIndexes($outAroonUp, self::$outBegIdx)];
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
    public static function aroonosc(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::aroonOsc(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function asin(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::asin(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function atan(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::atan(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function atr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getVolumeIndicators()::atr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function avgprice(array $open, array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getPriceTransform()::avgPrice(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function bbands(array $real, int $timePeriod = 5, float $nbDevUp = 2.0, float $nbDevDn = 2.0, int $mAType = MovingAverageType::SMA): array
    {
        $real              = \array_values($real);
        $endIdx            = count($real) - 1;
        $outRealUpperBand  = [];
        $outRealMiddleBand = [];
        $outRealLowerBand  = [];
        self::checkForError(self::getOverlapStudies()::bbands(0, $endIdx, $real, $timePeriod, $nbDevUp, $nbDevDn, $mAType, self::$outBegIdx, self::$outNBElement, $outRealUpperBand, $outRealMiddleBand, $outRealLowerBand));

        return
            [
                'UpperBand'  => self::adjustIndexes($outRealUpperBand, self::$outBegIdx),
                'MiddleBand' => self::adjustIndexes($outRealMiddleBand, self::$outBegIdx),
                'LowerBand'  => self::adjustIndexes($outRealLowerBand, self::$outBegIdx),
            ];
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
    public static function beta(array $real0, array $real1, int $timePeriod = 5): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::beta(0, $endIdx, $real0, $real1, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function bop(array $open, array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::bop(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function cci(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::cci(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function cdl2crows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdl2Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdl3blackcrows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdl3BlackCrows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdl3inside(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdl3Inside(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdl3linestrike(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdl3LineStrike(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdl3outside(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdl3Outside(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdl3starsinsouth(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdl3StarsInSouth(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdl3whitesoldiers(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdl3WhiteSoldiers(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlabandonedbaby(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlAbandonedBaby(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdladvanceblock(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlAdvanceBlock(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlbelthold(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlBeltHold(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlbreakaway(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlBreakaway(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlclosingmarubozu(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlClosingMarubozu(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlconcealbabyswall(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlConcealBabysWall(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlcounterattack(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlCounterAttack(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdldarkcloudcover(array $open, array $high, array $low, array $close, float $penetration = 0.5): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlDarkCloudCover(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdldoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdldojistar(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdldojistar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdldragonflydoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlDragonflyDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlengulfing(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlEngulfing(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdleveningdojistar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlEveningDojiStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdleveningstar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlEveningStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlgapsidesidewhite(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlGapSideSideWhite(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
    }

    /**
     * Gravestone Doji
     *
     * @param array $open  Opening price, arr
     *                     ay of real values.
     * @param array $high  High price, array of real values.
     * @param array $low   Low price, array of real values.
     * @param array $close Closing price, array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cdlgravestonedoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlGravestoneDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlhammer(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHammer(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlhangingman(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $endIdx     = count($high) - 1;
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHangingMan(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlharami(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHarami(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlharamicross(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHaramiCross(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlhighwave(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHighWave(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlhikkake(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHikkake(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlhikkakemod(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHikkakeMod(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlhomingpigeon(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlHomingPigeon(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlidentical3crows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlIdentical3Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlinneck(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlInNeck(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlinvertedhammer(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlInvertedHammer(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlkicking(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlKicking(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlkickingbylength(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlKickingByLength(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlladderbottom(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlLadderBottom(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdllongleggeddoji(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlLongLeggedDoji(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdllongline(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlLongLine(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlmarubozu(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlMarubozu(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlmatchinglow(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlMatchingLow(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlmathold(array $open, array $high, array $low, array $close, float $penetration = 0.5): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlMatHold(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlmorningdojistar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlMorningDojiStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlmorningstar(array $open, array $high, array $low, array $close, float $penetration = 0.3): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlMorningStar(0, $endIdx, $open, $high, $low, $close, $penetration, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlonneck(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlOnNeck(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlpiercing(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlPiercing(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlrickshawman(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlRickshawMan(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlrisefall3methods(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlRiseFall3Methods(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlseparatinglines(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlSeparatingLines(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlshootingstar(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlShootingStar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlshortline(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlShortLine(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlspinningtop(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlSpinningTop(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlstalledpattern(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlStalledPattern(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlsticksandwich(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlStickSandwich(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdltakuri(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlTakuri(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdltasukigap(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlTasukiGap(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlthrusting(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlThrusting(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdltristar(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlTristar(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlunique3river(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlUnique3River(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlupsidegap2crows(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlUpsideGap2Crows(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function cdlxsidegap3methods(array $open, array $high, array $low, array $close): array
    {
        $endIdx     = self::verifyArrayCounts([&$open, &$high, &$low, &$close]);
        $outInteger = [];
        self::checkForError(self::getPatternRecognition()::cdlXSideGap3Methods(0, $endIdx, $open, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function ceil(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::ceil(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Chande Momentum Oscillator
     *
     * A technical momentum indicator invented by the technical analyst Tushar Chande.
     * It is created by calculating the difference between the sum of all recent gains and the sum of all recent losses and then dividing the result by the sum of all price movement over the period.
     * This oscillator is similar to other momentum indicators such as the Relative Strength Index and the Stochastic Oscillator because it is range bounded (+100 and -100).
     *
     * @param array $real       Array of real values.
     * @param int   $timePeriod [OPTIONAL] [DEFAULT 14, SUGGESTED 4-200] Number of period. Valid range from 2 to 100000.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function cmo(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::cmo(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function correl(array $real0, array $real1, int $timePeriod = 30): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::correl(0, $endIdx, $real0, $real1, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function cos(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::cos(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function cosh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::cosh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function dema(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::dema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function div(array $real0, array $real1): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::checkForError(self::getMathOperators()::div(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function dx(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::dx(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function ema(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::ema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function exp(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::exp(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function floor(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::floor(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Dominant Cycle Period
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_dcperiod(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getCycleIndicators()::htDcPeriod(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Dominant Cycle Phase
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_dcphase(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getCycleIndicators()::htDcPhase(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Phasor Components
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data. [Quadrature => [...], InPhase => [...]]
     * @throws \Exception
     */
    public static function ht_phasor(array $real): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outInPhase    = [];
        $outQuadrature = [];
        self::checkForError(self::getCycleIndicators()::htPhasor(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outInPhase, $outQuadrature));

        return [
            "Quadrature" => self::adjustIndexes($outQuadrature, self::$outBegIdx),
            "InPhase"    => self::adjustIndexes($outInPhase, self::$outBegIdx),
        ];
    }

    /**
     * Hilbert Transform - SineWave
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data. [LeadSine => [...], Sine => [...]]
     * @throws \Exception
     */
    public static function ht_sine(array $real): array
    {
        $real        = \array_values($real);
        $endIdx      = count($real) - 1;
        $outSine     = [];
        $outLeadSine = [];
        self::checkForError(self::getCycleIndicators()::htSine(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outSine, $outLeadSine));

        return [
            "LeadSine" => self::adjustIndexes($outLeadSine, self::$outBegIdx),
            "Sine"     => self::adjustIndexes($outSine, self::$outBegIdx),
        ];
    }

    /**
     * Hilbert Transform - Instantaneous Trendline
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_trendline(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::htTrendline(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Hilbert Transform - Trend vs Cycle Mode
     *
     * @param array $real Array of real values.
     *
     * @return array Returns an array with calculated data.
     * @throws \Exception
     */
    public static function ht_trendmode(array $real): array
    {
        $real       = \array_values($real);
        $endIdx     = count($real) - 1;
        $outInteger = [];
        self::checkForError(self::getCycleIndicators()::htTrendMode(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outInteger));

        return self::adjustIndexes($outInteger, self::$outBegIdx);
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
    public static function kama(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::kama(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function linearreg_angle(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::linearRegAngle(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function linearreg_intercept(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::linearRegIntercept(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function linearreg_slope(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::linearRegSlope(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function linearreg(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::linearReg(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function ln(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::ln(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function log10(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::log10(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function ma(array $real, int $timePeriod = 30, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::movingAverage(0, $endIdx, $real, $timePeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

    /**
     * Moving Average Convergence/Divergence
     *
     * @param array $real         Array of real values.
     * @param int   $fastPeriod   [OPTIONAL] [DEFAULT 12, SUGGESTED 4-200] Number of period for the fast MA. Valid range from 2 to 100000.
     * @param int   $slowPeriod   [OPTIONAL] [DEFAULT 26, SUGGESTED 4-200] Number of period for the slow MA. Valid range from 2 to 100000.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data. [MACD => [...], MACDSignal => [...], MACDHist => [...]]
     * @throws \Exception
     */
    public static function macd(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $signalPeriod = 9): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        self::checkForError(self::getMomentumIndicators()::macd(0, $endIdx, $real, $fastPeriod, $slowPeriod, $signalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist));

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx),
            ];
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
     * @return array Returns an array with calculated data. [MACD => [...], MACDSignal => [...], MACDHist => [...]]
     * @throws \Exception
     */
    public static function macdext(array $real, int $fastPeriod = 12, int $fastMAType = MovingAverageType::SMA, int $slowPeriod = 26, int $slowMAType = MovingAverageType::SMA, int $signalPeriod = 9, int $signalMAType = MovingAverageType::SMA): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        self::checkForError(self::getMomentumIndicators()::macdExt(0, $endIdx, $real, $fastPeriod, $fastMAType, $slowPeriod, $slowMAType, $signalPeriod, $signalMAType, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist));

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx),
            ];
    }

    /**
     * Moving Average Convergence/Divergence Fix 12/26
     *
     * @param array $real         Array of real values.
     * @param int   $signalPeriod [OPTIONAL] [DEFAULT 9, SUGGESTED 1-200] Smoothing for the signal line (nb of period). Valid range from 1 to 100000.
     *
     * @return array Returns an array with calculated data. [MACD => [...], MACDSignal => [...], MACDHist => [...]]
     * @throws \Exception
     */
    public static function macdfix(array $real, int $signalPeriod = 9): array
    {
        $real          = \array_values($real);
        $endIdx        = count($real) - 1;
        $outMACD       = [];
        $outMACDSignal = [];
        $outMACDHist   = [];
        self::checkForError(self::getMomentumIndicators()::macdFix(0, $endIdx, $real, $signalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist));

        return
            [
                'MACD'       => self::adjustIndexes($outMACD, self::$outBegIdx),
                'MACDSignal' => self::adjustIndexes($outMACDSignal, self::$outBegIdx),
                'MACDHist'   => self::adjustIndexes($outMACDHist, self::$outBegIdx),
            ];
    }

    /**
     * MESA Adaptive Moving Average
     *
     * @param array $real      Array of real values.
     * @param float $fastLimit [OPTIONAL] [DEFAULT 0.5, SUGGESTED 0.21-0.80] Upper limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     * @param float $slowLimit [OPTIONAL] [DEFAULT 0.05, SUGGESTED 0.01-0.60] Lower limit use in the adaptive algorithm. Valid range from 0.01 to 0.99.
     *
     * @return array Returns an array with calculated data. [MAMA => [...], FAMA => [...]]
     * @throws \Exception
     */
    public static function mama(array $real, float $fastLimit = 0.5, float $slowLimit = 0.05): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outMAMA = [];
        $outFAMA = [];
        self::checkForError(self::getOverlapStudies()::mama(0, $endIdx, $real, $fastLimit, $slowLimit, self::$outBegIdx, self::$outNBElement, $outMAMA, $outFAMA));

        return
            [
                'MAMA' => self::adjustIndexes($outMAMA, self::$outBegIdx),
                'FAMA' => self::adjustIndexes($outFAMA, self::$outBegIdx),
            ];
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
    public static function mavp(array $real, array $periods, int $minPeriod = 2, int $maxPeriod = 30, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::movingAverageVariablePeriod(0, $endIdx, $real, $periods, $minPeriod, $maxPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function max(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathOperators()::max(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function maxindex(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathOperators()::maxIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function medprice(array $high, array $low): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::checkForError(self::getPriceTransform()::medPrice(0, $endIdx, $high, $low, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function mfi(array $high, array $low, array $close, array $volume, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close, &$volume]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::mfi(0, $endIdx, $high, $low, $close, $volume, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function midpoint(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::midPoint(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function midprice(array $high, array $low, int $timePeriod = 14)
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::midPrice(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function min(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathOperators()::min(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function minindex(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathOperators()::minIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function minmax(array $real, int $timePeriod = 30): array
    {
        $real   = \array_values($real);
        $endIdx = count($real) - 1;
        $outMin = [];
        $outMax = [];
        self::checkForError(self::getMathOperators()::minMax(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax));

        return [
            'Min' => self::adjustIndexes($outMin, self::$outBegIdx),
            'Max' => self::adjustIndexes($outMax, self::$outBegIdx),
        ];
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
    public static function minmaxindex(array $real, int $timePeriod = 30): array
    {
        $real   = \array_values($real);
        $endIdx = count($real) - 1;
        $outMin = [];
        $outMax = [];
        self::checkForError(self::getMathOperators()::minMaxIndex(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax));

        return [
            'Min' => self::adjustIndexes($outMin, self::$outBegIdx),
            'Max' => self::adjustIndexes($outMax, self::$outBegIdx),
        ];
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
    public static function minus_di(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::minusDI(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function minus_dm(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::minusDM(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function mom(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::mom(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function mult(array $real0, array $real1): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::checkForError(self::getMathOperators()::mult(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function natr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getVolatilityIndicators()::natr(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function obv(array $real, array $volume): array
    {
        $endIdx  = self::verifyArrayCounts([&$real, &$volume]);
        $outReal = [];
        self::checkForError(self::getVolumeIndicators()::obv(0, $endIdx, $real, $volume, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function plus_di(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::plusDI(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function plus_dm(array $high, array $low, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::plusDM(0, $endIdx, $high, $low, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function ppo(array $real, int $fastPeriod = 12, int $slowPeriod = 26, int $mAType = MovingAverageType::SMA): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::ppo(0, $endIdx, $real, $fastPeriod, $slowPeriod, $mAType, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function roc(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::roc(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function rocp(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::rocp(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function rocr100(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::rocr100(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function rocr(array $real, int $timePeriod = 10): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::rocr(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function rsi(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::rsi(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function sar(array $high, array $low, float $acceleration = 0.02, float $maximum = 0.2): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::sar(0, $endIdx, $high, $low, $acceleration, $maximum, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function sarext(array $high, array $low, float $startValue = 0.0, float $offsetOnReverse = 0.0, float $accelerationInitLong = 0.02, float $accelerationLong = 0.02, float $accelerationMaxLong = 0.2, float $accelerationInitShort = 0.02, float $accelerationShort = 0.02, float $accelerationMaxShort = 0.2): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low]);
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::sarExt(0, $endIdx, $high, $low, $startValue, $offsetOnReverse, $accelerationInitLong, $accelerationLong, $accelerationMaxLong, $accelerationInitShort, $accelerationShort, $accelerationMaxShort, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function sin(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::sin(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function sinh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::sinh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function sma(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::sma(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function sqrt(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::sqrt(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function stddev(array $real, int $timePeriod = 5, float $nbDev = 1.0): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::stddev(0, $endIdx, $real, $timePeriod, $nbDev, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
     * @return array Returns an array with calculated data. [SlowK => [...], SlowD => [...]]
     * @throws \Exception
     */
    public static function stoch(array $high, array $low, array $close, int $fastK_Period = 5, int $slowK_Period = 3, int $slowK_MAType = MovingAverageType::SMA, int $slowD_Period = 3, int $slowD_MAType = MovingAverageType::SMA): array
    {
        $endIdx   = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outSlowK = [];
        $outSlowD = [];
        self::checkForError(self::getMomentumIndicators()::stoch(0, $endIdx, $high, $low, $close, $fastK_Period, $slowK_Period, $slowK_MAType, $slowD_Period, $slowD_MAType, self::$outBegIdx, self::$outNBElement, $outSlowK, $outSlowD));

        return [
            'SlowK' => self::adjustIndexes($outSlowK, self::$outBegIdx),
            'SlowD' => self::adjustIndexes($outSlowD, self::$outBegIdx),
        ];
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
     * @return array Returns an array with calculated data. [FastK => [...], FastD => [...]]
     * @throws \Exception
     */
    public static function stochf(array $high, array $low, array $close, int $fastK_Period = 5, int $fastD_Period = 3, int $fastD_MAType = MovingAverageType::SMA): array
    {
        $endIdx   = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outFastK = [];
        $outFastD = [];
        self::checkForError(self::getMomentumIndicators()::stochF(0, $endIdx, $high, $low, $close, $fastK_Period, $fastD_Period, $fastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD));

        return [
            'FastK' => self::adjustIndexes($outFastK, self::$outBegIdx),
            'FastD' => self::adjustIndexes($outFastD, self::$outBegIdx),
        ];
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
     * @return array Returns an array with calculated data. [FastK => [...], FastD => [...]]
     * @throws \Exception
     */
    public static function stochrsi(array $real, int $timePeriod = 14, int $fastK_Period = 5, int $fastD_Period = 3, int $fastD_MAType = MovingAverageType::SMA): array
    {
        $real     = \array_values($real);
        $endIdx   = count($real) - 1;
        $outFastK = [];
        $outFastD = [];
        self::checkForError(self::getMomentumIndicators()::stochRsi(0, $endIdx, $real, $timePeriod, $fastK_Period, $fastD_Period, $fastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD));

        return [
            'FastK' => self::adjustIndexes($outFastK, self::$outBegIdx),
            'FastD' => self::adjustIndexes($outFastD, self::$outBegIdx),
        ];
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
    public static function sub(array $real0, array $real1): array
    {
        $endIdx  = self::verifyArrayCounts([&$real0, &$real1]);
        $outReal = [];
        self::checkForError(self::getMathOperators()::sub(0, $endIdx, $real0, $real1, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function sum(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathOperators()::sum(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function t3(array $real, int $timePeriod = 5, float $vFactor = 0.7): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::t3(0, $endIdx, $real, $timePeriod, $vFactor, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function tan(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::tan(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function tanh(array $real): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMathTransform()::tanh(0, $endIdx, $real, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function tema(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::tema(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function trange(array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getVolatilityIndicators()::trueRange(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function trima(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::trima(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function trix(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::trix(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function tsf(array $real, int $timePeriod = 14): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::tsf(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function typprice(array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getPriceTransform()::typPrice(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function ultosc(array $high, array $low, array $close, int $timePeriod1 = 7, int $timePeriod2 = 14, int $timePeriod3 = 28): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::ultOsc(0, $endIdx, $high, $low, $close, $timePeriod1, $timePeriod2, $timePeriod3, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function var(array $real, int $timePeriod = 5, float $nbDev = 1.0): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getStatisticFunctions()::variance(0, $endIdx, $real, $timePeriod, $nbDev, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function wclprice(array $high, array $low, array $close): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getPriceTransform()::wclPrice(0, $endIdx, $high, $low, $close, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function willr(array $high, array $low, array $close, int $timePeriod = 14): array
    {
        $endIdx  = self::verifyArrayCounts([&$high, &$low, &$close]);
        $outReal = [];
        self::checkForError(self::getMomentumIndicators()::willR(0, $endIdx, $high, $low, $close, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
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
    public static function wma(array $real, int $timePeriod = 30): array
    {
        $real    = \array_values($real);
        $endIdx  = count($real) - 1;
        $outReal = [];
        self::checkForError(self::getOverlapStudies()::wma(0, $endIdx, $real, $timePeriod, self::$outBegIdx, self::$outNBElement, $outReal));

        return self::adjustIndexes($outReal, self::$outBegIdx);
    }

}
