<?php

namespace LupeCode\phpTraderNativeTest;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\LupeTrader;
use LupeCode\phpTraderNativeTest\TraderTest;

class LupeTraderTest extends TraderTest
{

    /**
     * @throws \Exception
     */
    public function testSlowStochRsi()
    {
        $close = [4265.36, 4283.8, 3774.99, 3936.69, 3731.32, 3775, 4225.03, 4248, 3976, 4142.01, 4103.19, 3833.47, 3901.84, 3694.39, 3433.26, 3380.01, 3401, 3531.18, 3410.15, 3349.36, 3430.24, 3265, 3195.71, 3183, 3195];

        $rsi_period        = 3;
        $optInFastK_Period = 3;
        $optInSlowK_Period = 14;
        $optInSlowD_Period = 3;
        $optInSlowK_MAType = MovingAverageType::SMA;
        $optInSlowD_MAType = MovingAverageType::SMA;
        $Output            = LupeTrader::slowstochrsi($close, $rsi_period, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        var_export($Output);
    }
}
