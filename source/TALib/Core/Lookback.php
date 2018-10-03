<?php

/**
 * This is a PHP port of the Trader extension for PHP, which is a port of the TA-LIB C code.
 *
 * This port is written in PHP and without any other requirements.
 * The goal is that this library can be used by those whom cannot install the PHP Trader extension.
 *
 * Below is the copyright information for TA-LIB found in the source code.
 */

/* TA-LIB Copyright (c) 1999-2007, Mario Fortier
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 *
 * - Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in
 *   the documentation and/or other materials provided with the
 *   distribution.
 *
 * - Neither name of author nor the names of its contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Enum\CandleSettingType;
use LupeCode\phpTraderNative\TALib\Enum\Compatibility;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TALib\Enum\UnstablePeriodFunctionID;

class Lookback extends Core
{

    public static function acosLookback(): int
    {
        return 0;
    }

    public static function adLookback(): int
    {
        return 0;
    }

    public static function addLookback(): int
    {
        return 0;
    }

    public static function adOscLookback(int $optInFastPeriod, int $optInSlowPeriod): int
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

        return self::emaLookback($slowestPeriod);
    }

    public static function adxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return (2 * $optInTimePeriod) + (static::$unstablePeriod[UnstablePeriodFunctionID::ADX]) - 1;
    }

    public static function adxrLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + self::adxLookback($optInTimePeriod) - 1;
        } else {
            return 3;
        }
    }

    public static function apoLookback(int $optInFastPeriod, int $optInSlowPeriod, int $optInMAType): int
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

        return self::movingAverageLookback(((($optInSlowPeriod) > ($optInFastPeriod)) ? ($optInSlowPeriod) : ($optInFastPeriod)), $optInMAType);
    }

    public static function aroonLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function aroonOscLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function asinLookback(): int
    {
        return 0;
    }

    public static function atanLookback(): int
    {
        return 0;
    }

    public static function atrLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::ATR]);
    }

    public static function avgPriceLookback(): int
    {
        return 0;
    }

    public static function bbandsLookback(int $optInTimePeriod, float $optInNbDevUp, float $optInNbDevDn, int $optInMAType): int
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

        return self::movingAverageLookback($optInTimePeriod, $optInMAType);
    }

    public static function betaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 5;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function bopLookback(): int
    {
        return 0;
    }

    public static function cciLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function cdl2CrowsLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 2;
    }

    public static function cdl3BlackCrowsLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) + 3;
    }

    public static function cdl3InsideLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    public static function cdl3LineStrikeLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::Near]->avgPeriod) + 3;
    }

    public static function cdl3OutsideLookback(): int
    {
        return 3;
    }

    public static function cdl3StarsInSouthLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > (((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))))) ? (((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : (((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))))) + 2;
    }

    public static function cdl3WhiteSoldiersLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) > (((((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))))) ? (((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) : (((((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))))) + 2;
    }

    public static function cdlAbandonedBabyLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return (((((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) + 2;
    }

    public static function cdlAdvanceBlockLookback(): int
    {
        return ((((((((((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) > (((((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))))) ? (((((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) : (((((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod)))))) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((((((((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) > (((((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))))) ? (((((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod)))) : (((((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Far]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod)))))) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    public static function cdlBeltHoldLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    public static function cdlBreakawayLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 4;
    }

    public static function cdlClosingMarubozuLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    public static function cdlConcealBabysWallLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) + 3;
    }

    public static function cdlCounterAttackLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlDarkCloudCoverLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return (static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 1;
    }

    public static function cdlDojiLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod);
    }

    public static function cdlDojiStarLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlDragonflyDojiLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    public static function cdlEngulfingLookback(): int
    {
        return 2;
    }

    public static function cdlEveningDojiStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return (((((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) + 2;
    }

    public static function cdlEveningStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    public static function cdlGapSideSideWhiteLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::Near]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Near]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod))) + 2;
    }

    public static function cdlGravestoneDojiLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    public static function cdlHammerLookback(): int
    {
        return ((((((((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((((((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) + 1;
    }

    public static function cdlHangingManLookback(): int
    {
        return ((((((((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((((((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) + 1;
    }

    public static function cdlHaramiLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlHaramiCrossLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlHighWaveLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod)));
    }

    public static function cdlHikkakeLookback(): int
    {
        return 5;
    }

    public static function cdlHikkakeModLookback(): int
    {
        return (((1) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? (1) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) + 5;
    }

    public static function cdlHomingPigeonLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlIdentical3CrowsLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod))) + 2;
    }

    public static function cdlInNeckLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlInvertedHammerLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) + 1;
    }

    public static function cdlKickingLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlKickingByLengthLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlLadderBottomLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod) + 4;
    }

    public static function cdlLongLeggedDojiLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)));
    }

    public static function cdlLongLineLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod)));
    }

    public static function cdlMarubozuLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)));
    }

    public static function cdlMatchingLowLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::Equal]->avgPeriod) + 1;
    }

    public static function cdlMatHoldLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 5.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 4;
    }

    public static function cdlMorningDojiStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return (((((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) + 2;
    }

    public static function cdlMorningStarLookback(float $optInPenetration): int
    {
        if ($optInPenetration == (-4e+37)) {
            $optInPenetration = 3.000000e-1;
        } elseif (($optInPenetration < 0.000000e+0) || ($optInPenetration > 3.000000e+37)) {
            return -1;
        }

        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    public static function cdlOnNeckLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlPiercingLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod) + 1;
    }

    public static function cdlRickshawManLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod)));
    }

    public static function cdlRiseFall3MethodsLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 4;
    }

    public static function cdlSeparatingLinesLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod))) + 1;
    }

    public static function cdlShootingStarLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowLong]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) + 1;
    }

    public static function cdlShortLineLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowShort]->avgPeriod)));
    }

    public static function cdlSpinningTopLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod);
    }

    public static function cdlStalledPatternLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) > (((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))))) ? (((((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)))) : (((((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::Near]->avgPeriod))))) + 2;
    }

    public static function cdlStickSandwichLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::Equal]->avgPeriod) + 2;
    }

    public static function cdlTakuriLookback(): int
    {
        return (((((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) > ((static::$candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod))) ? (((((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::ShadowVeryShort]->avgPeriod)))) : ((static::$candleSettings[CandleSettingType::ShadowVeryLong]->avgPeriod)));
    }

    public static function cdlTasukiGapLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::Near]->avgPeriod) + 2;
    }

    public static function cdlThrustingLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::Equal]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 1;
    }

    public static function cdlTristarLookback(): int
    {
        return (static::$candleSettings[CandleSettingType::BodyDoji]->avgPeriod) + 2;
    }

    public static function cdlUnique3RiverLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    public static function cdlUpsideGap2CrowsLookback(): int
    {
        return ((((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) > ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) ? ((static::$candleSettings[CandleSettingType::BodyShort]->avgPeriod)) : ((static::$candleSettings[CandleSettingType::BodyLong]->avgPeriod))) + 2;
    }

    public static function cdlXSideGap3MethodsLookback(): int
    {
        return 2;
    }

    public static function ceilLookback(): int
    {
        return 0;
    }

    public static function cmoLookback(int $optInTimePeriod): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $retValue = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::CMO]);
        if ((static::$compatibility) == Compatibility::Metastock) {
            $retValue--;
        }

        return $retValue;
    }

    public static function correlLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function cosLookback(): int
    {
        return 0;
    }

    public static function coshLookback(): int
    {
        return 0;
    }

    public static function demaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return self::emaLookback($optInTimePeriod) * 2;
    }

    public static function divLookback(): int
    {
        return 0;
    }

    public static function dxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::DX]);
        } else {
            return 2;
        }
    }

    public static function emaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } else {
            if (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
                return -1;
            }
        }

        return $optInTimePeriod - 1 + (static::$unstablePeriod[UnstablePeriodFunctionID::EMA]);
    }

    public static function expLookback(): int
    {
        return 0;
    }

    public static function floorLookback(): int
    {
        return 0;
    }

    public static function htDcPeriodLookback(): int
    {
        return 32 + (static::$unstablePeriod[UnstablePeriodFunctionID::HtDcPeriod]);
    }

    public static function htDcPhaseLookback(): int
    {
        return 63 + (static::$unstablePeriod[UnstablePeriodFunctionID::HtDcPhase]);
    }

    public static function htPhasorLookback(): int
    {
        return 32 + (static::$unstablePeriod[UnstablePeriodFunctionID::HtPhasor]);
    }

    public static function htSineLookback(): int
    {
        return 63 + (static::$unstablePeriod[UnstablePeriodFunctionID::HtSine]);
    }

    public static function htTrendlineLookback(): int
    {
        return 63 + (static::$unstablePeriod[UnstablePeriodFunctionID::HtTrendline]);
    }

    public static function htTrendModeLookback(): int
    {
        return 63 + (static::$unstablePeriod[UnstablePeriodFunctionID::HtTrendMode]);
    }

    public static function kamaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::KAMA]);
    }

    public static function linearRegLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function linearRegAngleLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function linearRegInterceptLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function linearRegSlopeLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function lnLookback(): int
    {
        return 0;
    }

    public static function log10Lookback(): int
    {
        return 0;
    }

    public static function movingAverageLookback(int $optInTimePeriod, int $optInMAType): int
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
                $retValue = self::smaLookback($optInTimePeriod);
                break;
            case MovingAverageType::EMA:
                $retValue = self::emaLookback($optInTimePeriod);
                break;
            case MovingAverageType::WMA:
                $retValue = self::wmaLookback($optInTimePeriod);
                break;
            case MovingAverageType::DEMA:
                $retValue = self::demaLookback($optInTimePeriod);
                break;
            case MovingAverageType::TEMA:
                $retValue = self::temaLookback($optInTimePeriod);
                break;
            case MovingAverageType::TRIMA:
                $retValue = self::trimaLookback($optInTimePeriod);
                break;
            case MovingAverageType::KAMA:
                $retValue = self::kamaLookback($optInTimePeriod);
                break;
            case MovingAverageType::MAMA:
                $retValue = self::mamaLookback(0.5, 0.05);
                break;
            case MovingAverageType::T3:
                $retValue = self::t3Lookback($optInTimePeriod, 0.7);
                break;
            default:
                $retValue = 0;
        }

        return $retValue;
    }

    public static function macdLookback(int $optInFastPeriod, int $optInSlowPeriod, int $optInSignalPeriod): int
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

        return self::emaLookback($optInSlowPeriod)
               + self::emaLookback($optInSignalPeriod);
    }

    public static function macdExtLookback(int $optInFastPeriod, int $optInFastMAType, int $optInSlowPeriod, int $optInSlowMAType, int $optInSignalPeriod, int $optInSignalMAType): int
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
        $lookbackLargest = self::movingAverageLookback($optInFastPeriod, $optInFastMAType);
        $tempInteger     = self::movingAverageLookback($optInSlowPeriod, $optInSlowMAType);
        if ($tempInteger > $lookbackLargest) {
            $lookbackLargest = $tempInteger;
        }

        return $lookbackLargest + self::movingAverageLookback($optInSignalPeriod, $optInSignalMAType);
    }

    public static function macdFixLookback(int $optInSignalPeriod): int
    {
        if ((int)$optInSignalPeriod == (PHP_INT_MIN)) {
            $optInSignalPeriod = 9;
        } elseif (((int)$optInSignalPeriod < 1) || ((int)$optInSignalPeriod > 100000)) {
            return -1;
        }

        return self::emaLookback(26)
               + self::emaLookback($optInSignalPeriod);
    }

    public static function mamaLookback(float $optInFastLimit, float $optInSlowLimit): int
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

        return 32 + (static::$unstablePeriod[UnstablePeriodFunctionID::MAMA]);
    }

    public static function movingAverageVariablePeriodLookback(int $optInMinPeriod, int $optInMaxPeriod, int $optInMAType): int
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

        return self::movingAverageLookback($optInMaxPeriod, $optInMAType);
    }

    public static function maxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function maxIndexLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function medPriceLookback(): int
    {
        return 0;
    }

    public static function mfiLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::MFI]);
    }

    public static function midPointLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function midPriceLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function minLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function minIndexLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function minMaxLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function minMaxIndexLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function minusDILookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::MinusDI]);
        } else {
            return 1;
        }
    }

    public static function minusDMLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::MinusDM]) - 1;
        } else {
            return 1;
        }
    }

    public static function momLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function multLookback(): int
    {
        return 0;
    }

    public static function natrLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::NATR]);
    }

    public static function obvLookback(): int
    {
        return 0;
    }

    public static function plusDILookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::PlusDI]);
        } else {
            return 1;
        }
    }

    public static function plusDMLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        if ($optInTimePeriod > 1) {
            return $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::PlusDM]) - 1;
        } else {
            return 1;
        }
    }

    public static function ppoLookback(int $optInFastPeriod, int $optInSlowPeriod, int $optInMAType): int
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

        return self::movingAverageLookback(((($optInSlowPeriod) > ($optInFastPeriod)) ? ($optInSlowPeriod) : ($optInFastPeriod)), $optInMAType);
    }

    public static function rocLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function rocPLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function rocRLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function rocR100Lookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 10;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod;
    }

    public static function rsiLookback(int $optInTimePeriod): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $retValue = $optInTimePeriod + (static::$unstablePeriod[UnstablePeriodFunctionID::RSI]);
        if ((static::$compatibility) == Compatibility::Metastock) {
            $retValue--;
        }

        return $retValue;
    }

    public static function sarLookback(float $optInAcceleration, float $optInMaximum): int
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

    public static function sarExtLookback(float $optInStartValue, float $optInOffsetOnReverse, float $optInAccelerationInitLong, float $optInAccelerationLong, float $optInAccelerationMaxLong, float $optInAccelerationInitShort, float $optInAccelerationShort, float $optInAccelerationMaxShort): int
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

    public static function sinLookback(): int
    {
        return 0;
    }

    public static function sinhLookback(): int
    {
        return 0;
    }

    public static function smaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function sqrtLookback(): int
    {
        return 0;
    }

    public static function stdDevLookback(int $optInTimePeriod, float $optInNbDev): int
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

        return self::varianceLookback($optInTimePeriod, $optInNbDev);
    }

    public static function stochLookback(int $optInFastK_Period, int $optInSlowK_Period, int $optInSlowK_MAType, int $optInSlowD_Period, int $optInSlowD_MAType): int
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
        $retValue += self::movingAverageLookback($optInSlowK_Period, $optInSlowK_MAType);
        $retValue += self::movingAverageLookback($optInSlowD_Period, $optInSlowD_MAType);

        return $retValue;
    }

    public static function stochFLookback(int $optInFastK_Period, int $optInFastD_Period, int $optInFastD_MAType): int
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
        $retValue += self::movingAverageLookback($optInFastD_Period, $optInFastD_MAType);

        return $retValue;
    }

    public static function stochRsiLookback(int $optInTimePeriod, int $optInFastK_Period, int $optInFastD_Period, int $optInFastD_MAType): int
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
        $retValue = self::rsiLookback($optInTimePeriod) + self::stochFLookback($optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);

        return $retValue;
    }

    public static function subLookback(): int
    {
        return 0;
    }

    public static function sumLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function t3Lookback(int $optInTimePeriod, float $optInVFactor): int
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

        return 6 * ($optInTimePeriod - 1) + (static::$unstablePeriod[UnstablePeriodFunctionID::T3]);
    }

    public static function tanLookback(): int
    {
        return 0;
    }

    public static function tanhLookback(): int
    {
        return 0;
    }

    public static function temaLookback(int $optInTimePeriod): int
    {
        //int $retValue;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $retValue = self::emaLookback($optInTimePeriod);

        return $retValue * 3;
    }

    public static function trueRangeLookback(): int
    {
        return 1;
    }

    public static function trimaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function trixLookback(int $optInTimePeriod): int
    {
        //int $emaLookback;
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 1) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }
        $emaLookback = self::emaLookback($optInTimePeriod);

        return ($emaLookback * 3) + self::rocRLookback(1);
    }

    public static function tsfLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }

    public static function typPriceLookback(): int
    {
        return 0;
    }

    public static function ultOscLookback(int $optInTimePeriod1, int $optInTimePeriod2, int $optInTimePeriod3): int
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

        return self::smaLookback($maxPeriod) + 1;
    }

    public static function varianceLookback(int $optInTimePeriod, float $optInNbDev): int
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

    public static function wclPriceLookback(): int
    {
        return 0;
    }

    public static function willRLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 14;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return ($optInTimePeriod - 1);
    }

    public static function wmaLookback(int $optInTimePeriod): int
    {
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return -1;
        }

        return $optInTimePeriod - 1;
    }
}

