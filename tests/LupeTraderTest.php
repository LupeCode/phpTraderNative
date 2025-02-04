<?php

namespace LupeCode\phpTraderNativeTest;

use LupeCode\phpTraderNative\LupeTrader;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\Trader;
use LupeCode\phpTraderNativeTest\TraderTest;
use PHPUnit\Framework\TestCase;

class LupeTraderTest extends TestCase
{

    use TestingTrait;

    public function testAcos(): void
    {
        $in = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_acos($in), $this->adjustForPECL(LupeTrader::acos($in)));
    }

    public function testAd(): void
    {
        $Output        = LupeTrader::ad($this->High, $this->Low, $this->Close, $this->Volume);
        $traderAccDist = \trader_ad($this->High, $this->Low, $this->Close, $this->Volume);
        $this->assertEqualsWithDelta($traderAccDist, $this->adjustForPECL($Output), 0.1);
    }

    public function testAdd(): void
    {
        $this->assertEquals(\trader_add($this->High, $this->Low), $this->adjustForPECL(LupeTrader::add($this->High, $this->Low)));
    }

    public function testAdOscVs()
    {
        $optInFastPeriod = 3;
        $optInSlowPeriod = 10;
        $this->assertEquals(
            Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            LupeTrader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod)
        );

        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEqualsWithDelta(
            Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            LupeTrader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            0.1
        );

        $optInFastPeriod = 3;
        $optInSlowPeriod = 15;
        $this->assertEqualsWithDelta(
            Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            LupeTrader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            0.1
        );
    }

    public function testAdOsc(): void
    {
        $optInFastPeriod = 3;
        $optInSlowPeriod = 10;
        $this->assertEqualsWithDelta(
            \trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            LupeTrader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            0.1
        );

        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEqualsWithDelta(
            \trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            LupeTrader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod),
            0.1
        );
    }

    public function testSlowStochRsi(): void
    {
        $rsi_period        = 14;
        $optInFastK_Period = 3;
        $optInSlowK_Period = 10;
        $optInSlowD_Period = 5;
        $optInSlowK_MAType = MovingAverageType::SMA->value;
        $optInSlowD_MAType = MovingAverageType::SMA->value;
        $Output            = LupeTrader::slowstochrsi($this->Close, $rsi_period, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $traderRsi         = \trader_rsi($this->Close, $rsi_period);
        [$traderSlowK, $traderSlowD] = \trader_stoch($traderRsi, $traderRsi, $traderRsi, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $this->assertEqualsWithDelta($traderSlowK, $this->adjustForPECL($Output['SlowK']), 0.1);
        $this->assertEqualsWithDelta($traderSlowD, $this->adjustForPECL($Output['SlowD']), 0.1);
    }
}
