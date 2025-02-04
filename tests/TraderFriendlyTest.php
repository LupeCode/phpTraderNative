<?php

namespace LupeCode\phpTraderNativeTest;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TraderFriendly;
use PHPUnit\Framework\TestCase;

class TraderFriendlyTest extends TestCase
{

    use TestingTrait;

    /**
     * @throws \Exception
     */
    public function testMathArcCosine(): void
    {
        $in = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_acos($in), $this->adjustForPECL(TraderFriendly::mathArcCosine($in)));
    }

    /**
     * @throws \Exception
     */
    public function testChaikinAccumulationDistributionLine(): void
    {
        $this->assertEquals(\trader_ad($this->High, $this->Low, $this->Close, $this->Volume), $this->adjustForPECL(TraderFriendly::chaikinAccumulationDistributionLine($this->High, $this->Low, $this->Close, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testMathAddition(): void
    {
        $this->assertEquals(\trader_add($this->High, $this->Low), $this->adjustForPECL(TraderFriendly::mathAddition($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testChaikinAccumulationDistributionOscillator(): void
    {
        $optInFastPeriod = 3;
        $optInSlowPeriod = 10;
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL(TraderFriendly::chaikinAccumulationDistributionOscillator($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod)));

        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL(TraderFriendly::chaikinAccumulationDistributionOscillator($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAverageDirectionalMovementIndex(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::averageDirectionalMovementIndex($this->High, $this->Low, $this->Close, $optInTimePeriod)));

        $optInTimePeriod = 20;
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::averageDirectionalMovementIndex($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAverageDirectionalMovementIndexRating(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::averageDirectionalMovementIndexRating($this->High, $this->Low, $this->Close, $optInTimePeriod)));

        $optInTimePeriod = 20;
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::averageDirectionalMovementIndexRating($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAbsolutePriceOscillator(): void
    {
        $optInMAType     = MovingAverageType::SMA->value;
        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInFastPeriod = 7;
        $optInSlowPeriod = 20;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::EMA->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::WMA->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::DEMA->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::TEMA->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::TRIMA->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::KAMA->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::MAMA->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::T3->value;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::absolutePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testAroonIndicator(): void
    {
        $optInTimePeriod = 10;
        [$traderAroonDown, $traderAroonUp] = \trader_aroon($this->High, $this->Low, $optInTimePeriod);
        $Output = TraderFriendly::aroonIndicator($this->High, $this->Low, $optInTimePeriod);
        $this->assertEquals($traderAroonDown, $this->adjustForPECL($Output['AroonDown']));
        $this->assertEquals($traderAroonUp, $this->adjustForPECL($Output['AroonUp']));
    }

    /**
     * @throws \Exception
     */
    public function testAroonOscillator(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_aroonosc($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::aroonOscillator($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathArcSine(): void
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_asin($acosArray), $this->adjustForPECL(TraderFriendly::mathArcSine($acosArray)));
    }

    /**
     * @throws \Exception
     */
    public function testMathArcTangent(): void
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_atan($acosArray), $this->adjustForPECL(TraderFriendly::mathArcTangent($acosArray)));
    }

    /**
     * @throws \Exception
     */
    public function testAverageTrueRange(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_atr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::averageTrueRange($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAveragePrice(): void
    {
        $this->assertEquals(\trader_avgprice($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::averagePrice($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testBollingerBands(): void
    {
        $optInTimePeriod = 10;
        $optInNbDevUp    = 2.0;
        $optInNbDevDn    = 2.0;
        $optInMAType     = MovingAverageType::SMA->value;
        [$traderUpperBand, $traderMiddleBand, $traderLowerBand] = \trader_bbands($this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType);
        $Output = TraderFriendly::bollingerBands($this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType);
        $this->assertEquals($traderUpperBand, $this->adjustForPECL($Output['UpperBand']));
        $this->assertEquals($traderMiddleBand, $this->adjustForPECL($Output['MiddleBand']));
        $this->assertEquals($traderLowerBand, $this->adjustForPECL($Output['LowerBand']));
    }

    /**
     * @throws \Exception
     */
    public function testBetaVolatility(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_beta($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::betaVolatility($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testBalanceOfPower(): void
    {
        $this->assertEquals(\trader_bop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::balanceOfPower($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCommodityChannelIndex(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_cci($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::commodityChannelIndex($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleTwoCrows(): void
    {
        $this->assertEquals(\trader_cdl2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleTwoCrows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleThreeBlackCrows(): void
    {
        $this->assertEquals(\trader_cdl3blackcrows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleThreeBlackCrows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleThreeInsideUpDown(): void
    {
        $this->assertEquals(\trader_cdl3inside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleThreeInsideUpDown($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleThreeLineStrike(): void
    {
        $this->assertEquals(\trader_cdl3linestrike($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleThreeLineStrike($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleThreeOutsideUpDown(): void
    {
        $this->assertEquals(\trader_cdl3outside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleThreeOutsideUpDown($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleThreeStarsInTheSouth(): void
    {
        $this->assertEquals(\trader_cdl3starsinsouth($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleThreeStarsInTheSouth($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleThreeWhiteSoldiers(): void
    {
        $this->assertEquals(\trader_cdl3whitesoldiers($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleThreeWhiteSoldiers($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleAbandonedBaby(): void
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlabandonedbaby($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(TraderFriendly::candleAbandonedBaby($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleAdvanceBlock(): void
    {
        $this->assertEquals(\trader_cdladvanceblock($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleAdvanceBlock($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleBeltHold(): void
    {
        $this->assertEquals(\trader_cdlbelthold($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleBeltHold($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleBreakaway(): void
    {
        $this->assertEquals(\trader_cdlbreakaway($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleBreakaway($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleClosingMarubozu(): void
    {
        $this->assertEquals(\trader_cdlclosingmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleClosingMarubozu($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleConcealingBabySwallow(): void
    {
        $this->assertEquals(\trader_cdlconcealbabyswall($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleConcealingBabySwallow($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleCounterattack(): void
    {
        $this->assertEquals(\trader_cdlcounterattack($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleCounterattack($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleDarkCloudCover(): void
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdldarkcloudcover($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(TraderFriendly::candleDarkCloudCover($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleDoji(): void
    {
        $this->assertEquals(\trader_cdldoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleDoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleDojiStar(): void
    {
        $this->assertEquals(\trader_cdldojistar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleDojiStar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleDragonflyDoji(): void
    {
        $this->assertEquals(\trader_cdldragonflydoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleDragonflyDoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleEngulfingPattern(): void
    {
        $this->assertEquals(\trader_cdlengulfing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleEngulfingPattern($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleEveningDojiStar(): void
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdleveningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(TraderFriendly::candleEveningDojiStar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleEveningStar(): void
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdleveningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(TraderFriendly::candleEveningStar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleUpDownGapsSideBySideWhiteLines(): void
    {
        $this->assertEquals(\trader_cdlgapsidesidewhite($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleUpDownGapsSideBySideWhiteLines($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleGravestoneDoji(): void
    {
        $this->assertEquals(\trader_cdlgravestonedoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleGravestoneDoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleHammer(): void
    {
        $this->assertEquals(\trader_cdlhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleHammer($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleHangingMan(): void
    {
        $this->assertEquals(\trader_cdlhangingman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleHangingMan($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleHarami(): void
    {
        $this->assertEquals(\trader_cdlharami($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleHarami($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleHaramiCross(): void
    {
        $this->assertEquals(\trader_cdlharamicross($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleHaramiCross($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleHighWave(): void
    {
        $this->assertEquals(\trader_cdlhighwave($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleHighWave($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleHikkake(): void
    {
        $this->assertEquals(\trader_cdlhikkake($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleHikkake($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleModifiedHikkake(): void
    {
        $this->assertEquals(\trader_cdlhikkakemod($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleModifiedHikkake($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleHomingPigeon(): void
    {
        $this->assertEquals(\trader_cdlhomingpigeon($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleHomingPigeon($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleIdenticalThreeCrows(): void
    {
        $this->assertEquals(\trader_cdlidentical3crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleIdenticalThreeCrows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleInNeck(): void
    {
        $this->assertEquals(\trader_cdlinneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleInNeck($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleInvertedHammer(): void
    {
        $this->assertEquals(\trader_cdlinvertedhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleInvertedHammer($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleKicking(): void
    {
        $this->assertEquals(\trader_cdlkicking($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleKicking($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleKickingByLength(): void
    {
        $this->assertEquals(\trader_cdlkickingbylength($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleKickingByLength($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleLadderBottom(): void
    {
        $this->assertEquals(\trader_cdlladderbottom($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleLadderBottom($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleLongLeggedDoji(): void
    {
        $this->assertEquals(\trader_cdllongleggeddoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleLongLeggedDoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleLongLine(): void
    {
        $this->assertEquals(\trader_cdllongline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleLongLine($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleMarubozu(): void
    {
        $this->assertEquals(\trader_cdlmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleMarubozu($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleMatchingLow(): void
    {
        $this->assertEquals(\trader_cdlmatchinglow($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleMatchingLow($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleMatHold(): void
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmathold($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(TraderFriendly::candleMatHold($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleMorningDojiStar(): void
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmorningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(TraderFriendly::candleMorningDojiStar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleMorningStar(): void
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmorningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(TraderFriendly::candleMorningStar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleOnNeck(): void
    {
        $this->assertEquals(\trader_cdlonneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleOnNeck($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandlePiercing(): void
    {
        $this->assertEquals(\trader_cdlpiercing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candlePiercing($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleRickshawMan(): void
    {
        $this->assertEquals(\trader_cdlrickshawman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleRickshawMan($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleRisingFallingThreeMethods(): void
    {
        $this->assertEquals(\trader_cdlrisefall3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleRisingFallingThreeMethods($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleSeparatingLines(): void
    {
        $this->assertEquals(\trader_cdlseparatinglines($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleSeparatingLines($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleShootingStar(): void
    {
        $this->assertEquals(\trader_cdlshootingstar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleShootingStar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleShortLine(): void
    {
        $this->assertEquals(\trader_cdlshortline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleShortLine($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleSpinningTop(): void
    {
        $this->assertEquals(\trader_cdlspinningtop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleSpinningTop($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleStalled(): void
    {
        $this->assertEquals(\trader_cdlstalledpattern($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleStalled($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleStickSandwich(): void
    {
        $this->assertEquals(\trader_cdlsticksandwich($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleStickSandwich($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleTakuri(): void
    {
        $this->assertEquals(\trader_cdltakuri($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleTakuri($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleTasukiGap(): void
    {
        $this->assertEquals(\trader_cdltasukigap($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleTasukiGap($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleThrusting(): void
    {
        $this->assertEquals(\trader_cdlthrusting($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleThrusting($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleTristar(): void
    {
        $this->assertEquals(\trader_cdltristar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleTristar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleUniqueThreeRiver(): void
    {
        $this->assertEquals(\trader_cdlunique3river($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleUniqueThreeRiver($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleUpsideGapTwoCrows(): void
    {
        $this->assertEquals(\trader_cdlupsidegap2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleUpsideGapTwoCrows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCandleUpsideDownsideGapThreeMethods(): void
    {
        $this->assertEquals(\trader_cdlxsidegap3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::candleUpsideDownsideGapThreeMethods($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testMathCeiling(): void
    {
        $this->assertEquals(\trader_ceil($this->High), $this->adjustForPECL(TraderFriendly::mathCeiling($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testChandeMomentumOscillator(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_cmo($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::chandeMomentumOscillator($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testPearsonCorrelationCoefficient(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_correl($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::pearsonCorrelationCoefficient($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathCosine(): void
    {
        $this->assertEquals(\trader_cos($this->High), $this->adjustForPECL(TraderFriendly::mathCosine($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMathHyperbolicCosine(): void
    {
        $this->assertEquals(\trader_cosh($this->High), $this->adjustForPECL(TraderFriendly::mathHyperbolicCosine($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testDoubleExponentialMovingAverage(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_dema($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::doubleExponentialMovingAverage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathDivision(): void
    {
        $this->assertEquals(\trader_div($this->High, $this->Low), $this->adjustForPECL(TraderFriendly::mathDivision($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testDirectionalMovementIndex(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_dx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::directionalMovementIndex($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testExponentialMovingAverage(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_ema($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::exponentialMovingAverage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathExponent(): void
    {
        $this->assertEquals(\trader_exp($this->High), $this->adjustForPECL(TraderFriendly::mathExponent($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMathFloor(): void
    {
        $this->assertEquals(\trader_floor($this->High), $this->adjustForPECL(TraderFriendly::mathFloor($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHilbertTransformDominantCyclePeriod(): void
    {
        $this->assertEquals(\trader_ht_dcperiod($this->High), $this->adjustForPECL(TraderFriendly::hilbertTransformDominantCyclePeriod($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHilbertTransformDominantCyclePhase(): void
    {
        $this->assertEquals(\trader_ht_dcphase($this->High), $this->adjustForPECL(TraderFriendly::hilbertTransformDominantCyclePhase($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHilbertTransformPhasorComponents(): void
    {
        [$traderInPhase, $traderQuadrature] = \trader_ht_phasor($this->High);
        $Output = TraderFriendly::hilbertTransformPhasorComponents($this->High);
        $this->assertEquals($traderQuadrature, $this->adjustForPECL($Output['Quadrature']));
        $this->assertEquals($traderInPhase, $this->adjustForPECL($Output['InPhase']));
    }

    /**
     * @throws \Exception
     */
    public function testHilbertTransformSineWave(): void
    {
        [$traderSine, $traderLeadSine] = \trader_ht_sine($this->High);
        $Output = TraderFriendly::hilbertTransformSineWave($this->High);
        $this->assertEquals($traderLeadSine, $this->adjustForPECL($Output['LeadSine']));
        $this->assertEquals($traderSine, $this->adjustForPECL($Output['Sine']));
    }

    /**
     * @throws \Exception
     */
    public function testHilbertTransformInstantaneousTrendLine(): void
    {
        $this->assertEquals(\trader_ht_trendline($this->High), $this->adjustForPECL(TraderFriendly::hilbertTransformInstantaneousTrendLine($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHilbertTransformTrendVsCycleMode(): void
    {
        $this->assertEquals(\trader_ht_trendmode($this->High), $this->adjustForPECL(TraderFriendly::hilbertTransformTrendVsCycleMode($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testKaufmanAdaptiveMovingAverage(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_kama($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::kaufmanAdaptiveMovingAverage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegressionAngle(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_angle($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::linearRegressionAngle($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegressionIntercept(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_intercept($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::linearRegressionIntercept($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegressionSlope(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_slope($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::linearRegressionSlope($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegression(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::linearRegression($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathLogarithmNatural(): void
    {
        $this->assertEquals(\trader_ln($this->High), $this->adjustForPECL(TraderFriendly::mathLogarithmNatural($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMathLogarithmBase10(): void
    {
        $this->assertEquals(\trader_log10($this->High), $this->adjustForPECL(TraderFriendly::mathLogarithmBase10($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverage(): void
    {
        $optInTimePeriod = 10;
        $optInMAType     = MovingAverageType::SMA->value;
        $this->assertEquals(\trader_ma($this->High, $optInTimePeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::movingAverage($this->High, $optInTimePeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverageConvergenceDivergence(): void
    {
        $optInFastPeriod   = 3;
        $optInSlowPeriod   = 10;
        $optInSignalPeriod = 5;
        [$traderMACD, $traderMACDSignal, $traderMACDHist] = \trader_macd($this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod);
        $Output = TraderFriendly::movingAverageConvergenceDivergence($this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverageConvergenceDivergenceExtended(): void
    {
        $optInFastPeriod   = 3;
        $optInFastMAType   = MovingAverageType::SMA->value;
        $optInSlowPeriod   = 10;
        $optInSlowMAType   = MovingAverageType::SMA->value;
        $optInSignalPeriod = 5;
        $optInSignalMAType = MovingAverageType::SMA->value;
        [$traderMACD, $traderMACDSignal, $traderMACDHist] = \trader_macdext($this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType);
        $Output = TraderFriendly::movingAverageConvergenceDivergenceExtended($this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverageConvergenceDivergenceFixed(): void
    {
        $optInSignalPeriod = 5;
        [$traderMACD, $traderMACDSignal, $traderMACDHist] = \trader_macdfix($this->High, $optInSignalPeriod);
        $Output = TraderFriendly::movingAverageConvergenceDivergenceFixed($this->High, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMesaAdaptiveMovingAverage(): void
    {
        $optInFastLimit = 0.5;
        $optInSlowLimit = 0.05;
        [$traderMAMA, $traderFAMA] = \trader_mama($this->High, $optInFastLimit, $optInSlowLimit);
        $Output = TraderFriendly::mesaAdaptiveMovingAverage($this->High, $optInFastLimit, $optInSlowLimit);
        $this->assertEquals($traderMAMA, $this->adjustForPECL($Output['MAMA']));
        $this->assertEquals($traderFAMA, $this->adjustForPECL($Output['FAMA']));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverageVariablePeriod(): void
    {
        $inPeriods = array_pad(array(), count($this->High), 10);
        $optInMinPeriod = 2;
        $optInMaxPeriod = 20;
        $optInMAType    = MovingAverageType::SMA->value;
        $this->assertEquals(\trader_mavp($this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::movingAverageVariablePeriod($this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testMathMax(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_max($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::mathMax($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathMaxIndex(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_maxindex($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::mathMaxIndex($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathMedianPrice(): void
    {
        $this->assertEquals(\trader_medprice($this->High, $this->Low), $this->adjustForPECL(TraderFriendly::mathMedianPrice($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testMoneyFlowIndex(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_mfi($this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::moneyFlowIndex($this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMiddlePoint(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_midpoint($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::middlePoint($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMiddlePointPrice(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_midprice($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::middlePointPrice($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathMin(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_min($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::mathMin($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathMinIndex(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minindex($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::mathMinIndex($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathMinMax(): void
    {
        $outMin = array();
        $outMax          = array();
        $optInTimePeriod = 10;
        [$traderMin, $traderMax] = \trader_minmax($this->High, $optInTimePeriod);
        $Output = TraderFriendly::mathMinMax($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($Output['Min']));
        $this->assertEquals($traderMax, $this->adjustForPECL($Output['Max']));
    }

    /**
     * @throws \Exception
     */
    public function testMathMinMaxIndex(): void
    {
        $outMin = [];
        $outMax          = array();
        $optInTimePeriod = 10;
        [$traderMin, $traderMax] = \trader_minmaxindex($this->High, $optInTimePeriod);
        $Output = TraderFriendly::mathMinMaxIndex($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($Output['Min']));
        $this->assertEquals($traderMax, $this->adjustForPECL($Output['Max']));
    }

    /**
     * @throws \Exception
     */
    public function testMinusDirectionalIndicator(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::minusDirectionalIndicator($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMinusDirectionalMovement(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::minusDirectionalMovement($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMomentum(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_mom($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::momentum($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathMultiply(): void
    {
        $this->assertEquals(\trader_mult($this->Low, $this->High), $this->adjustForPECL(TraderFriendly::mathMultiply($this->Low, $this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testNormalizedAverageTrueRange(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_natr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::normalizedAverageTrueRange($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testOnBalanceVolume(): void
    {
        $this->assertEquals(\trader_obv($this->High, $this->Volume), $this->adjustForPECL(TraderFriendly::onBalanceVolume($this->High, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testPlusDirectionalIndicator(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_plus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::plusDirectionalIndicator($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testPlusDirectionalMovement(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_plus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::plusDirectionalMovement($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testPercentagePriceOscillator(): void
    {
        $optInFastPeriod = 10;
        $optInSlowPeriod = 10;
        $optInMAType     = MovingAverageType::SMA->value;
        $this->assertEquals(\trader_ppo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(TraderFriendly::percentagePriceOscillator($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testRateOfChange(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_roc($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::rateOfChange($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRateOfChangePercentage(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocp($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::rateOfChangePercentage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRateOfChangeRatio100(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocr100($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::rateOfChangeRatio100($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRateOfChangeRatio(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocr($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::rateOfChangeRatio($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRelativeStrengthIndex(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rsi($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::relativeStrengthIndex($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testParabolicSAR(): void
    {
        $optInAcceleration = 10;
        $optInMaximum      = 20;
        $this->assertEquals(\trader_sar($this->High, $this->Low, $optInAcceleration, $optInMaximum), $this->adjustForPECL(TraderFriendly::parabolicSAR($this->High, $this->Low, $optInAcceleration, $optInMaximum)));
    }

    /**
     * @throws \Exception
     */
    public function testParabolicSARExtended(): void
    {
        $optInStartValue = 0.0;
        $optInOffsetOnReverse       = 0.0;
        $optInAccelerationInitLong  = 2.0;
        $optInAccelerationLong      = 2.0;
        $optInAccelerationMaxLong   = 2.0;
        $optInAccelerationInitShort = 2.0;
        $optInAccelerationShort     = 2.0;
        $optInAccelerationMaxShort  = 2.0;
        $optInAccelerationMaxShort  = 2.0;
        $this->assertEquals(\trader_sarext($this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort), $this->adjustForPECL(TraderFriendly::parabolicSARExtended($this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort)));
    }

    /**
     * @throws \Exception
     */
    public function testMathSine(): void
    {
        $this->assertEquals(\trader_sin($this->High), $this->adjustForPECL(TraderFriendly::mathSine($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMathHyperbolicSine(): void
    {
        $this->assertEquals(\trader_sinh($this->High), $this->adjustForPECL(TraderFriendly::mathHyperbolicSine($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testSimpleMovingAverage(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_sma($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::simpleMovingAverage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMathSquareRoot(): void
    {
        $this->assertEquals(\trader_sqrt($this->High), $this->adjustForPECL(TraderFriendly::mathSquareRoot($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testStandardDeviation(): void
    {
        $optInTimePeriod = 10;
        $optInNbDev      = 1;
        $this->assertEquals(\trader_stddev($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL(TraderFriendly::standardDeviation($this->High, $optInTimePeriod, $optInNbDev)));
    }

    /**
     * @throws \Exception
     */
    public function testStochastic(): void
    {
        $optInFastK_Period = 2;
        $optInSlowK_Period = 10;
        $optInSlowK_MAType = MovingAverageType::SMA->value;
        $optInSlowD_Period = 20;
        $optInSlowD_MAType = MovingAverageType::SMA->value;
        [$traderSlowK, $traderSlowD] = \trader_stoch($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $Output = TraderFriendly::stochastic($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $this->assertEquals($traderSlowK, $this->adjustForPECL($Output['SlowK']));
        $this->assertEquals($traderSlowD, $this->adjustForPECL($Output['SlowD']));
    }

    /**
     * @throws \Exception
     */
    public function testStochasticFast(): void
    {
        $optInFastK_Period = 2;
        $optInFastD_Period = 10;
        $optInFastD_MAType = MovingAverageType::SMA->value;
        [$traderFastK, $traderFastD] = \trader_stochf($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $Output = TraderFriendly::stochasticFast($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $this->assertEquals($traderFastK, $this->adjustForPECL($Output['FastK']));
        $this->assertEquals($traderFastD, $this->adjustForPECL($Output['FastD']));
    }

    /**
     * @throws \Exception
     */
    public function testStochasticRelativeStrengthIndex(): void
    {
        $optInTimePeriod   = 10;
        $optInFastK_Period = 2;
        $optInFastD_Period = 10;
        $optInFastD_MAType = MovingAverageType::SMA->value;
        [$traderFastK, $traderFastD] = \trader_stochrsi($this->High, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $Output = TraderFriendly::stochasticRelativeStrengthIndex($this->High, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $this->assertEquals($traderFastK, $this->adjustForPECL($Output['FastK']));
        $this->assertEquals($traderFastD, $this->adjustForPECL($Output['FastD']));
    }

    /**
     * @throws \Exception
     */
    public function testMathSubtraction(): void
    {
        $this->assertEquals(\trader_sub($this->High, $this->Low), $this->adjustForPECL(TraderFriendly::mathSubtraction($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testMathSummation(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_sum($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::mathSummation($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTripleExponentialMovingAverageT3(): void
    {
        $optInTimePeriod = 10;
        $optInVFactor = 0.7;
        $this->assertEquals(\trader_t3($this->High, $optInTimePeriod, $optInVFactor), $this->adjustForPECL(TraderFriendly::tripleExponentialMovingAverageT3($this->High, $optInTimePeriod, $optInVFactor)));
    }

    /**
     * @throws \Exception
     */
    public function testMathTangent(): void
    {
        $this->assertEquals(\trader_tan($this->High), $this->adjustForPECL(TraderFriendly::mathTangent($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMathHyperbolicTangent(): void
    {
        $this->assertEquals(\trader_tanh($this->High), $this->adjustForPECL(TraderFriendly::mathHyperbolicTangent($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testTripleExponentialMovingAverage(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_tema($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::tripleExponentialMovingAverage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTrueRange(): void
    {
        $this->assertEquals(\trader_trange($this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::trueRange($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testTriangularMovingAverage(): void
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_trima($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::triangularMovingAverage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTripleExponentialAverage(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_trix($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::tripleExponentialAverage($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTimeSeriesForecast(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_tsf($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::timeSeriesForecast($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTypicalPrice(): void
    {
        $this->assertEquals(\trader_typprice($this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::typicalPrice($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testUltimateOscillator(): void
    {
        $optInTimePeriod1 = 10;
        $optInTimePeriod2 = 11;
        $optInTimePeriod3 = 12;
        $this->assertEquals(\trader_ultosc($this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3), $this->adjustForPECL(TraderFriendly::ultimateOscillator($this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3)));
    }

    /**
     * @throws \Exception
     */
    public function testVariance(): void
    {
        $optInTimePeriod = 10;
        $optInNbDev = 1.0;
        $this->assertEquals(\trader_var($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL(TraderFriendly::variance($this->High, $optInTimePeriod, $optInNbDev)));
    }

    /**
     * @throws \Exception
     */
    public function testWeightedClosePrice(): void
    {
        $this->assertEquals(\trader_wclprice($this->High, $this->Low, $this->Close), $this->adjustForPECL(TraderFriendly::weightedClosePrice($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testWilliamsR(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_willr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::williamsR($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testWeightedMovingAverage(): void
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_wma($this->High, $optInTimePeriod), $this->adjustForPECL(TraderFriendly::weightedMovingAverage($this->High, $optInTimePeriod)));
    }
}
