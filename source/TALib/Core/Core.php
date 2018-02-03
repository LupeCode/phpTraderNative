<?php

namespace LupeCode\phpTraderNative\TALib\Core;

class Core
{

    /** @var int[] */
    protected $unstablePeriod;
    /** @var CandleSetting[] */
    protected $candleSettings;
    /** @var int */
    protected $compatibility;

    public function __construct()
    {
        $this->TA_CandleDefaultSettings = $this->TA_CandleDefaultSettings();
        $this->unstablePeriod           = \array_pad([], FuncUnstId::ALL, 0);
        $this->compatibility            = Compatibility::Default;
        $this->candleSettings           = \array_fill(0, CandleSettingType::AllCandleSettings, null);
        for ($i = 0; $i < count($this->candleSettings); $i++) {
            $this->candleSettings[$i] = $this->TA_CandleDefaultSettings[$i];
        }
    }

    protected function double(int $size)
    {
        return \array_pad([], $size, 0.);
    }

    public function SetCandleSettings(int $settingType, int $rangeType, int $avgPeriod, float $factor)
    {
        if ($settingType >= CandleSettingType::AllCandleSettings) {
            return RetCode::BadParam;
        }
        $this->candleSettings[$settingType]->settingType = $settingType;
        $this->candleSettings[$settingType]->rangeType   = $rangeType;
        $this->candleSettings[$settingType]->avgPeriod   = $avgPeriod;
        $this->candleSettings[$settingType]->factor      = $factor;

        return RetCode::Success;
    }

    /** @var CandleSetting[] */
    protected $TA_CandleDefaultSettings;

    final protected function TA_CandleDefaultSettings()
    {
        return [
            /* real body is long when it's longer than the average of the 10 previous candles' real body */
            new CandleSetting(CandleSettingType::BodyLong, RangeType::RealBody, 10, 1.),
            /* real body is very long when it's longer than 3 times the average of the 10 previous candles' real body */
            new CandleSetting(CandleSettingType::BodyVeryLong, RangeType::RealBody, 10, 3.),
            /* real body is short when it's shorter than the average of the 10 previous candles' real bodies */
            new CandleSetting(CandleSettingType::BodyShort, RangeType::RealBody, 10, 1.),
            /* real body is like doji's body when it's shorter than 10% the average of the 10 previous candles' high-low range */
            new CandleSetting(CandleSettingType::BodyDoji, RangeType::HighLow, 10, 0.1),
            /* shadow is long when it's longer than the real body */
            new CandleSetting(CandleSettingType::ShadowLong, RangeType::RealBody, 0, 1.),
            /* shadow is very long when it's longer than 2 times the real body */
            new CandleSetting(CandleSettingType::ShadowVeryLong, RangeType::RealBody, 0, 2.),
            /* shadow is short when it's shorter than half the average of the 10 previous candles' sum of shadows */
            new CandleSetting(CandleSettingType::ShadowShort, RangeType::Shadows, 10, 1.),
            /* shadow is very short when it's shorter than 10% the average of the 10 previous candles' high-low range */
            new CandleSetting(CandleSettingType::ShadowVeryShort, RangeType::HighLow, 10, 0.1),
            /* when measuring distance between parts of candles or width of gaps "near" means "<= 20% of the average of the 5 previous candles' high-low range" */
            new CandleSetting(CandleSettingType::Near, RangeType::HighLow, 5, 0.2),
            /* when measuring distance between parts of candles or width of gaps "far" means ">= 60% of the average of the 5 previous candles' high-low range" */
            new CandleSetting(CandleSettingType::Far, RangeType::HighLow, 5, 0.6),
            /* when measuring distance between parts of candles or width of gaps "equal" means "<= 5% of the average of the 5 previous candles' high-low range" */
            new CandleSetting(CandleSettingType::Equal, RangeType::HighLow, 5, 0.05),
        ];
    }

    public function RestoreCandleDefaultSettings(int $settingType): int
    {
        if ($settingType > CandleSettingType::AllCandleSettings) {
            return RetCode::BadParam;
        }
        if ($settingType === CandleSettingType::AllCandleSettings) {
            for ($i = 0; $i < CandleSettingType::AllCandleSettings; ++$i) {
                $this->candleSettings[$i]->CopyFrom($this->TA_CandleDefaultSettings[$i]);
            }
        } else {
            $this->candleSettings[$settingType]->CopyFrom($this->TA_CandleDefaultSettings[$settingType]);
        }

        return RetCode::Success;
    }

    public function SetUnstablePeriod(int $id, int $period): int
    {
        if ($id >= FuncUnstId::ALL) {
            return RetCode::BadParam;
        }
        $this->unstablePeriod[$id] = $period;

        return RetCode::Success;
    }

    public function GetUnstablePeriod(int $id): int
    {
        return $this->unstablePeriod[$id];
    }

    public function SetCompatibility(int $compatibility)
    {
        $this->compatibility = $compatibility;
    }

    public function getCompatibility(): int
    {
        return $this->compatibility;
    }

}
