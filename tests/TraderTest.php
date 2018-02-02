<?php

namespace LupeCode\phpTraderNative;

use PHPUnit\Framework\TestCase;

class TraderTest extends TestCase
{
    protected function adjustForPECL(array $outReal, int $precision = 3, int $mode = \PHP_ROUND_HALF_DOWN)
    {
        $newOutReal = [];
        $outReal    = \array_values($outReal);
        foreach ($outReal as $index => $inDouble) {
            $newOutReal[$index] = round($inDouble, $precision, $mode);
        }

        return $newOutReal;
    }

    public function testAcos()
    {
        $in = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_acos($in), $this->adjustForPECL(Trader::acos($in)));
    }
}
