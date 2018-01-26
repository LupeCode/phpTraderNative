<?php

namespace LupeCode\phpTraderNative\ConvertedJava;

class CandleSetting
{

    //<editor-fold defaultstate="collapsed" desc="Protected Members">

    /** @var int */
    public $settingType;
    /** @var int */
    public $rangeType;
    /** @var int */
    public $avgPeriod;
    /** @var float */
    public $factor;

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Constant Members">

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Getters and Setters">

    //</editor-fold>

    public function __construct(int $settingType, int $rangeType = null, int $avgPeriod = null, float $factor = null)
    {
        $this->settingType = $settingType;
        $this->rangeType   = $rangeType;
        $this->avgPeriod   = $avgPeriod;
        $this->factor      = $factor;
    }

    public function CopyFrom(CandleSetting $source)
    {
        $this->settingType = $source->settingType;
        $this->rangeType   = $source->rangeType;
        $this->avgPeriod   = $source->avgPeriod;
        $this->factor      = $source->factor;
    }

    public function CandleSetting(CandleSetting $that)
    {
        $this->settingType = $that->settingType;
        $this->rangeType   = $that->rangeType;
        $this->avgPeriod   = $that->avgPeriod;
        $this->factor      = $that->factor;
    }

}
