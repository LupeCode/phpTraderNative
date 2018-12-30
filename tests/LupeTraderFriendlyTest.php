<?php

namespace LupeCode\phpTraderNativeTest;

use LupeCode\phpTraderNative\LupeTraderFriendly;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNativeTest\TraderFriendlyTest;

class LupeTraderFriendlyTest extends TraderFriendlyTest
{
    /**
     * @throws \Exception
     */
    public function testSlowStochasticRelativeStrengthIndex()
    {
        $rsi_period        = 14;
        $optInFastK_Period = 3;
        $optInSlowK_Period = 10;
        $optInSlowD_Period = 5;
        $optInSlowK_MAType = MovingAverageType::SMA;
        $optInSlowD_MAType = MovingAverageType::SMA;
        $Output            = LupeTraderFriendly::slowStochasticRelativeStrengthIndex($this->Close, $rsi_period, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $traderRsi         = \trader_rsi($this->Close, $rsi_period);
        list($traderSlowK, $traderSlowD) = \trader_stoch($traderRsi, $traderRsi, $traderRsi, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $this->assertEquals($traderSlowK, $this->adjustForPECL($Output['SlowK']), '', 0.1);
        $this->assertEquals($traderSlowD, $this->adjustForPECL($Output['SlowD']), '', 0.1);
    }
}
