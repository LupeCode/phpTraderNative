<?php

namespace LupeCode\phpTraderNative;

trait TraderCommon
{

    public static $TRADER_MA_TYPE_SMA                   = 0;
    public static $TRADER_MA_TYPE_EMA                   = 1;
    public static $TRADER_MA_TYPE_WMA                   = 2;
    public static $TRADER_MA_TYPE_DEMA                  = 3;
    public static $TRADER_MA_TYPE_TEMA                  = 4;
    public static $TRADER_MA_TYPE_TRIMA                 = 5;
    public static $TRADER_MA_TYPE_KAMA                  = 6;
    public static $TRADER_MA_TYPE_MAMA                  = 7;
    public static $TRADER_MA_TYPE_T3                    = 8;
    public static $TRADER_REAL_MIN                      = -3.0000000000000002E+37;
    public static $TRADER_REAL_MAX                      = 3.0000000000000002E+37;
    public static $TRADER_FUNC_UNST_ADX                 = 0;
    public static $TRADER_FUNC_UNST_ADXR                = 1;
    public static $TRADER_FUNC_UNST_ATR                 = 2;
    public static $TRADER_FUNC_UNST_CMO                 = 3;
    public static $TRADER_FUNC_UNST_DX                  = 4;
    public static $TRADER_FUNC_UNST_EMA                 = 5;
    public static $TRADER_FUNC_UNST_HT_DCPERIOD         = 6;
    public static $TRADER_FUNC_UNST_HT_DCPHASE          = 7;
    public static $TRADER_FUNC_UNST_HT_PHASOR           = 8;
    public static $TRADER_FUNC_UNST_HT_TRENDLINE        = 10;
    public static $TRADER_FUNC_UNST_HT_TRENDMODE        = 11;
    public static $TRADER_FUNC_UNST_KAMA                = 12;
    public static $TRADER_FUNC_UNST_MAMA                = 13;
    public static $TRADER_FUNC_UNST_MFI                 = 14;
    public static $TRADER_FUNC_UNST_MINUS_DI            = 15;
    public static $TRADER_FUNC_UNST_MINUS_DM            = 16;
    public static $TRADER_FUNC_UNST_NATR                = 17;
    public static $TRADER_FUNC_UNST_PLUS_DI             = 18;
    public static $TRADER_FUNC_UNST_PLUS_DM             = 19;
    public static $TRADER_FUNC_UNST_RSI                 = 20;
    public static $TRADER_FUNC_UNST_STOCHRSI            = 21;
    public static $TRADER_FUNC_UNST_T3                  = 22;
    public static $TRADER_FUNC_UNST_ALL                 = 23;
    public static $TRADER_FUNC_UNST_NONE                = -1;
    public static $TRADER_COMPATIBILITY_DEFAULT         = 0;
    public static $TRADER_COMPATIBILITY_METASTOCK       = 1;
    public static $TRADER_ERR_SUCCESS                   = 0;
    public static $TRADER_ERR_LIB_NOT_INITIALIZE        = 1;
    public static $TRADER_ERR_BAD_PARAM                 = 2;
    public static $TRADER_ERR_ALLOC_ERR                 = 3;
    public static $TRADER_ERR_GROUP_NOT_FOUND           = 4;
    public static $TRADER_ERR_FUNC_NOT_FOUND            = 5;
    public static $TRADER_ERR_INVALID_HANDLE            = 6;
    public static $TRADER_ERR_INVALID_PARAM_HOLDER      = 7;
    public static $TRADER_ERR_INVALID_PARAM_HOLDER_TYPE = 8;
    public static $TRADER_ERR_INVALID_PARAM_FUNCTION    = 9;
    public static $TRADER_ERR_INPUT_NOT_ALL_INITIALIZE  = 10;
    public static $TRADER_ERR_OUTPUT_NOT_ALL_INITIALIZE = 11;
    public static $TRADER_ERR_OUT_OF_RANGE_START_INDEX  = 12;
    public static $TRADER_ERR_OUT_OF_RANGE_END_INDEX    = 13;
    public static $TRADER_ERR_INVALID_LIST_TYPE         = 14;
    public static $TRADER_ERR_BAD_OBJECT                = 15;
    public static $TRADER_ERR_NOT_SUPPORTED             = 16;
    public static $TRADER_ERR_INTERNAL_ERROR            = 5000;
    public static $TRADER_ERR_UNKNOWN_ERROR             = 65535;

    private static function checkForError(int $retCode)
    {
        switch($retCode){
            case RetCode::Success:
                return;
            case RetCode::BadParam:
                throw new \Exception("Bad Parameters");
        }
    }

    /**
     * @param \array[] ...$array
     *
     * @return bool
     * @throws \LupeCode\phpTraderNative\Exception
     */
    private static function compareArrayCount(array ...$array)
    {
        foreach ($array as $arg) {
            if (count($arg) !== count($array[0])) {
                throw new Exception(Exception::TRADER_INPUT_ARRAYS_HAVE_DIFFERENT_COUNTS_MESSAGE, Exception::TRADER_INPUT_ARRAYS_HAVE_DIFFERENT_COUNTS_NUMBER);
            }
        }

        return true;
    }

    private static function PeriodToK(float $periods)
    {
        return 2.0 / $periods + 1;
    }

}