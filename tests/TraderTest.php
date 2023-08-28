<?php

namespace LupeCode\phpTraderNativeTest;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;
use LupeCode\phpTraderNative\Trader;
use PHPUnit\Framework\TestCase;

class TraderTest extends TestCase
{

    use TestingTrait;

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAddUnevenParametersError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ReturnCode::Messages[ReturnCode::UnevenParameters]);
        $this->expectExceptionCode(6);
        Trader::add([1, 2], [1, 2, 3]);
    }

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAddEmptyParametersError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ReturnCode::Messages[ReturnCode::OutOfRangeEndIndex]);
        $this->expectExceptionCode(3);
        Trader::add([], []);
    }

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAdxBadParameterError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ReturnCode::Messages[ReturnCode::BadParam]);
        $this->expectExceptionCode(1);
        Trader::adx([1], [1], [1], 0);
    }

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAdOscDefaultsDifferent()
    {
        $this->assertNotEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume), $this->adjustForPECL(Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testAcos()
    {
        $in = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_acos($in), $this->adjustForPECL(Trader::acos($in)));
    }

    /**
     * @throws \Exception
     */
    public function testAd()
    {
        $this->assertEquals(\trader_ad($this->High, $this->Low, $this->Close, $this->Volume), $this->adjustForPECL(Trader::ad($this->High, $this->Low, $this->Close, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testAdd()
    {
        $this->assertEquals(\trader_add($this->High, $this->Low), $this->adjustForPECL(Trader::add($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testAdOsc()
    {
        $optInFastPeriod = 3;
        $optInSlowPeriod = 10;
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL(Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod)));

        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL(Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAdx()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adx($this->High, $this->Low, $this->Close, $optInTimePeriod)));

        $optInTimePeriod = 20;
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adx($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAdxr()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adxr($this->High, $this->Low, $this->Close, $optInTimePeriod)));

        $optInTimePeriod = 20;
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adxr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testApo()
    {
        $optInMAType     = MovingAverageType::SMA;
        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInFastPeriod = 7;
        $optInSlowPeriod = 20;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::EMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::WMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::DEMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::TEMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::TRIMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::KAMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::MAMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::T3;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testAroon()
    {
        $optInTimePeriod = 10;
        list($traderAroonDown, $traderAroonUp) = \trader_aroon($this->High, $this->Low, $optInTimePeriod);
        $Output = Trader::aroon($this->High, $this->Low, $optInTimePeriod);
        $this->assertEquals($traderAroonDown, $this->adjustForPECL($Output['AroonDown']));
        $this->assertEquals($traderAroonUp, $this->adjustForPECL($Output['AroonUp']));
    }

    /**
     * @throws \Exception
     */
    public function testAroonOsc()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_aroonosc($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::aroonosc($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAsin()
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_asin($acosArray), $this->adjustForPECL(Trader::asin($acosArray)));
    }

    /**
     * @throws \Exception
     */
    public function testAtan()
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_atan($acosArray), $this->adjustForPECL(Trader::atan($acosArray)));
    }

    /**
     * @throws \Exception
     */
    public function testAtr()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_atr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::atr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAvgPrice()
    {
        $this->assertEquals(\trader_avgprice($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::avgprice($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testBbands()
    {
        $optInTimePeriod = 10;
        $optInNbDevUp    = 2.0;
        $optInNbDevDn    = 2.0;
        $optInMAType     = MovingAverageType::SMA;
        list($traderUpperBand, $traderMiddleBand, $traderLowerBand) = \trader_bbands($this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType);
        $Output = Trader::bbands($this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType);
        $this->assertEquals($traderUpperBand, $this->adjustForPECL($Output['UpperBand']));
        $this->assertEquals($traderMiddleBand, $this->adjustForPECL($Output['MiddleBand']));
        $this->assertEquals($traderLowerBand, $this->adjustForPECL($Output['LowerBand']));
    }

    /**
     * @throws \Exception
     */
    public function testBeta()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_beta($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::beta($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testBop()
    {
        $this->assertEquals(\trader_bop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::bop($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCci()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_cci($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::cci($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl2Crows()
    {
        $this->assertEquals(\trader_cdl2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl2crows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3BlackCrows()
    {
        $this->assertEquals(\trader_cdl3blackcrows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3blackcrows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3Inside()
    {
        $this->assertEquals(\trader_cdl3inside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3inside($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3LineStrike()
    {
        $this->assertEquals(\trader_cdl3linestrike($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3linestrike($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3Outside()
    {
        $this->assertEquals(\trader_cdl3outside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3outside($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3StarsInSouth()
    {
        $this->assertEquals(\trader_cdl3starsinsouth($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3starsinsouth($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3WhiteSoldiers()
    {
        $this->assertEquals(\trader_cdl3whitesoldiers($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3whitesoldiers($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlAbandonedBaby()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlabandonedbaby($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlabandonedbaby($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlAdvanceBlock()
    {
        $this->assertEquals(\trader_cdladvanceblock($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdladvanceblock($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlBeltHold()
    {
        $this->assertEquals(\trader_cdlbelthold($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlbelthold($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlBreakaway()
    {
        $this->assertEquals(\trader_cdlbreakaway($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlbreakaway($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlClosingMarubozu()
    {
        $this->assertEquals(\trader_cdlclosingmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlclosingmarubozu($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlConcealBabysWall()
    {
        $this->assertEquals(\trader_cdlconcealbabyswall($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlconcealbabyswall($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlCounterAttack()
    {
        $this->assertEquals(\trader_cdlcounterattack($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlcounterattack($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDarkCloudCover()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdldarkcloudcover($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdldarkcloudcover($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDoji()
    {
        $this->assertEquals(\trader_cdldoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdldoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDojiStar()
    {
        $this->assertEquals(\trader_cdldojistar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdldojistar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDragonflyDoji()
    {
        $this->assertEquals(\trader_cdldragonflydoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdldragonflydoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlEngulfing()
    {
        $this->assertEquals(\trader_cdlengulfing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlengulfing($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlEveningDojiStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdleveningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdleveningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlEveningStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdleveningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdleveningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlGapSideSideWhite()
    {
        $this->assertEquals(\trader_cdlgapsidesidewhite($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlgapsidesidewhite($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlGravestoneDoji()
    {
        $this->assertEquals(\trader_cdlgravestonedoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlgravestonedoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHammer()
    {
        $this->assertEquals(\trader_cdlhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhammer($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHangingMan()
    {
        $this->assertEquals(\trader_cdlhangingman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhangingman($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHarami()
    {
        $this->assertEquals(\trader_cdlharami($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlharami($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHaramiCross()
    {
        $this->assertEquals(\trader_cdlharamicross($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlharamicross($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHighWave()
    {
        $this->assertEquals(\trader_cdlhighwave($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhighwave($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHikkake()
    {
        $this->assertEquals(\trader_cdlhikkake($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhikkake($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHikkakeMod()
    {
        $this->assertEquals(\trader_cdlhikkakemod($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhikkakemod($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHomingPigeon()
    {
        $this->assertEquals(\trader_cdlhomingpigeon($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhomingpigeon($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlIdentical3Crows()
    {
        $this->assertEquals(\trader_cdlidentical3crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlidentical3crows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlInNeck()
    {
        $this->assertEquals(\trader_cdlinneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlinneck($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlInvertedHammer()
    {
        $this->assertEquals(\trader_cdlinvertedhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlinvertedhammer($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlKicking()
    {
        $this->assertEquals(\trader_cdlkicking($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlkicking($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlKickingByLength()
    {
        $this->assertEquals(\trader_cdlkickingbylength($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlkickingbylength($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlLadderBottom()
    {
        $this->assertEquals(\trader_cdlladderbottom($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlladderbottom($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlLongLeggedDoji()
    {
        $this->assertEquals(\trader_cdllongleggeddoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdllongleggeddoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlLongLine()
    {
        $this->assertEquals(\trader_cdllongline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdllongline($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMarubozu()
    {
        $this->assertEquals(\trader_cdlmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlmarubozu($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMatchingLow()
    {
        $this->assertEquals(\trader_cdlmatchinglow($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlmatchinglow($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMatHold()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmathold($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlmathold($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMorningDojiStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmorningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlmorningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMorningStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmorningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlmorningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlOnNeck()
    {
        $this->assertEquals(\trader_cdlonneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlonneck($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlPiercing()
    {
        $this->assertEquals(\trader_cdlpiercing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlpiercing($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlRickshawMan()
    {
        $this->assertEquals(\trader_cdlrickshawman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlrickshawman($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlRiseFall3Methods()
    {
        $this->assertEquals(\trader_cdlrisefall3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlrisefall3methods($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlSeparatingLines()
    {
        $this->assertEquals(\trader_cdlseparatinglines($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlseparatinglines($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlShootingStar()
    {
        $this->assertEquals(\trader_cdlshootingstar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlshootingstar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlShortLine()
    {
        $this->assertEquals(\trader_cdlshortline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlshortline($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlSpinningTop()
    {
        $this->assertEquals(\trader_cdlspinningtop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlspinningtop($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlStalledPattern()
    {
        $this->assertEquals(\trader_cdlstalledpattern($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlstalledpattern($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlStickSandwich()
    {
        $this->assertEquals(\trader_cdlsticksandwich($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlsticksandwich($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlTakuri()
    {
        $this->assertEquals(\trader_cdltakuri($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdltakuri($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlTasukiGap()
    {
        $this->assertEquals(\trader_cdltasukigap($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdltasukigap($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlThrusting()
    {
        $this->assertEquals(\trader_cdlthrusting($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlthrusting($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlTristar()
    {
        $this->assertEquals(\trader_cdltristar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdltristar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlUnique3River()
    {
        $this->assertEquals(\trader_cdlunique3river($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlunique3river($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlUpsideGap2Crows()
    {
        $this->assertEquals(\trader_cdlupsidegap2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlupsidegap2crows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlXSideGap3Methods()
    {
        $this->assertEquals(\trader_cdlxsidegap3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlxsidegap3methods($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCeil()
    {
        $this->assertEquals(\trader_ceil($this->High), $this->adjustForPECL(Trader::ceil($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testCmo()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_cmo($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::cmo($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testCorrel()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_correl($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::correl($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testCos()
    {
        $this->assertEquals(\trader_cos($this->High), $this->adjustForPECL(Trader::cos($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testCosh()
    {
        $this->assertEquals(\trader_cosh($this->High), $this->adjustForPECL(Trader::cosh($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testDema()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_dema($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::dema($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testDiv()
    {
        $this->assertEquals(\trader_div($this->High, $this->Low), $this->adjustForPECL(Trader::div($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testDx()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_dx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::dx($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testEma()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_ema($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::ema($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testExp()
    {
        $this->assertEquals(\trader_exp($this->High), $this->adjustForPECL(Trader::exp($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testFloor()
    {
        $this->assertEquals(\trader_floor($this->High), $this->adjustForPECL(Trader::floor($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtDcPeriod()
    {
        $this->assertEquals(\trader_ht_dcperiod($this->High), $this->adjustForPECL(Trader::ht_dcperiod($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtDcPhase()
    {
        $this->assertEquals(\trader_ht_dcphase($this->High), $this->adjustForPECL(Trader::ht_dcphase($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtPhasor()
    {
        list($traderInPhase, $traderQuadrature) = \trader_ht_phasor($this->High);
        $Output = Trader::ht_phasor($this->High);
        $this->assertEquals($traderQuadrature, $this->adjustForPECL($Output['Quadrature']));
        $this->assertEquals($traderInPhase, $this->adjustForPECL($Output['InPhase']));
    }

    /**
     * @throws \Exception
     */
    public function testHtSine()
    {
        list($traderSine, $traderLeadSine) = \trader_ht_sine($this->High);
        $Output = Trader::ht_sine($this->High);
        $this->assertEquals($traderLeadSine, $this->adjustForPECL($Output['LeadSine']));
        $this->assertEquals($traderSine, $this->adjustForPECL($Output['Sine']));
    }

    /**
     * @throws \Exception
     */
    public function testHtTrendline()
    {
        $this->assertEquals(\trader_ht_trendline($this->High), $this->adjustForPECL(Trader::ht_trendline($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtTrendMode()
    {
        $this->assertEquals(\trader_ht_trendmode($this->High), $this->adjustForPECL(Trader::ht_trendmode($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testKama()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_kama($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::kama($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearReg()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegAngle()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_angle($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg_angle($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegIntercept()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_intercept($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg_intercept($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegSlope()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_slope($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg_slope($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLn()
    {
        $this->assertEquals(\trader_ln($this->High), $this->adjustForPECL(Trader::ln($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testLog10()
    {
        $this->assertEquals(\trader_log10($this->High), $this->adjustForPECL(Trader::log10($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverage()
    {
        $optInTimePeriod = 10;
        $optInMAType     = MovingAverageType::SMA;
        $this->assertEquals(\trader_ma($this->High, $optInTimePeriod, $optInMAType), $this->adjustForPECL(Trader::ma($this->High, $optInTimePeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testMacd()
    {
        $optInFastPeriod   = 3;
        $optInSlowPeriod   = 10;
        $optInSignalPeriod = 5;
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macd($this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod);
        $Output = Trader::macd($this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMacdExt()
    {
        $optInFastPeriod   = 3;
        $optInFastMAType   = MovingAverageType::SMA;
        $optInSlowPeriod   = 10;
        $optInSlowMAType   = MovingAverageType::SMA;
        $optInSignalPeriod = 5;
        $optInSignalMAType = MovingAverageType::SMA;
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macdext($this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType);
        $Output = Trader::macdext($this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMacdFix()
    {
        $optInSignalPeriod = 5;
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macdfix($this->High, $optInSignalPeriod);
        $Output = Trader::macdfix($this->High, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMama()
    {
        $optInFastLimit = 0.5;
        $optInSlowLimit = 0.05;
        list($traderMAMA, $traderFAMA) = \trader_mama($this->High, $optInFastLimit, $optInSlowLimit);
        $Output = Trader::mama($this->High, $optInFastLimit, $optInSlowLimit);
        $this->assertEquals($traderMAMA, $this->adjustForPECL($Output['MAMA']));
        $this->assertEquals($traderFAMA, $this->adjustForPECL($Output['FAMA']));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverageVariablePeriod()
    {
        $inPeriods      = array_pad(array(), count($this->High), 10);
        $optInMinPeriod = 2;
        $optInMaxPeriod = 20;
        $optInMAType    = MovingAverageType::SMA;
        $this->assertEquals(\trader_mavp($this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType), $this->adjustForPECL(Trader::mavp($this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testMax()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_max($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::max($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMaxIndex()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_maxindex($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::maxindex($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMedPrice()
    {
        $this->assertEquals(\trader_medprice($this->High, $this->Low), $this->adjustForPECL(Trader::medprice($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testMfi()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_mfi($this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod), $this->adjustForPECL(Trader::mfi($this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMidPoint()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_midpoint($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::midpoint($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMidPrice()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_midprice($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::midprice($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMin()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_min($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::min($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMinIndex()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minindex($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::minindex($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMinMax()
    {
        $outMin          = array();
        $outMax          = array();
        $optInTimePeriod = 10;
        list($traderMin, $traderMax) = \trader_minmax($this->High, $optInTimePeriod);
        $Output = Trader::minmax($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($Output['Min']));
        $this->assertEquals($traderMax, $this->adjustForPECL($Output['Max']));
    }

    /**
     * @throws \Exception
     */
    public function testMinMaxIndex()
    {
        $outMin          = array();
        $outMax          = array();
        $optInTimePeriod = 10;
        list($traderMin, $traderMax) = \trader_minmaxindex($this->High, $optInTimePeriod);
        $Output = Trader::minmaxindex($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($Output['Min']));
        $this->assertEquals($traderMax, $this->adjustForPECL($Output['Max']));
    }

    /**
     * @throws \Exception
     */
    public function testMinusDI()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::minus_di($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMinusDM()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::minus_dm($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMom()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_mom($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::mom($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMult()
    {
        $this->assertEquals(\trader_mult($this->Low, $this->High), $this->adjustForPECL(Trader::mult($this->Low, $this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testNatr()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_natr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::natr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testObv()
    {
        $this->assertEquals(\trader_obv($this->High, $this->Volume), $this->adjustForPECL(Trader::obv($this->High, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testPlusDI()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_plus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::plus_di($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testPlusDM()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_plus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::plus_dm($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testPpo()
    {
        $optInFastPeriod = 10;
        $optInSlowPeriod = 10;
        $optInMAType     = MovingAverageType::SMA;
        $this->assertEquals(\trader_ppo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::ppo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testRoc()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_roc($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::roc($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRocP()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocp($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rocp($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRocR()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocr($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rocr($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRocR100()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocr100($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rocr100($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRsi()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rsi($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rsi($this->High, $optInTimePeriod)));
        // A test with real numbers.
        $expected = [
            '42.210139', '45.101929', '43.790895', '44.024530', '44.766276', '42.444434', '48.000712', '43.387497', '50.151878', '54.875765', '46.476423', '40.575072', '45.539758', '47.613588', '44.386741', '41.223737', '49.616844', '50.889708', '53.821251', '50.052066', '48.971100', '53.314404', '52.217529', '51.085649', '49.005748', '44.873849', '50.348316', '39.891732', '44.278588', '47.994485', '46.817654', '49.933871', '55.694793', '53.736302', '55.014014', '55.280020', '58.940617', '50.285986', '44.761536', '43.012268', '51.303298', '54.053435', '49.372593', '46.975564', '44.794978', '49.214314', '41.650776', '46.482220', '49.008324', '47.793580', '47.708699', '47.436434', '48.491563', '36.262877', '39.824684', '34.143726', '43.235154', '38.818494', '41.561516', '38.864092', '35.321889', '31.357898', '34.076185', '37.996955', '35.560439', '38.337523', '35.584957', '40.802948', '40.983594', '36.380568', '35.272427', '36.316932', '32.030886', '28.586394', '32.545789', '28.259153', '26.918714', '34.881916', '32.247423', '30.193482', '27.600608', '27.240716', '22.102399', '19.782925', '18.204042', '24.587512', '23.178912', '31.855723', '42.339904', '37.422332', '40.893819', '46.083348', '45.834278', '44.944830', '53.468048', '49.635845', '48.870488', '50.055282', '50.117523', '43.066245', '44.647382', '50.214816', '46.906383', '49.528304', '56.326269', '54.291897', '54.689855', '53.683162', '56.515458', '60.476269', '59.476124', '62.636911', '67.848693', '70.395688', '68.359165', '69.524440', '70.769607', '70.989704', '59.024694', '59.783842', '59.600880', '63.832334', '57.774446', '52.713437', '55.490157', '61.795305', '64.242416', '69.907488', '58.102394', '60.497014', '57.631898', '63.215569', '59.246658', '52.454033', '48.044407', '46.833006', '46.727265', '48.722093', '48.175699', '47.287333', '49.780555', '47.565390', '48.491029', '43.157875', '47.539249', '50.666455', '57.263833', '51.664946', '55.488415', '60.241130', '60.657133', '59.566447', '61.074004', '62.852393', '59.814512', '57.298068', '52.296049', '57.043103', '44.072413', '41.426100', '34.892326', '40.690291', '49.369603', '48.149120', '50.333699', '43.933833', '43.254177', '45.084004', '46.086954', '44.009508', '40.919572', '38.428973', '35.881079', '35.968983', '27.209610', '29.989026', '28.908348', '28.648929', '32.822456', '38.266650', '37.285247', '45.863047', '47.426056', '50.371855', '45.828722', '51.311700', '50.150082', '41.665223', '48.062853', '50.556842', '52.126783', '46.337265', '56.251859', '55.035151', '54.152187', '54.985480', '51.491515', '59.129101', '61.036646', '61.226782', '54.752799', '57.545952', '38.204380', '56.383780', '59.714488', '62.073404', '62.363481', '63.616679', '61.599866', '58.104685', '54.546423', '62.264369', '62.626982', '60.929376', '63.430451', '63.323977', '59.505277', '59.726294', '62.956195', '62.591746', '64.155933', '62.286086', '64.920742', '69.298878', '67.304416', '62.272428', '66.154737', '67.438507', '62.028564', '58.873861', '57.649489', '58.337041', '55.561625', '56.123615', '55.221124', '48.075094', '54.480397', '57.315600', '59.901817', '60.921764',
        ];
        $result   = Trader::rsi($this->Open);
        $count    = count($expected);
        $actual = [];
        for ($i = 0; $i < $count; $i++) {
            $expected[$i] = round((float)$expected[$i], 6);
            $actual[$i]   = round($result[$i + 14], 6);
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \Exception
     */
    public function testSar()
    {
        $optInAcceleration = 10;
        $optInMaximum      = 20;
        $this->assertEquals(\trader_sar($this->High, $this->Low, $optInAcceleration, $optInMaximum), $this->adjustForPECL(Trader::sar($this->High, $this->Low, $optInAcceleration, $optInMaximum)));
    }

    /**
     * @throws \Exception
     */
    public function testSarExt()
    {
        $optInStartValue            = 0.0;
        $optInOffsetOnReverse       = 0.0;
        $optInAccelerationInitLong  = 2.0;
        $optInAccelerationLong      = 2.0;
        $optInAccelerationMaxLong   = 2.0;
        $optInAccelerationInitShort = 2.0;
        $optInAccelerationShort     = 2.0;
        $optInAccelerationMaxShort  = 2.0;
        $this->assertEquals(\trader_sarext($this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort), $this->adjustForPECL(Trader::sarext($this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort)));
    }

    /**
     * @throws \Exception
     */
    public function testSin()
    {
        $this->assertEquals(\trader_sin($this->High), $this->adjustForPECL(Trader::sin($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testSinh()
    {
        $this->assertEquals(\trader_sinh($this->High), $this->adjustForPECL(Trader::sinh($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testSma()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_sma($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::sma($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testSqrt()
    {
        $this->assertEquals(\trader_sqrt($this->High), $this->adjustForPECL(Trader::sqrt($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testStdDev()
    {
        $optInTimePeriod = 10;
        $optInNbDev      = 1;
        $this->assertEquals(\trader_stddev($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL(Trader::stddev($this->High, $optInTimePeriod, $optInNbDev)));
    }

    /**
     * @throws \Exception
     */
    public function testStoch()
    {
        $optInFastK_Period = 2;
        $optInSlowK_Period = 10;
        $optInSlowK_MAType = MovingAverageType::SMA;
        $optInSlowD_Period = 20;
        $optInSlowD_MAType = MovingAverageType::SMA;
        list($traderSlowK, $traderSlowD) = \trader_stoch($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $Output = Trader::stoch($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $this->assertEquals($traderSlowK, $this->adjustForPECL($Output['SlowK']));
        $this->assertEquals($traderSlowD, $this->adjustForPECL($Output['SlowD']));
    }

    /**
     * @throws \Exception
     */
    public function testStochF()
    {
        $optInFastK_Period = 2;
        $optInFastD_Period = 10;
        $optInFastD_MAType = MovingAverageType::SMA;
        list($traderFastK, $traderFastD) = \trader_stochf($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $Output = Trader::stochf($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $this->assertEquals($traderFastK, $this->adjustForPECL($Output['FastK']));
        $this->assertEquals($traderFastD, $this->adjustForPECL($Output['FastD']));
        //Test with real data
        $expectedK = [
            15 => '0.652015751', '0.842490476', '0.703297436', '0.378698449', '0.477942296', '0.13740437', '0.557254111', '0.174244927', '0.915661047', '0.843372667', '0.343282245', '0.045976611', '0.789270962', '0.455938139', '0.394635481', '0.593869121', '0.678161043', '0.689654525', '0.770114264', '0.386972649', '0.597700154', '0.727968303', '0.817426556', '0.737068534', '0.758620259', '0.554972775', '0.126828355', '0.13170543', '0.385364578', '0.419510449', '0.414633373', '0.590243703', '0.724867579', '0.788359147', '0.740425311', '0.810923711', '0.85714309', '0.240816621', '0.31836692', '0.314284466', '0.558440743', '0.619048155', '0.337662197', '0.259740052', '0.175097665', '0.214007393', '0.40466965', '0.307393385', '0.544747471', '0.334630739', '0.632979451', '0.500000798', '0.526104798', '0.211805829', '0.260504482', '0.243697199', '0.414565826', '0.431372829', '0.347338375', '0.112947627', '0.224444', '0.057777333', '0.135514518', '0.218468743', '0.140655133', '0.327188479', '0.209476414', '0.466334148', '0.441396978', '0.197007829', '0.20448888', '0.239747154', '0.148148765', '0.047468957', '0.270531577', '0.25109203', '0.189956467', '0.299126725', '0.072649573', '0.006012222', '0.058271031', '0.223706214', '0.049080039', '0.086036647', '0.141791087', '0.106180682', '0.156521565', '0.347992227', '0.573613876', '0.292543077', '0.57509873', '0.838235294', '0.823529412', '0.580357272', '0.790178971', '0.854910905', '0.810268261', '0.787951997', '0.681927634', '0.554217242', '0.78078837', '0.633004836', '0.923645548', '0.536193173', '0.871313907', '0.731903413', '0.778048293', '0.66341439', '0.940239219', '0.948207549', '0.788844737', '0.931199818', '0.898484545', '0.976703038', '0.897849426', '0.895522274', '0.946395399', '0.889999444', '0.645999942', '0.622362818', '0.680084458', '0.733332722', '0.524999563', '0.40277772', '0.276679909', '0.956521395', '0.603306445', '0.727272251', '0.680440859', '0.743801723', '0.586776973', '0.822967225', '0.358851675', '0.382775598', '0.174641148', '0.227272967', '0.179425837', '0.232911899', '0.169620759', '0.048101772', '0.162162202', '0.132678411', '0.194379859', '0.016393443', '0.259953396', '0.597157063', '0.886256926', '0.739336616', '0.675324886', '0.943396226', '0.92767327', '0.807453787', '0.937500019', '0.977653085', '0.818435297', '0.85754142', '0.603399204', '0.148148834', '0.268785049', '0.102450066', '0.023454366', '0.388060045', '0.530916731', '0.503197974', '0.52665234', '0.230277136', '0.283582029', '0.414798337', '0.338564947', '0.304932443', '0.301754631', '0.093749658', '0.142450102', '0.419889241', '0.088397774', '0.088397774', '0.068219623', '0.063592836', '0.331614834', '0.367697486', '0.43642602', '0.58287799', '0.749056556', '0.739039611', '0.821585864', '0.919037181', '0.840262766', '0.682713278', '0.925601734', '0.864332574', '0.939130626', '0.515075118', '0.685929304', '0.915204436', '0.690058076', '0.640350503', '0.79050291', '0.896634375', '0.981651147', '0.713302752', '0.461538462', '0.48427652', '0.782868526', '0.857142377', '0.92803611', '0.930458961', '0.980662436', '0.985034554', '0.91064377', '0.721419353', '0.904761776', '0.937859947', '0.882623577', '0.858457981', '0.94718725', '0.877083308', '0.735751364', '0.730569759', '0.924870227', '0.938574939', '0.950860688', '0.894618681', '0.87259974', '0.859259225', '0.851240373', '0.967568073', '0.880435087', '0.91689753', '0.70360119', '0.556787103', '0.598214703', '0.54981476', '0.239851661', '0.125461624', '0.095395034', '0.485293815', '0.597058881', '0.823529602', '0.808824005',
        ];
        $expectedD = [
            15 => '0.649246928', '0.67155116', '0.732601221', '0.641495454', '0.519979393', '0.331348372', '0.390866926', '0.289634469', '0.549053361', '0.644426213', '0.700771986', '0.410877174', '0.392843273', '0.430395237', '0.546614861', '0.481480914', '0.555555215', '0.653894896', '0.712643277', '0.615580479', '0.584929022', '0.570880369', '0.714365005', '0.760821131', '0.77103845', '0.683553856', '0.480140463', '0.271168853', '0.214632788', '0.312193486', '0.4065028', '0.474795841', '0.576581552', '0.70115681', '0.751217346', '0.779902723', '0.802830704', '0.636294474', '0.472108877', '0.291156003', '0.39703071', '0.497257788', '0.505050365', '0.405483468', '0.257499971', '0.216281703', '0.264591569', '0.308690143', '0.418936835', '0.395590532', '0.50411922', '0.489203663', '0.553028349', '0.412637142', '0.332805036', '0.23866917', '0.306255836', '0.363211951', '0.39775901', '0.297219611', '0.228243334', '0.131722987', '0.139245284', '0.137253531', '0.164879465', '0.228770785', '0.225773342', '0.334333014', '0.372402513', '0.368246318', '0.280964562', '0.213747954', '0.1974616', '0.145121626', '0.1553831', '0.189697522', '0.237193358', '0.246725074', '0.187244255', '0.125929507', '0.045644275', '0.095996489', '0.110352428', '0.119607633', '0.092302591', '0.111336139', '0.134831112', '0.203564825', '0.35937589', '0.404716394', '0.480418561', '0.5686257', '0.745621145', '0.747373993', '0.731355218', '0.741815716', '0.818452712', '0.817710388', '0.760049298', '0.674698958', '0.672311082', '0.656003482', '0.779146251', '0.697614519', '0.777050876', '0.713136831', '0.793755204', '0.724455365', '0.793900634', '0.850620386', '0.892430502', '0.889417368', '0.872843033', '0.935462467', '0.92434567', '0.923358246', '0.9132557', '0.910639039', '0.827464928', '0.719454068', '0.649482406', '0.678593332', '0.646138914', '0.553703335', '0.40148573', '0.545326341', '0.61216925', '0.762366697', '0.670339852', '0.717171611', '0.670339852', '0.717848641', '0.589531958', '0.521531499', '0.305422807', '0.261563238', '0.193779984', '0.213203568', '0.193986165', '0.150211477', '0.126628245', '0.114314128', '0.163073491', '0.114483904', '0.156908899', '0.291167967', '0.581122462', '0.740916868', '0.76697281', '0.786019243', '0.848798128', '0.892841094', '0.890875692', '0.90753563', '0.911196134', '0.884543267', '0.759791974', '0.536363153', '0.340111029', '0.173127983', '0.13156316', '0.171321492', '0.314143714', '0.47405825', '0.520255682', '0.420042483', '0.346837168', '0.309552501', '0.345648438', '0.352765242', '0.315084007', '0.233478911', '0.17931813', '0.218696334', '0.216912372', '0.19889493', '0.081671723', '0.073403411', '0.154475764', '0.254301718', '0.378579446', '0.462333832', '0.589453522', '0.690324719', '0.769894011', '0.826554219', '0.86029527', '0.814004409', '0.816192593', '0.824215862', '0.909688311', '0.772846106', '0.713378349', '0.705402952', '0.763730605', '0.748537671', '0.706970496', '0.775829263', '0.889596144', '0.863862758', '0.718830787', '0.553039245', '0.576227836', '0.708095808', '0.856015671', '0.905212483', '0.946385836', '0.965385317', '0.958780253', '0.872365892', '0.8456083', '0.854680359', '0.9084151', '0.892980502', '0.896089603', '0.894242846', '0.85334064', '0.78113481', '0.797063783', '0.864671641', '0.938101951', '0.928018102', '0.90602637', '0.875492548', '0.861033112', '0.892689223', '0.899747844', '0.921633563', '0.833644602', '0.725761941', '0.619534332', '0.568272189', '0.462627041', '0.305042681', '0.15356944', '0.235383491', '0.392582577', '0.635294099', '0.743137496',
        ];
        $Output    = Trader::stochf($this->High, $this->Low, $this->Close, 14, 3, $optInFastD_MAType);
        foreach ($expectedK as &$k) {
            $k = round((float)$k * 100, 4);
        }
        unset($k);
        foreach ($expectedD as &$d) {
            $d = round((float)$d * 100, 4);
        }
        unset($d);
        foreach ($Output['FastK'] as &$k) {
            $k = round((float)$k, 4);
        }
        unset($k);
        foreach ($Output['FastD'] as &$d) {
            $d = round((float)$d, 4);
        }
        unset($d);
        $this->assertEquals($expectedK, $Output['FastK']);
        $this->assertEquals($expectedD, $Output['FastD']);
    }

    /**
     * @throws \Exception
     */
    public function testStochRsi()
    {
        $optInTimePeriod   = 14;
        $optInFastK_Period = 3;
        $optInFastD_Period = 3;
        $optInFastD_MAType = MovingAverageType::SMA;
        for ($optInFastD_MAType = MovingAverageType::SMA; $optInFastD_MAType < MovingAverageType::MAMA; $optInFastD_MAType++) {
            list($traderFastK, $traderFastD) = \trader_stochrsi($this->Close, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
            $Output = Trader::stochrsi($this->Close, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
            $this->assertEquals($traderFastK, $this->adjustForPECL($Output['FastK']));
            $this->assertEquals($traderFastD, $this->adjustForPECL($Output['FastD']));
        }
        $expectedK = [
            29 => '0.480917', '0.695296', '0.785200', '0.798026', '0.891792', '0.452034', '0.708867', '0.861670', '0.952319', '0.853071', '0.882393', '0.407726', '0.000000', '0.008825', '0.463480', '0.523509', '0.515471', '0.847087', '0.991448', '1.000000', '1.000000', '1.000000', '1.000000', '0.108854', '0.222072', '0.216946', '0.603107', '0.642376', '0.228447', '0.123087', '0.000000', '0.069023', '0.392579', '0.249540', '0.622430', '0.313617', '0.750104', '0.538920', '0.340564', '0.000000', '0.000000', '0.000000', '0.397416', '0.434583', '0.298908', '0.000000', '0.000000', '0.000000', '0.176459', '0.300383', '0.060773', '0.493913', '0.389306', '1.000000', '0.957764', '0.577813', '0.596454', '0.554263', '0.203775', '0.077795', '0.074767', '0.000000', '0.000000', '0.348542', '0.042476', '0.000000', '0.000000', '0.000000', '0.000000', '0.000000', '0.191255', '0.194804', '0.367652', '0.997927', '1.000000', '0.728093', '1.000000', '1.000000', '0.989601', '0.860609', '1.000000', '1.000000', '0.964930', '0.955481', '0.865731', '0.741097', '0.966753', '0.723172', '1.000000', '0.500008', '1.000000', '0.747116', '0.947746', '0.708572', '1.000000', '1.000000', '0.721643', '1.000000', '1.000000', '1.000000', '0.853966', '0.944138', '1.000000', '0.871601', '0.322003', '0.313558', '0.409465', '0.417419', '0.000000', '0.000000', '0.131672', '0.728542', '0.614362', '0.756610', '0.665143', '0.743827', '0.490861', '1.000000', '0.000000', '0.040796', '0.000000', '0.075056', '0.007756', '0.154641', '0.062128', '0.000000', '0.130378', '0.088154', '0.124746', '0.000000', '0.357903', '0.922174', '1.000000', '0.809910', '1.000000', '1.000000', '0.976883', '0.812586', '0.985454', '1.000000', '0.745403', '0.788417', '0.421697', '0.000000', '0.000000', '0.000000', '0.000000', '0.409048', '0.539294', '0.514718', '0.537394', '0.283618', '0.339388', '0.529425', '0.454367', '0.593838', '0.492812', '0.207696', '0.175233', '0.132231', '0.000000', '0.000000', '0.000000', '0.000000', '0.551434', '0.617465', '0.743290', '1.000000', '1.000000', '0.904131', '0.949975', '1.000000', '0.914652', '0.755448', '1.000000', '0.937092', '1.000000', '0.747732', '1.000000', '1.000000', '0.605043', '0.521316', '0.802793', '1.000000', '1.000000', '0.542553', '0.280949', '0.000000', '0.558297', '0.880759', '1.000000', '1.000000', '1.000000', '1.000000', '0.888666', '0.428329', '0.724647', '0.903601', '0.761714', '0.699136', '0.837368', '0.654066', '0.396014', '0.384273', '0.657498', '0.744693', '0.762810', '0.972600', '1.000000', '1.000000', '1.000000', '1.000000', '0.839187', '0.886515', '0.242733', '0.000000', '0.104823', '0.160150', '0.000000', '0.000000', '0.000000', '0.288984', '0.384690', '0.560697', '0.542329',
        ];
        $expectedD = [
            29 => '0.6229282', '0.5709972', '0.6538047', '0.7595077', '0.8250061', '0.7139507', '0.6842311', '0.6741906', '0.8409521', '0.8890200', '0.8959277', '0.7143967', '0.4300396', '0.1388503', '0.1574352', '0.3319381', '0.5008200', '0.6286889', '0.7846686', '0.9461782', '0.9971492', '1.0000000', '1.0000000', '0.7029513', '0.4436419', '0.1826238', '0.3473748', '0.4874762', '0.4913099', '0.3313032', '0.1171779', '0.0640365', '0.1538672', '0.2370474', '0.4215166', '0.3951960', '0.5620503', '0.5342138', '0.5431959', '0.2931613', '0.1135212', '0.0000000', '0.1324722', '0.2773330', '0.3769691', '0.2444969', '0.0996360', '0.0000000', '0.0588198', '0.1589473', '0.1792050', '0.2850229', '0.3146640', '0.6277396', '0.7823567', '0.8451925', '0.7106771', '0.5761766', '0.4514972', '0.2786109', '0.1187788', '0.0508538', '0.0249222', '0.1161807', '0.1303394', '0.1303394', '0.0141587', '0.0000000', '0.0000000', '0.0000000', '0.0637517', '0.1286864', '0.2512370', '0.5201277', '0.7885263', '0.9086734', '0.9093644', '0.9093644', '0.9965338', '0.9500702', '0.9500702', '0.9535364', '0.9883100', '0.9734704', '0.9287141', '0.8541032', '0.8578604', '0.8103408', '0.8966417', '0.7410601', '0.8333360', '0.7490415', '0.8982874', '0.8011447', '0.8854392', '0.9028572', '0.9072144', '0.9072144', '0.9072144', '1.0000000', '0.9513220', '0.9327014', '0.9327014', '0.9385798', '0.7312015', '0.5023876', '0.3483423', '0.3801475', '0.2756281', '0.1391397', '0.0438907', '0.2867379', '0.4915253', '0.6998378', '0.6787050', '0.7218600', '0.6332770', '0.7448959', '0.4969535', '0.3469319', '0.0135986', '0.0386171', '0.0276039', '0.0791511', '0.0748418', '0.0722564', '0.0641685', '0.0728440', '0.1144260', '0.0709667', '0.1608830', '0.4266924', '0.7600257', '0.9106946', '0.9366365', '0.9366365', '0.9922943', '0.9298231', '0.9249744', '0.9326802', '0.9102857', '0.8446065', '0.6518387', '0.4033711', '0.1405655', '-0.0000000', '-0.0000000', '0.1363494', '0.3161141', '0.4876867', '0.5304684', '0.4452432', '0.3868000', '0.3841439', '0.4410602', '0.5258768', '0.5136725', '0.4314486', '0.2919135', '0.1717197', '0.1024879', '0.0440770', '-0.0000000', '-0.0000000', '0.1838112', '0.3896330', '0.6373964', '0.7869185', '0.9144301', '0.9680437', '0.9513688', '0.9513688', '0.9548759', '0.8900334', '0.8900334', '0.8975135', '0.9790308', '0.8949416', '0.9159108', '0.9159108', '0.8683476', '0.7087864', '0.6430508', '0.7747031', '0.9342643', '0.8475175', '0.6078340', '0.2745007', '0.2797488', '0.4796852', '0.8130185', '0.9602529', '1.0000000', '1.0000000', '0.9628887', '0.7723318', '0.6805476', '0.6855260', '0.7966543', '0.7881506', '0.7660728', '0.7301900', '0.6291492', '0.4781174', '0.4792615', '0.5954879', '0.7216668', '0.8267009', '0.9118032', '0.9908667', '1.0000000', '1.0000000', '0.9463956', '0.9085673', '0.6561450', '0.3764161', '0.1158520', '0.0883243', '0.0883243', '0.0533834', '-0.0000000', '0.0963281', '0.2245580', '0.4114569', '0.4959053',
        ];
        $Output    = Trader::stochrsi($this->Close, 14, 14);
        foreach ($expectedK as &$k) {
            $k = round((float)$k * 100, 4);
        }
        unset($k);
        foreach ($expectedD as &$d) {
            $d = round((float)$d * 100, 4);
        }
        unset($d);
        foreach ($Output['FastK'] as &$k) {
            $k = round((float)$k, 4);
        }
        unset($k);
        foreach ($Output['FastD'] as &$d) {
            $d = round((float)$d, 4);
        }
        unset($d);
        $this->assertEquals($expectedK, $Output['FastK']);
        $this->assertEquals($expectedD, $Output['FastD']);
    }

    /**
     * @throws \Exception
     */
    public function testStochRstVsStochAndRsi()
    {
        $rsi      = Trader::rsi($this->Close);
        $stoch    = Trader::stochf($rsi, $rsi, $rsi, 14);
        $stochRsi = Trader::stochrsi($this->Close, 14, 14);
        $this->assertEquals(array_values($stoch['FastK']), array_values($stochRsi['FastK']));
        $this->assertEquals(array_values($stoch['FastD']), array_values($stochRsi['FastD']));
    }

    /**
     * @throws \Exception
     */
    public function testSub()
    {
        $this->assertEquals(\trader_sub($this->High, $this->Low), $this->adjustForPECL(Trader::sub($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testSum()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_sum($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::sum($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testT3()
    {
        $optInTimePeriod = 10;
        $optInVFactor    = 0.7;
        $this->assertEquals(\trader_t3($this->High, $optInTimePeriod, $optInVFactor), $this->adjustForPECL(Trader::t3($this->High, $optInTimePeriod, $optInVFactor)));
    }

    /**
     * @throws \Exception
     */
    public function testTan()
    {
        $this->assertEquals(\trader_tan($this->High), $this->adjustForPECL(Trader::tan($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testTanh()
    {
        $this->assertEquals(\trader_tanh($this->High), $this->adjustForPECL(Trader::tanh($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testTema()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_tema($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::tema($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTrueRange()
    {
        $this->assertEquals(\trader_trange($this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::trange($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testTrima()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_trima($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::trima($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTrix()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_trix($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::trix($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTsf()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_tsf($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::tsf($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTypPrice()
    {
        $this->assertEquals(\trader_typprice($this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::typprice($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testUltOsc()
    {
        $optInTimePeriod1 = 10;
        $optInTimePeriod2 = 11;
        $optInTimePeriod3 = 12;
        $this->assertEquals(\trader_ultosc($this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3), $this->adjustForPECL(Trader::ultosc($this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3)));
    }

    /**
     * @throws \Exception
     */
    public function testVariance()
    {
        $optInTimePeriod = 10;
        $optInNbDev      = 1.0;
        $this->assertEquals(\trader_var($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL(Trader::var($this->High, $optInTimePeriod, $optInNbDev)));
    }

    /**
     * @throws \Exception
     */
    public function testWclPrice()
    {
        $this->assertEquals(\trader_wclprice($this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::wclprice($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testWillR()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_willr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::willr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testWma()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_wma($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::wma($this->High, $optInTimePeriod)));
    }
}
