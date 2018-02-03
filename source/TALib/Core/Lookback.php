<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Enum\CandleSettingType;
use LupeCode\phpTraderNative\TALib\Enum\Compatibility;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TALib\Enum\UnstablePeriodFunctionID;

class Lookback extends Core
{

    protected function acosLookback(): int
    {
        return 0;
    }

    protected function adLookback(): int
    {
        return 0;
    }

    protected function addLookback(): int
    {
        return 0;
    }

    protected function adOscLookback(int $optInFastPeriod, int $optInSlowPeriod): int
    {
        if ((int)$optInFastPeriod == (\PHP_INT_MIN)) {
            $optInFastPeriod = 3;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInSlowPeriod == (\PHP_INT_MIN)) {
            $optInSlowPeriod = 10;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return -1;
        }
        if ($optInFastPeriod < $optInSlowPeriod) {
            $slowestPeriod = $optInSlowPeriod;
        } else {
            $slowestPeriod = $optInFastPeriod;
        }

        return $this->emaLookback($slowestPeriod);
    }

    protected function adxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return (2 * $optInTimePeriod) + ($this->unstablePeriod[UnstablePeriodFunctionID::ADX]) - 1;
    }

    protected function adxrLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + $this->adxLookback($optInTimePeriod) - 1;
        } else {
            return 3;
        }
    }

    protected function apoLookback(int $optInFastPeriod, int $optInSlowPeriod, int $optInMAType): int
    {
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return -1;
        }

        return $this->movingAverageLookback(((($optInSlowPeriod) > ($optInFastPeriod)) ? ($optInSlowPeriod) : ($optInFastPeriod)), $optInMAType);
    }

    protected function aroonLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function aroonOscLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function asinLookback(): int
    {
        return 0;
    }

    protected function atanLookback(): int
    {
        return 0;
    }

    protected function atrLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::ATR]);
    }

    protected function avgPriceLookback(): int
    {
        return 0;
    }

    protected function bbandsLookback(int $optInTimePeriod, float $optInNbDevUp, float $optInNbDevDn, int $optInMAType): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInNbDevUp == (-4e+37)) {
            $optInNbDevUp = 2.000000e+0;
        } elseif (($optInNbDevUp < -3.000000e+37) || ($optInNbDevUp > 3.000000e+37)) {
            return -1;
        }
        if ($optInNbDevDn == (-4e+37)) {
            $optInNbDevDn = 2.000000e+0;
        } elseif (($optInNbDevDn < -3.000000e+37) || ($optInNbDevDn > 3.000000e+37)) {
            return -1;
        }

        return $this->movingAverageLookback($optInTimePeriod, $optInMAType);
    }

    protected function betaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function bopLookback(): int
    {
        return 0;
    }

    protected function cciLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function cdl2CrowsLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 2;
    }

    protected function cdl3BlackCrowsLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) + 3;
    }

    protected function cdl3InsideLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    protected function cdl3LineStrikeLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::Near]->avgPeriod) + 3;
    }

    protected function cdl3OutsideLookback(): int
    {
        return 3;
    }

    protected function cdl3StarsInSouthLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))))) ? ((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))))) + 2;
    }

    protected function cdl3WhiteSoldiersLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) > ((((($this->candleSettings[CandleSettingType::Far]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Far]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))))) ? ((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) : ((((($this->candleSettings[CandleSettingType::Far]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Far]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))))) + 2;
    }

    protected function cdlAbandonedBabyLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return ((((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) + 2;
    }

    protected function cdlAdvanceBlockLookback(): int
    {
        return (((((((((($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) > ((((($this->candleSettings[CandleSettingType::Far]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Far]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))))) ? ((((($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) : ((((($this->candleSettings[CandleSettingType::Far]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Far]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod)))))) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (((((((($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) > ((((($this->candleSettings[CandleSettingType::Far]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Far]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))))) ? ((((($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) : ((((($this->candleSettings[CandleSettingType::Far]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Far]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod)))))) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    protected function cdlBeltHoldLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    protected function cdlBreakawayLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 4;
    }

    protected function cdlClosingMarubozuLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    protected function cdlConcealBabysWallLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) + 3;
    }

    protected function cdlCounterAttackLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    protected function cdlDarkCloudCoverLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 1;
    }

    protected function cdlDojiLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
    }

    protected function cdlDojiStarLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    protected function cdlDragonflyDojiLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    protected function cdlEngulfingLookback(): int
    {
        return 2;
    }

    protected function cdlEveningDojiStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return ((((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) +
               2;
    }

    protected function cdlEveningStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    protected function cdlGapSideSideWhiteLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::Near]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Equal]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Near]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Equal]->avgPeriod))) + 2;
    }

    protected function cdlGravestoneDojiLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    protected function cdlHammerLookback(): int
    {
        return (((((((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (((((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) +
               1;
    }

    protected function cdlHangingManLookback(): int
    {
        return (((((((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (((((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) +
               1;
    }

    protected function cdlHaramiLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    protected function cdlHaramiCrossLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    protected function cdlHighWaveLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod)));
    }

    protected function cdlHikkakeLookback(): int
    {
        return 5;
    }

    protected function cdlHikkakeModLookback(): int
    {
        return (((1) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (1) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) + 5;
    }

    protected function cdlHomingPigeonLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    protected function cdlIdentical3CrowsLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Equal]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Equal]->avgPeriod))) +
               2;
    }

    protected function cdlInNeckLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) +
               1;
    }

    protected function cdlInvertedHammerLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) +
               1;
    }

    protected function cdlKickingLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) +
               1;
    }

    protected function cdlKickingByLengthLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) +
               1;
    }

    protected function cdlLadderBottomLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) + 4;
    }

    protected function cdlLongLeggedDojiLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)));
    }

    protected function cdlLongLineLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod)));
    }

    protected function cdlMarubozuLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    protected function cdlMatchingLowLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) + 1;
    }

    protected function cdlMatHoldLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 4;
    }

    protected function cdlMorningDojiStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return ((((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) +
               2;
    }

    protected function cdlMorningStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    protected function cdlOnNeckLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) +
               1;
    }

    protected function cdlPiercingLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 1;
    }

    protected function cdlRickshawManLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod)));
    }

    protected function cdlRiseFall3MethodsLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 4;
    }

    protected function cdlSeparatingLinesLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::Equal]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::Equal]->avgPeriod))) +
               1;
    }

    protected function cdlShootingStarLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) +
               1;
    }

    protected function cdlShortLineLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowShort]->avgPeriod)));
    }

    protected function cdlSpinningTopLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod);
    }

    protected function cdlStalledPatternLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) > ((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))))) ? ((((($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) : ((((($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::Near]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::Near]->avgPeriod))))) +
               2;
    }

    protected function cdlStickSandwichLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::Equal]->avgPeriod) + 2;
    }

    protected function cdlTakuriLookback(): int
    {
        return ((((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) > (($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod))) ? ((((($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : (($this->candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) : (($this->candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod)));
    }

    protected function cdlTasukiGapLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::Near]->avgPeriod) + 2;
    }

    protected function cdlThrustingLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::Equal]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) +
               1;
    }

    protected function cdlTristarLookback(): int
    {
        return ($this->candleSettings[CandleSettingType::BodyDoji]->avgPeriod) + 2;
    }

    protected function cdlUnique3RiverLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    protected function cdlUpsideGap2CrowsLookback(): int
    {
        return (((($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? (($this->candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : (($this->candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    protected function cdlXSideGap3MethodsLookback(): int
    {
        return 2;
    }

    protected function ceilLookback(): int
    {
        return 0;
    }

    protected function cmoLookback(int $optInTimePeriod): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $retValue = $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::CMO]);
        if (($this->compatibility) == Compatibility::Metastock) {
            $retValue--;
        }

        return $retValue;
    }

    protected function correlLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function cosLookback(): int
    {
        return 0;
    }

    protected function coshLookback(): int
    {
        return 0;
    }

    protected function demaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $this->emaLookback($optInTimePeriod) * 2;
    }

    protected function divLookback(): int
    {
        return 0;
    }

    protected function dxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::DX]);
        } else {
            return 2;
        }
    }

    protected function emaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } else {
            if (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
                return -1;
            }
        }

        return $optInTimePeriod - 1 + ($this->unstablePeriod[UnstablePeriodFunctionID::EMA]);
    }

    protected function expLookback(): int
    {
        return 0;
    }

    protected function floorLookback(): int
    {
        return 0;
    }

    protected function htDcPeriodLookback(): int
    {
        return 32 + ($this->unstablePeriod[UnstablePeriodFunctionID::HtDcPeriod]);
    }

    protected function htDcPhaseLookback(): int
    {
        return 63 + ($this->unstablePeriod[UnstablePeriodFunctionID::HtDcPhase]);
    }

    protected function htPhasorLookback(): int
    {
        return 32 + ($this->unstablePeriod[UnstablePeriodFunctionID::HtPhasor]);
    }

    protected function htSineLookback(): int
    {
        return 63 + ($this->unstablePeriod[UnstablePeriodFunctionID::HtSine]);
    }

    protected function htTrendlineLookback(): int
    {
        return 63 + ($this->unstablePeriod[UnstablePeriodFunctionID::HtTrendline]);
    }

    protected function htTrendModeLookback(): int
    {
        return 63 + ($this->unstablePeriod[UnstablePeriodFunctionID::HtTrendMode]);
    }

    protected function kamaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::KAMA]);
    }

    protected function linearRegLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function linearRegAngleLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function linearRegInterceptLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function linearRegSlopeLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function lnLookback(): int
    {
        return 0;
    }

    protected function log10Lookback(): int
    {
        return 0;
    }

    protected function movingAverageLookback(int $optInTimePeriod, int $optInMAType): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod <= 1) {
            return 0;
        }
        switch ($optInMAType) {
            case MovingAverageType::SMA:
                $retValue = $this->smaLookback($optInTimePeriod);
                break;
            case MovingAverageType::EMA:
                $retValue = $this->emaLookback($optInTimePeriod);
                break;
            case MovingAverageType::WMA:
                $retValue = $this->wmaLookback($optInTimePeriod);
                break;
            case MovingAverageType::DEMA:
                $retValue = $this->demaLookback($optInTimePeriod);
                break;
            case MovingAverageType::TEMA:
                $retValue = $this->temaLookback($optInTimePeriod);
                break;
            case MovingAverageType::TRIMA:
                $retValue = $this->trimaLookback($optInTimePeriod);
                break;
            case MovingAverageType::KAMA:
                $retValue = $this->kamaLookback($optInTimePeriod);
                break;
            case MovingAverageType::MAMA:
                $retValue = $this->mamaLookback(0.5, 0.05);
                break;
            case MovingAverageType::T3:
                $retValue = $this->t3Lookback($optInTimePeriod, 0.7);
                break;
            default:
                $retValue = 0;
        }

        return $retValue;
    }

    protected function macdLookback(int $optInFastPeriod, int $optInSlowPeriod, int $optInSignalPeriod): int
    {
        //int $tempInteger;
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInSignalPeriod == (PHP_INT_MIN)) {
            $optInSignalPeriod = 9;
        } elseif (((int)$optInSignalPeriod < 1) || ((int)$optInSignalPeriod > 100000)) {
            return -1;
        }
        if ($optInSlowPeriod < $optInFastPeriod) {
            $tempInteger     = $optInSlowPeriod;
            $optInSlowPeriod = $optInFastPeriod;
            $optInFastPeriod = $tempInteger;
        }

        return $this->emaLookback($optInSlowPeriod)
               + $this->emaLookback($optInSignalPeriod);
    }

    protected function macdExtLookback(int $optInFastPeriod, int $optInFastMAType, int $optInSlowPeriod, int $optInSlowMAType, int $optInSignalPeriod, int $optInSignalMAType): int
    {
        //int $tempInteger, $lookbackLargest;
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInSignalPeriod == (PHP_INT_MIN)) {
            $optInSignalPeriod = 9;
        } elseif (((int)$optInSignalPeriod < 1) || ((int)$optInSignalPeriod > 100000)) {
            return -1;
        }
        $lookbackLargest = $this->movingAverageLookback($optInFastPeriod, $optInFastMAType);
        $tempInteger     = $this->movingAverageLookback($optInSlowPeriod, $optInSlowMAType);
        if ($tempInteger > $lookbackLargest) {
            $lookbackLargest = $tempInteger;
        }

        return $lookbackLargest + $this->movingAverageLookback($optInSignalPeriod, $optInSignalMAType);
    }

    protected function macdFixLookback(int $optInSignalPeriod): int
    {
        if ((int)$optInSignalPeriod == (PHP_INT_MIN)) {
            $optInSignalPeriod = 9;
        } elseif (((int)$optInSignalPeriod < 1) || ((int)$optInSignalPeriod > 100000)) {
            return -1;
        }

        return $this->emaLookback(26)
               + $this->emaLookback($optInSignalPeriod);
    }

    protected function mamaLookback(float $optInFastLimit, float $optInSlowLimit): int
    {
        if ($optInFastLimit == (-4e+37)) {
            $optInFastLimit = 5.000000e-1;
        } elseif (($optInFastLimit < 1.000000e-2) || ($optInFastLimit > 9.900000e-1)) {
            return -1;
        }
        if ($optInSlowLimit == (-4e+37)) {
            $optInSlowLimit = 5.000000e-2;
        } elseif (($optInSlowLimit < 1.000000e-2) || ($optInSlowLimit > 9.900000e-1)) {
            return -1;
        }

        return 32 + ($this->unstablePeriod[UnstablePeriodFunctionID::MAMA]);
    }

    protected function movingAverageVariablePeriodLookback(int $optInMinPeriod, int $optInMaxPeriod, int $optInMAType): int
    {
        if ((int)$optInMinPeriod == (PHP_INT_MIN)) {
            $optInMinPeriod = 2;
        } elseif (((int)$optInMinPeriod < 2) || ((int)$optInMinPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInMaxPeriod == (PHP_INT_MIN)) {
            $optInMaxPeriod = 30;
        } elseif (((int)$optInMaxPeriod < 2) || ((int)$optInMaxPeriod > 100000)) {
            return -1;
        }

        return $this->movingAverageLookback($optInMaxPeriod, $optInMAType);
    }

    protected function maxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function maxIndexLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function medPriceLookback(): int
    {
        return 0;
    }

    protected function mfiLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::MFI]);
    }

    protected function midPointLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function midPriceLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function minLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function minIndexLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function minMaxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function minMaxIndexLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function minusDILookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::MinusDI]);
        } else {
            return 1;
        }
    }

    protected function minusDMLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::MinusDM]) - 1;
        } else {
            return 1;
        }
    }

    protected function momLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function multLookback(): int
    {
        return 0;
    }

    protected function natrLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::NATR]);
    }

    protected function obvLookback(): int
    {
        return 0;
    }

    protected function plusDILookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::PlusDI]);
        } else {
            return 1;
        }
    }

    protected function plusDMLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::PlusDM]) - 1;
        } else {
            return 1;
        }
    }

    protected function ppoLookback(int $optInFastPeriod, int $optInSlowPeriod, int $optInMAType): int
    {
        if ((int)$optInFastPeriod == (PHP_INT_MIN)) {
            $optInFastPeriod = 12;
        } elseif (((int)$optInFastPeriod < 2) || ((int)$optInFastPeriod > 100000)) {
            return -1;
        }
        if ((int)$optInSlowPeriod == (PHP_INT_MIN)) {
            $optInSlowPeriod = 26;
        } elseif (((int)$optInSlowPeriod < 2) || ((int)$optInSlowPeriod > 100000)) {
            return -1;
        }

        return $this->movingAverageLookback(((($optInSlowPeriod) > ($optInFastPeriod)) ? ($optInSlowPeriod) : ($optInFastPeriod)), $optInMAType);
    }

    protected function rocLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function rocPLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function rocRLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function rocR100Lookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    protected function rsiLookback(int $optInTimePeriod): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $retValue = $optInTimePeriod + ($this->unstablePeriod[UnstablePeriodFunctionID::RSI]);
        if (($this->compatibility) == Compatibility::Metastock) {
            $retValue--;
        }

        return $retValue;
    }

    protected function sarLookback(float $optInAcceleration, float $optInMaximum): int
    {
        if ($optInAcceleration == (-4e+37)) {
            $optInAcceleration = 2.000000e-2;
        } elseif (($optInAcceleration < 0.000000e+0) || ($optInAcceleration > 3.000000e+37)) {
            return -1;
        }
        if ($optInMaximum == (-4e+37)) {
            $optInMaximum = 2.000000e-1;
        } elseif (($optInMaximum < 0.000000e+0) || ($optInMaximum > 3.000000e+37)) {
            return -1;
        }

        return 1;
    }

    protected function sarExtLookback(float $optInStartValue, float $optInOffsetOnReverse, float $optInAccelerationInitLong, float $optInAccelerationLong, float $optInAccelerationMaxLong, float $optInAccelerationInitShort, float $optInAccelerationShort, float $optInAccelerationMaxShort): int
    {
        if ($optInStartValue == (-4e+37)) {
            $optInStartValue = 0.000000e+0;
        } elseif (($optInStartValue < -3.000000e+37) || ($optInStartValue > 3.000000e+37)) {
            return -1;
        }
        if ($optInOffsetOnReverse == (-4e+37)) {
            $optInOffsetOnReverse = 0.000000e+0;
        } elseif (($optInOffsetOnReverse < 0.000000e+0) || ($optInOffsetOnReverse > 3.000000e+37)) {
            return -1;
        }
        if ($optInAccelerationInitLong == (-4e+37)) {
            $optInAccelerationInitLong = 2.000000e-2;
        } elseif (($optInAccelerationInitLong < 0.000000e+0) || ($optInAccelerationInitLong > 3.000000e+37)) {
            return -1;
        }
        if ($optInAccelerationLong == (-4e+37)) {
            $optInAccelerationLong = 2.000000e-2;
        } elseif (($optInAccelerationLong < 0.000000e+0) || ($optInAccelerationLong > 3.000000e+37)) {
            return -1;
        }
        if ($optInAccelerationMaxLong == (-4e+37)) {
            $optInAccelerationMaxLong = 2.000000e-1;
        } elseif (($optInAccelerationMaxLong < 0.000000e+0) || ($optInAccelerationMaxLong > 3.000000e+37)) {
            return -1;
        }
        if ($optInAccelerationInitShort == (-4e+37)) {
            $optInAccelerationInitShort = 2.000000e-2;
        } elseif (($optInAccelerationInitShort < 0.000000e+0) || ($optInAccelerationInitShort > 3.000000e+37)) {
            return -1;
        }
        if ($optInAccelerationShort == (-4e+37)) {
            $optInAccelerationShort = 2.000000e-2;
        } elseif (($optInAccelerationShort < 0.000000e+0) || ($optInAccelerationShort > 3.000000e+37)) {
            return -1;
        }
        if ($optInAccelerationMaxShort == (-4e+37)) {
            $optInAccelerationMaxShort = 2.000000e-1;
        } elseif (($optInAccelerationMaxShort < 0.000000e+0) || ($optInAccelerationMaxShort > 3.000000e+37)) {
            return -1;
        }

        return 1;
    }

    protected function sinLookback(): int
    {
        return 0;
    }

    protected function sinhLookback(): int
    {
        return 0;
    }

    protected function smaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function sqrtLookback(): int
    {
        return 0;
    }

    protected function stdDevLookback(int $optInTimePeriod, float $optInNbDev): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInNbDev == (-4e+37)) {
            $optInNbDev = 1.000000e+0;
        } elseif (($optInNbDev < -3.000000e+37) || ($optInNbDev > 3.000000e+37)) {
            return -1;
        }

        return $this->varianceLookback($optInTimePeriod, $optInNbDev);
    }

    protected function stochLookback(int $optInFastK_Period, int $optInSlowK_Period, int $optInSlowK_MAType, int $optInSlowD_Period, int $optInSlowD_MAType): int
    {
        //int $retValue;
        if ((int)$optInFastK_Period == (PHP_INT_MIN)) {
            $optInFastK_Period = 5;
        } elseif (((int)$optInFastK_Period < 1) || ((int)$optInFastK_Period > 100000)) {
            return -1;
        }
        if ((int)$optInSlowK_Period == (PHP_INT_MIN)) {
            $optInSlowK_Period = 3;
        } elseif (((int)$optInSlowK_Period < 1) || ((int)$optInSlowK_Period > 100000)) {
            return -1;
        }
        if ((int)$optInSlowD_Period == (PHP_INT_MIN)) {
            $optInSlowD_Period = 3;
        } elseif (((int)$optInSlowD_Period < 1) || ((int)$optInSlowD_Period > 100000)) {
            return -1;
        }
        $retValue = ($optInFastK_Period - 1);
        $retValue += $this->movingAverageLookback($optInSlowK_Period, $optInSlowK_MAType);
        $retValue += $this->movingAverageLookback($optInSlowD_Period, $optInSlowD_MAType);

        return $retValue;
    }

    protected function stochFLookback(int $optInFastK_Period, int $optInFastD_Period, int $optInFastD_MAType): int
    {
        //int $retValue;
        if ((int)$optInFastK_Period == (PHP_INT_MIN)) {
            $optInFastK_Period = 5;
        } elseif (((int)$optInFastK_Period < 1) || ((int)$optInFastK_Period > 100000)) {
            return -1;
        }
        if ((int)$optInFastD_Period == (PHP_INT_MIN)) {
            $optInFastD_Period = 3;
        } elseif (((int)$optInFastD_Period < 1) || ((int)$optInFastD_Period > 100000)) {
            return -1;
        }
        $retValue = ($optInFastK_Period - 1);
        $retValue += $this->movingAverageLookback($optInFastD_Period, $optInFastD_MAType);

        return $retValue;
    }

    protected function stochRsiLookback(int $optInTimePeriod, int $optInFastK_Period, int $optInFastD_Period, int $optInFastD_MAType): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ((int)$optInFastK_Period == (PHP_INT_MIN)) {
            $optInFastK_Period = 5;
        } elseif (((int)$optInFastK_Period < 1) || ((int)$optInFastK_Period > 100000)) {
            return -1;
        }
        if ((int)$optInFastD_Period == (PHP_INT_MIN)) {
            $optInFastD_Period = 3;
        } elseif (((int)$optInFastD_Period < 1) || ((int)$optInFastD_Period > 100000)) {
            return -1;
        }
        $retValue = $this->rsiLookback($optInTimePeriod) + $this->stochFLookback($optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);

        return $retValue;
    }

    protected function subLookback(): int
    {
        return 0;
    }

    protected function sumLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function t3Lookback(int $optInTimePeriod, float $optInVFactor): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInVFactor == (-4e+37)) {
            $optInVFactor = 7.000000e-1;
        } elseif (($optInVFactor < 0.000000e+0) || ($optInVFactor > 1.000000e+0)) {
            return -1;
        }

        return 6 * ($optInTimePeriod - 1) + ($this->unstablePeriod[UnstablePeriodFunctionID::T3]);
    }

    protected function tanLookback(): int
    {
        return 0;
    }

    protected function tanhLookback(): int
    {
        return 0;
    }

    protected function temaLookback(int $optInTimePeriod): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $retValue = $this->emaLookback($optInTimePeriod);

        return $retValue * 3;
    }

    protected function trueRangeLookback(): int
    {
        return 1;
    }

    protected function trimaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function trixLookback(int $optInTimePeriod): int
    {
        //int $emaLookback;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $emaLookback = $this->emaLookback($optInTimePeriod);

        return ($emaLookback * 3) + $this->rocRLookback(1);
    }

    protected function tsfLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function typPriceLookback(): int
    {
        return 0;
    }

    protected function ultOscLookback(int $optInTimePeriod1, int $optInTimePeriod2, int $optInTimePeriod3): int
    {
        //int $maxPeriod;
        if ((int)$optInTimePeriod1 == (PHP_INT_MIN)) {
            $optInTimePeriod1 = 7;
        } elseif (((int)$optInTimePeriod1 < 1) || ((int)$optInTimePeriod1 > 100000)) {
            return -1;
        }
        if ((int)$optInTimePeriod2 == (PHP_INT_MIN)) {
            $optInTimePeriod2 = 14;
        } elseif (((int)$optInTimePeriod2 < 1) || ((int)$optInTimePeriod2 > 100000)) {
            return -1;
        }
        if ((int)$optInTimePeriod3 == (PHP_INT_MIN)) {
            $optInTimePeriod3 = 28;
        } elseif (((int)$optInTimePeriod3 < 1) || ((int)$optInTimePeriod3 > 100000)) {
            return -1;
        }
        $maxPeriod = (((((($optInTimePeriod1) > ($optInTimePeriod2)) ? ($optInTimePeriod1) : ($optInTimePeriod2))) > ($optInTimePeriod3)) ? (((($optInTimePeriod1) > ($optInTimePeriod2)) ? ($optInTimePeriod1) : ($optInTimePeriod2))) : ($optInTimePeriod3));

        return $this->smaLookback($maxPeriod) + 1;
    }

    protected function varianceLookback(int $optInTimePeriod, float $optInNbDev): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInNbDev == (-4e+37)) {
            $optInNbDev = 1.000000e+0;
        } elseif (($optInNbDev < -3.000000e+37) || ($optInNbDev > 3.000000e+37)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    protected function wclPriceLookback(): int
    {
        return 0;
    }

    protected function willRLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    protected function wmaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }
}

