<?php

namespace LupeCode\phpTraderNative;

class CandleSetting
{

    //<editor-fold defaultstate="collapsed" desc="Protected Members">

    /** @var CandleSettingType */
    protected $settingType;
    /** @var RangeType */
    protected $rangeType;
    /** @var int */
    protected $avgPeriod;
    /** @var float */
    protected $factor;

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Constant Members">

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Getters and Setters">

    //</editor-fold>

    public function __construct(CandleSettingType $settingType, RangeType $rangeType, int $avgPeriod, float $factor)
    {
        $this->settingType = $settingType;
        $this->rangeType   = $rangeType;
        $this->avgPeriod   = $avgPeriod;
        $this->factor      = $factor;
    }

    public function copyFrom(CandleSetting $source)
    {
        $this->settingType = $source->settingType;
        $this->rangeType   = $source->rangeType;
        $this->avgPeriod   = $source->avgPeriod;
        $this->factor      = $source->factor;
    }

}
