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

namespace LupeCode\phpTraderNative\TALib\Classes;

use LupeCode\phpTraderNative\TALib\Enum\CandleSettingType;
use LupeCode\phpTraderNative\TALib\Enum\RangeType;

class CandleSetting
{

    public int    $settingType;
    public ?int   $rangeType;
    public ?int   $avgPeriod;
    public ?float $factor;

    public function __construct(int|CandleSettingType $settingType, int|RangeType|null $rangeType = null, int $avgPeriod = null, float $factor = null)
    {
        if (is_int($settingType)) {
            $this->settingType = $settingType;
        } else {
            $this->settingType = $settingType->value;
        }
        if (is_int($rangeType) || is_null($rangeType)) {
            $this->rangeType = $rangeType;
        } else {
            $this->rangeType = $rangeType->value;
        }
        $this->avgPeriod = $avgPeriod;
        $this->factor    = $factor;
    }

    public function CopyFrom(CandleSetting $source): void
    {
        $this->settingType = $source->settingType;
        $this->rangeType   = $source->rangeType;
        $this->avgPeriod   = $source->avgPeriod;
        $this->factor      = $source->factor;
    }

    public function CandleSetting(CandleSetting $that): void
    {
        $this->settingType = $that->settingType;
        $this->rangeType   = $that->rangeType;
        $this->avgPeriod   = $that->avgPeriod;
        $this->factor      = $that->factor;
    }

}
