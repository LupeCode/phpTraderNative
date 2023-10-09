<?php

/*
 * This file is part of the LupeCode\phpTraderNative package.
 *
 * (c) LupeCode <joshua@lupecode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @formatter:off
 */

use LupeCode\phpTraderNative\Trader;

if (extension_loaded('trader')) { return; }

\LupeCode\phpTraderNative\TALib\Core\Core::construct();

/*
 * Define any missing Trader constants
 */
// ENUM TRADER_MA_TYPE
if (!defined('TRADER_MA_TYPE_SMA')) { define('TRADER_MA_TYPE_SMA', 0); }
if (!defined('TRADER_MA_TYPE_EMA')) { define('TRADER_MA_TYPE_EMA', 1); }
if (!defined('TRADER_MA_TYPE_WMA')) { define('TRADER_MA_TYPE_WMA', 2); }
if (!defined('TRADER_MA_TYPE_DEMA')) { define('TRADER_MA_TYPE_DEMA', 3); }
if (!defined('TRADER_MA_TYPE_TEMA')) { define('TRADER_MA_TYPE_TEMA', 4); }
if (!defined('TRADER_MA_TYPE_TRIMA')) { define('TRADER_MA_TYPE_TRIMA', 5); }
if (!defined('TRADER_MA_TYPE_KAMA')) { define('TRADER_MA_TYPE_KAMA', 6); }
if (!defined('TRADER_MA_TYPE_MAMA')) { define('TRADER_MA_TYPE_MAMA', 7); }
if (!defined('TRADER_MA_TYPE_T3')) { define('TRADER_MA_TYPE_T3', 8); }

if (!defined('TRADER_REAL_MIN')) { define('TRADER_REAL_MIN', (-3e+37)); }
if (!defined('TRADER_REAL_MAX')) { define('TRADER_REAL_MAX', (3e+37)); }

if (!defined('TRADER_INTEGER_MIN')) { define('TRADER_INTEGER_MIN', (-2147483647 + 1)); }
if (!defined('TRADER_INTEGER_MAX')) { define('TRADER_INTEGER_MAX', (2147483647)); }
// ENUM TRADER_FUNC_UNST
if (!defined('TRADER_FUNC_UNST_ADX')) { define('TRADER_FUNC_UNST_ADX', 0); }
if (!defined('TRADER_FUNC_UNST_ADXR')) { define('TRADER_FUNC_UNST_ADXR', 1); }
if (!defined('TRADER_FUNC_UNST_ATR')) { define('TRADER_FUNC_UNST_ATR', 2); }
if (!defined('TRADER_FUNC_UNST_CMO')) { define('TRADER_FUNC_UNST_CMO', 3); }
if (!defined('TRADER_FUNC_UNST_DX')) { define('TRADER_FUNC_UNST_DX', 4); }
if (!defined('TRADER_FUNC_UNST_EMA')) { define('TRADER_FUNC_UNST_EMA', 5); }
if (!defined('TRADER_FUNC_UNST_HT_DCPERIOD')) { define('TRADER_FUNC_UNST_HT_DCPERIOD', 6); }
if (!defined('TRADER_FUNC_UNST_HT_DCPHASE')) { define('TRADER_FUNC_UNST_HT_DCPHASE', 7); }
if (!defined('TRADER_FUNC_UNST_HT_PHASOR')) { define('TRADER_FUNC_UNST_HT_PHASOR', 8); }
if (!defined('TRADER_FUNC_UNST_HT_SINE')) { define('TRADER_FUNC_UNST_HT_SINE', 9); }
if (!defined('TRADER_FUNC_UNST_HT_TRENDLINE')) { define('TRADER_FUNC_UNST_HT_TRENDLINE', 10); }
if (!defined('TRADER_FUNC_UNST_HT_TRENDMODE')) { define('TRADER_FUNC_UNST_HT_TRENDMODE', 11); }
if (!defined('TRADER_FUNC_UNST_KAMA')) { define('TRADER_FUNC_UNST_KAMA', 12); }
if (!defined('TRADER_FUNC_UNST_MAMA')) { define('TRADER_FUNC_UNST_MAMA', 13); }
if (!defined('TRADER_FUNC_UNST_MFI')) { define('TRADER_FUNC_UNST_MFI', 14); }
if (!defined('TRADER_FUNC_UNST_MINUS_DI')) { define('TRADER_FUNC_UNST_MINUS_DI', 15); }
if (!defined('TRADER_FUNC_UNST_MINUS_DM')) { define('TRADER_FUNC_UNST_MINUS_DM', 16); }
if (!defined('TRADER_FUNC_UNST_NATR')) { define('TRADER_FUNC_UNST_NATR', 17); }
if (!defined('TRADER_FUNC_UNST_PLUS_DI')) { define('TRADER_FUNC_UNST_PLUS_DI', 18); }
if (!defined('TRADER_FUNC_UNST_PLUS_DM')) { define('TRADER_FUNC_UNST_PLUS_DM', 19); }
if (!defined('TRADER_FUNC_UNST_RSI')) { define('TRADER_FUNC_UNST_RSI', 20); }
if (!defined('TRADER_FUNC_UNST_STOCHRSI')) { define('TRADER_FUNC_UNST_STOCHRSI', 21); }
if (!defined('TRADER_FUNC_UNST_T3')) { define('TRADER_FUNC_UNST_T3', 22); }
if (!defined('TRADER_FUNC_UNST_ALL')) { define('TRADER_FUNC_UNST_ALL', 23); }
if (!defined('TRADER_FUNC_UNST_NONE')) { define('TRADER_FUNC_UNST_NONE', -1); }
// ENUM TRADER_COMPATIBILITY
if (!defined('TRADER_COMPATIBILITY_DEFAULT')) { define('TRADER_COMPATIBILITY_DEFAULT', 0); }
if (!defined('TRADER_COMPATIBILITY_METASTOCK')) { define('TRADER_COMPATIBILITY_METASTOCK', 1); }
// ENUM TRADER_ERR
if (!defined('TRADER_ERR_SUCCESS')) { define('TRADER_ERR_SUCCESS', 0); }
if (!defined('TRADER_ERR_LIB_NOT_INITIALIZE')) { define('TRADER_ERR_LIB_NOT_INITIALIZE', 1); }
if (!defined('TRADER_ERR_BAD_PARAM')) { define('TRADER_ERR_BAD_PARAM', 2); }
if (!defined('TRADER_ERR_ALLOC_ERR')) { define('TRADER_ERR_ALLOC_ERR', 3); }
if (!defined('TRADER_ERR_GROUP_NOT_FOUND')) { define('TRADER_ERR_GROUP_NOT_FOUND', 4); }
if (!defined('TRADER_ERR_FUNC_NOT_FOUND')) { define('TRADER_ERR_FUNC_NOT_FOUND', 5); }
if (!defined('TRADER_ERR_INVALID_HANDLE')) { define('TRADER_ERR_INVALID_HANDLE', 6); }
if (!defined('TRADER_ERR_INVALID_PARAM_HOLDER')) { define('TRADER_ERR_INVALID_PARAM_HOLDER', 7); }
if (!defined('TRADER_ERR_INVALID_PARAM_HOLDER_TYPE')) { define('TRADER_ERR_INVALID_PARAM_HOLDER_TYPE', 8); }
if (!defined('TRADER_ERR_INVALID_PARAM_FUNCTION')) { define('TRADER_ERR_INVALID_PARAM_FUNCTION', 9); }
if (!defined('TRADER_ERR_INPUT_NOT_ALL_INITIALIZE')) { define('TRADER_ERR_INPUT_NOT_ALL_INITIALIZE', 10); }
if (!defined('TRADER_ERR_OUTPUT_NOT_ALL_INITIALIZE')) { define('TRADER_ERR_OUTPUT_NOT_ALL_INITIALIZE', 11); }
if (!defined('TRADER_ERR_OUT_OF_RANGE_START_INDEX')) { define('TRADER_ERR_OUT_OF_RANGE_START_INDEX', 12); }
if (!defined('TRADER_ERR_OUT_OF_RANGE_END_INDEX')) { define('TRADER_ERR_OUT_OF_RANGE_END_INDEX', 13); }
if (!defined('TRADER_ERR_INVALID_LIST_TYPE')) { define('TRADER_ERR_INVALID_LIST_TYPE', 14); }
if (!defined('TRADER_ERR_BAD_OBJECT')) { define('TRADER_ERR_BAD_OBJECT', 15); }
if (!defined('TRADER_ERR_NOT_SUPPORTED')) { define('TRADER_ERR_NOT_SUPPORTED', 16); }
if (!defined('TRADER_ERR_INTERNAL_ERROR')) { define('TRADER_ERR_INTERNAL_ERROR', 5000); }
if (!defined('TRADER_ERR_UNKNOWN_ERROR')) { define('TRADER_ERR_UNKNOWN_ERROR', 65535); }

/*
 * Define any missing Trader functions
 */
if (!function_exists('trader_set_unstable_period')) { function trader_set_unstable_period($functionId, $timePeriod): void { Trader::set_unstable_period($functionId, $timePeriod); } }
if (!function_exists('trader_get_unstable_period')) { function trader_get_unstable_period($functionId): int { return Trader::get_unstable_period($functionId); } }
if (!function_exists('trader_set_compat')) { function trader_set_compat($compatId): void { Trader::set_compat($compatId); } }
if (!function_exists('trader_get_compat')) { function trader_get_compat(): int { return Trader::get_compat(); } }

if (!function_exists('trader_acos')) { function trader_acos($real): array { return Trader::acos($real); } }
if (!function_exists('trader_ad')) { function trader_ad($high, $low, $close, $volume): array { return Trader::ad($high, $low, $close, $volume); } }
if (!function_exists('trader_add')) { function trader_add($real0, $real1): array { return Trader::add($real0, $real1); } }
if (!function_exists('trader_adosc')) { function trader_adosc($high, $low, $close, $volume, $fastPeriod = NULL, $slowPeriod = NULL): array { return Trader::adosc($high, $low, $close, $volume, $fastPeriod, $slowPeriod); } }
if (!function_exists('trader_adx')) { function trader_adx($high, $low, $close, $timePeriod = NULL): array { return Trader::adx($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_adxr')) { function trader_adxr($high, $low, $close, $timePeriod = NULL): array { return Trader::adxr($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_apo')) { function trader_apo($real, $fastPeriod = NULL, $slowPeriod = NULL, $mAType = NULL): array { return Trader::apo($real, $fastPeriod, $slowPeriod, $mAType); } }
if (!function_exists('trader_aroon')) { function trader_aroon($high, $low, $timePeriod = NULL): array { return Trader::aroon($high, $low, $timePeriod); } }
if (!function_exists('trader_aroonosc')) { function trader_aroonosc($high, $low, $timePeriod = NULL): array { return Trader::aroonosc($high, $low, $timePeriod); } }
if (!function_exists('trader_asin')) { function trader_asin($real): array { return Trader::asin($real); } }
if (!function_exists('trader_atan')) { function trader_atan($real): array { return Trader::atan($real); } }
if (!function_exists('trader_atr')) { function trader_atr($high, $low, $close, $timePeriod = NULL): array { return Trader::atr($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_avgprice')) { function trader_avgprice($open, $high, $low, $close): array { return Trader::avgprice($open, $high, $low, $close); } }
if (!function_exists('trader_bbands')) { function trader_bbands($real, $timePeriod = NULL, $nbDevUp = NULL, $nbDevDn = NULL, $mAType = NULL): array { return Trader::bbands($real, $timePeriod, $nbDevUp, $nbDevDn, $mAType); } }
if (!function_exists('trader_beta')) { function trader_beta($real0, $real1, $timePeriod = NULL): array { return Trader::beta($real0, $real1, $timePeriod); } }
if (!function_exists('trader_bop')) { function trader_bop($open, $high, $low, $close): array { return Trader::bop($open, $high, $low, $close); } }
if (!function_exists('trader_cci')) { function trader_cci($high, $low, $close, $timePeriod = NULL): array { return Trader::cci($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_cdl2crows')) { function trader_cdl2crows($open, $high, $low, $close): array { return Trader::cdl2crows($open, $high, $low, $close); } }
if (!function_exists('trader_cdl3blackcrows')) { function trader_cdl3blackcrows($open, $high, $low, $close): array { return Trader::cdl3blackcrows($open, $high, $low, $close); } }
if (!function_exists('trader_cdl3inside')) { function trader_cdl3inside($open, $high, $low, $close): array { return Trader::cdl3inside($open, $high, $low, $close); } }
if (!function_exists('trader_cdl3linestrike')) { function trader_cdl3linestrike($open, $high, $low, $close): array { return Trader::cdl3linestrike($open, $high, $low, $close); } }
if (!function_exists('trader_cdl3outside')) { function trader_cdl3outside($open, $high, $low, $close): array { return Trader::cdl3outside($open, $high, $low, $close); } }
if (!function_exists('trader_cdl3starsinsouth')) { function trader_cdl3starsinsouth($open, $high, $low, $close): array { return Trader::cdl3starsinsouth($open, $high, $low, $close); } }
if (!function_exists('trader_cdl3whitesoldiers')) { function trader_cdl3whitesoldiers($open, $high, $low, $close): array { return Trader::cdl3whitesoldiers($open, $high, $low, $close); } }
if (!function_exists('trader_cdlabandonedbaby')) { function trader_cdlabandonedbaby($open, $high, $low, $close, $penetration = NULL): array { return Trader::cdlabandonedbaby($open, $high, $low, $close, $penetration); } }
if (!function_exists('trader_cdladvanceblock')) { function trader_cdladvanceblock($open, $high, $low, $close): array { return Trader::cdladvanceblock($open, $high, $low, $close); } }
if (!function_exists('trader_cdlbelthold')) { function trader_cdlbelthold($open, $high, $low, $close): array { return Trader::cdlbelthold($open, $high, $low, $close); } }
if (!function_exists('trader_cdlbreakaway')) { function trader_cdlbreakaway($open, $high, $low, $close): array { return Trader::cdlbreakaway($open, $high, $low, $close); } }
if (!function_exists('trader_cdlclosingmarubozu')) { function trader_cdlclosingmarubozu($open, $high, $low, $close): array { return Trader::cdlclosingmarubozu($open, $high, $low, $close); } }
if (!function_exists('trader_cdlconcealbabyswall')) { function trader_cdlconcealbabyswall($open, $high, $low, $close): array { return Trader::cdlconcealbabyswall($open, $high, $low, $close); } }
if (!function_exists('trader_cdlcounterattack')) { function trader_cdlcounterattack($open, $high, $low, $close): array { return Trader::cdlcounterattack($open, $high, $low, $close); } }
if (!function_exists('trader_cdldarkcloudcover')) { function trader_cdldarkcloudcover($open, $high, $low, $close, $penetration = NULL): array { return Trader::cdldarkcloudcover($open, $high, $low, $close, $penetration); } }
if (!function_exists('trader_cdldoji')) { function trader_cdldoji($open, $high, $low, $close): array { return Trader::cdldoji($open, $high, $low, $close); } }
if (!function_exists('trader_cdldojistar')) { function trader_cdldojistar($open, $high, $low, $close): array { return Trader::cdldojistar($open, $high, $low, $close); } }
if (!function_exists('trader_cdldragonflydoji')) { function trader_cdldragonflydoji($open, $high, $low, $close): array { return Trader::cdldragonflydoji($open, $high, $low, $close); } }
if (!function_exists('trader_cdlengulfing')) { function trader_cdlengulfing($open, $high, $low, $close): array { return Trader::cdlengulfing($open, $high, $low, $close); } }
if (!function_exists('trader_cdleveningdojistar')) { function trader_cdleveningdojistar($open, $high, $low, $close, $penetration = NULL): array { return Trader::cdleveningdojistar($open, $high, $low, $close, $penetration); } }
if (!function_exists('trader_cdleveningstar')) { function trader_cdleveningstar($open, $high, $low, $close, $penetration = NULL): array { return Trader::cdleveningstar($open, $high, $low, $close, $penetration); } }
if (!function_exists('trader_cdlgapsidesidewhite')) { function trader_cdlgapsidesidewhite($open, $high, $low, $close): array { return Trader::cdlgapsidesidewhite($open, $high, $low, $close); } }
if (!function_exists('trader_cdlgravestonedoji')) { function trader_cdlgravestonedoji($open, $high, $low, $close): array { return Trader::cdlgravestonedoji($open, $high, $low, $close); } }
if (!function_exists('trader_cdlhammer')) { function trader_cdlhammer($open, $high, $low, $close): array { return Trader::cdlhammer($open, $high, $low, $close); } }
if (!function_exists('trader_cdlhangingman')) { function trader_cdlhangingman($open, $high, $low, $close): array { return Trader::cdlhangingman($open, $high, $low, $close); } }
if (!function_exists('trader_cdlharami')) { function trader_cdlharami($open, $high, $low, $close): array { return Trader::cdlharami($open, $high, $low, $close); } }
if (!function_exists('trader_cdlharamicross')) { function trader_cdlharamicross($open, $high, $low, $close): array { return Trader::cdlharamicross($open, $high, $low, $close); } }
if (!function_exists('trader_cdlhighwave')) { function trader_cdlhighwave($open, $high, $low, $close): array { return Trader::cdlhighwave($open, $high, $low, $close); } }
if (!function_exists('trader_cdlhikkake')) { function trader_cdlhikkake($open, $high, $low, $close): array { return Trader::cdlhikkake($open, $high, $low, $close); } }
if (!function_exists('trader_cdlhikkakemod')) { function trader_cdlhikkakemod($open, $high, $low, $close): array { return Trader::cdlhikkakemod($open, $high, $low, $close); } }
if (!function_exists('trader_cdlhomingpigeon')) { function trader_cdlhomingpigeon($open, $high, $low, $close): array { return Trader::cdlhomingpigeon($open, $high, $low, $close); } }
if (!function_exists('trader_cdlidentical3crows')) { function trader_cdlidentical3crows($open, $high, $low, $close): array { return Trader::cdlidentical3crows($open, $high, $low, $close); } }
if (!function_exists('trader_cdlinneck')) { function trader_cdlinneck($open, $high, $low, $close): array { return Trader::cdlinneck($open, $high, $low, $close); } }
if (!function_exists('trader_cdlinvertedhammer')) { function trader_cdlinvertedhammer($open, $high, $low, $close): array { return Trader::cdlinvertedhammer($open, $high, $low, $close); } }
if (!function_exists('trader_cdlkicking')) { function trader_cdlkicking($open, $high, $low, $close): array { return Trader::cdlkicking($open, $high, $low, $close); } }
if (!function_exists('trader_cdlkickingbylength')) { function trader_cdlkickingbylength($open, $high, $low, $close): array { return Trader::cdlkickingbylength($open, $high, $low, $close); } }
if (!function_exists('trader_cdlladderbottom')) { function trader_cdlladderbottom($open, $high, $low, $close): array { return Trader::cdlladderbottom($open, $high, $low, $close); } }
if (!function_exists('trader_cdllongleggeddoji')) { function trader_cdllongleggeddoji($open, $high, $low, $close): array { return Trader::cdllongleggeddoji($open, $high, $low, $close); } }
if (!function_exists('trader_cdllongline')) { function trader_cdllongline($open, $high, $low, $close): array { return Trader::cdllongline($open, $high, $low, $close); } }
if (!function_exists('trader_cdlmarubozu')) { function trader_cdlmarubozu($open, $high, $low, $close): array { return Trader::cdlmarubozu($open, $high, $low, $close); } }
if (!function_exists('trader_cdlmatchinglow')) { function trader_cdlmatchinglow($open, $high, $low, $close): array { return Trader::cdlmatchinglow($open, $high, $low, $close); } }
if (!function_exists('trader_cdlmathold')) { function trader_cdlmathold($open, $high, $low, $close, $penetration = NULL): array { return Trader::cdlmathold($open, $high, $low, $close, $penetration); } }
if (!function_exists('trader_cdlmorningdojistar')) { function trader_cdlmorningdojistar($open, $high, $low, $close, $penetration = NULL): array { return Trader::cdlmorningdojistar($open, $high, $low, $close, $penetration); } }
if (!function_exists('trader_cdlmorningstar')) { function trader_cdlmorningstar($open, $high, $low, $close, $penetration = NULL): array { return Trader::cdlmorningstar($open, $high, $low, $close, $penetration); } }
if (!function_exists('trader_cdlonneck')) { function trader_cdlonneck($open, $high, $low, $close): array { return Trader::cdlonneck($open, $high, $low, $close); } }
if (!function_exists('trader_cdlpiercing')) { function trader_cdlpiercing($open, $high, $low, $close): array { return Trader::cdlpiercing($open, $high, $low, $close); } }
if (!function_exists('trader_cdlrickshawman')) { function trader_cdlrickshawman($open, $high, $low, $close): array { return Trader::cdlrickshawman($open, $high, $low, $close); } }
if (!function_exists('trader_cdlrisefall3methods')) { function trader_cdlrisefall3methods($open, $high, $low, $close): array { return Trader::cdlrisefall3methods($open, $high, $low, $close); } }
if (!function_exists('trader_cdlseparatinglines')) { function trader_cdlseparatinglines($open, $high, $low, $close): array { return Trader::cdlseparatinglines($open, $high, $low, $close); } }
if (!function_exists('trader_cdlshootingstar')) { function trader_cdlshootingstar($open, $high, $low, $close): array { return Trader::cdlshootingstar($open, $high, $low, $close); } }
if (!function_exists('trader_cdlshortline')) { function trader_cdlshortline($open, $high, $low, $close): array { return Trader::cdlshortline($open, $high, $low, $close); } }
if (!function_exists('trader_cdlspinningtop')) { function trader_cdlspinningtop($open, $high, $low, $close): array { return Trader::cdlspinningtop($open, $high, $low, $close); } }
if (!function_exists('trader_cdlstalledpattern')) { function trader_cdlstalledpattern($open, $high, $low, $close): array { return Trader::cdlstalledpattern($open, $high, $low, $close); } }
if (!function_exists('trader_cdlsticksandwich')) { function trader_cdlsticksandwich($open, $high, $low, $close): array { return Trader::cdlsticksandwich($open, $high, $low, $close); } }
if (!function_exists('trader_cdltakuri')) { function trader_cdltakuri($open, $high, $low, $close): array { return Trader::cdltakuri($open, $high, $low, $close); } }
if (!function_exists('trader_cdltasukigap')) { function trader_cdltasukigap($open, $high, $low, $close): array { return Trader::cdltasukigap($open, $high, $low, $close); } }
if (!function_exists('trader_cdlthrusting')) { function trader_cdlthrusting($open, $high, $low, $close): array { return Trader::cdlthrusting($open, $high, $low, $close); } }
if (!function_exists('trader_cdltristar')) { function trader_cdltristar($open, $high, $low, $close): array { return Trader::cdltristar($open, $high, $low, $close); } }
if (!function_exists('trader_cdlunique3river')) { function trader_cdlunique3river($open, $high, $low, $close): array { return Trader::cdlunique3river($open, $high, $low, $close); } }
if (!function_exists('trader_cdlupsidegap2crows')) { function trader_cdlupsidegap2crows($open, $high, $low, $close): array { return Trader::cdlupsidegap2crows($open, $high, $low, $close); } }
if (!function_exists('trader_cdlxsidegap3methods')) { function trader_cdlxsidegap3methods($open, $high, $low, $close): array { return Trader::cdlxsidegap3methods($open, $high, $low, $close); } }
if (!function_exists('trader_ceil')) { function trader_ceil($real): array { return Trader::ceil($real); } }
if (!function_exists('trader_cmo')) { function trader_cmo($real, $timePeriod = NULL): array { return Trader::cmo($real, $timePeriod); } }
if (!function_exists('trader_correl')) { function trader_correl($real0, $real1, $timePeriod = NULL): array { return Trader::correl($real0, $real1, $timePeriod); } }
if (!function_exists('trader_cos')) { function trader_cos($real): array { return Trader::cos($real); } }
if (!function_exists('trader_cosh')) { function trader_cosh($real): array { return Trader::cosh($real); } }
if (!function_exists('trader_dema')) { function trader_dema($real, $timePeriod = NULL): array { return Trader::dema($real, $timePeriod); } }
if (!function_exists('trader_div')) { function trader_div($real0, $real1): array { return Trader::div($real0, $real1); } }
if (!function_exists('trader_dx')) { function trader_dx($high, $low, $close, $timePeriod = NULL): array { return Trader::dx($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_ema')) { function trader_ema($real, $timePeriod = NULL): array { return Trader::ema($real, $timePeriod); } }
if (!function_exists('trader_exp')) { function trader_exp($real): array { return Trader::exp($real); } }
if (!function_exists('trader_floor')) { function trader_floor($real): array { return Trader::floor($real); } }
if (!function_exists('trader_ht_dcperiod')) { function trader_ht_dcperiod($real): array { return Trader::ht_dcperiod($real); } }
if (!function_exists('trader_ht_dcphase')) { function trader_ht_dcphase($real): array { return Trader::ht_dcphase($real); } }
if (!function_exists('trader_ht_phasor')) { function trader_ht_phasor($real): array { return Trader::ht_phasor($real); } }
if (!function_exists('trader_ht_sine')) { function trader_ht_sine($real): array { return Trader::ht_sine($real); } }
if (!function_exists('trader_ht_trendline')) { function trader_ht_trendline($real): array { return Trader::ht_trendline($real); } }
if (!function_exists('trader_ht_trendmode')) { function trader_ht_trendmode($real): array { return Trader::ht_trendmode($real); } }
if (!function_exists('trader_kama')) { function trader_kama($real, $timePeriod = NULL): array { return Trader::kama($real, $timePeriod); } }
if (!function_exists('trader_linearreg')) { function trader_linearreg($real, $timePeriod = NULL): array { return Trader::linearreg($real, $timePeriod); } }
if (!function_exists('trader_linearreg_angle')) { function trader_linearreg_angle($real, $timePeriod = NULL): array { return Trader::linearreg_angle($real, $timePeriod); } }
if (!function_exists('trader_linearreg_intercept')) { function trader_linearreg_intercept($real, $timePeriod = NULL): array { return Trader::linearreg_intercept($real, $timePeriod); } }
if (!function_exists('trader_linearreg_slope')) { function trader_linearreg_slope($real, $timePeriod = NULL): array { return Trader::linearreg_slope($real, $timePeriod); } }
if (!function_exists('trader_ln')) { function trader_ln($real): array { return Trader::ln($real); } }
if (!function_exists('trader_log10')) { function trader_log10($real): array { return Trader::log10($real); } }
if (!function_exists('trader_ma')) { function trader_ma($real, $timePeriod = NULL, $mAType = NULL): array { return Trader::ma($real, $timePeriod, $mAType); } }
if (!function_exists('trader_macd')) { function trader_macd($real, $fastPeriod = NULL, $slowPeriod = NULL, $signalPeriod = NULL): array { return Trader::macd($real, $fastPeriod, $slowPeriod, $signalPeriod); } }
if (!function_exists('trader_macdext')) { function trader_macdext($real, $fastPeriod = NULL, $fastMAType = NULL, $slowPeriod = NULL, $slowMAType = NULL, $signalPeriod = NULL, $signalMAType = NULL): array { return Trader::macdext($real, $fastPeriod, $fastMAType, $slowPeriod, $slowMAType, $signalPeriod, $signalMAType); } }
if (!function_exists('trader_macdfix')) { function trader_macdfix($real, $signalPeriod = NULL): array { return Trader::macdfix($real, $signalPeriod); } }
if (!function_exists('trader_mama')) { function trader_mama($real, $fastLimit = NULL, $slowLimit = NULL): array { return Trader::mama($real, $fastLimit, $slowLimit); } }
if (!function_exists('trader_mavp')) { function trader_mavp($real, $periods, $minPeriod = NULL, $maxPeriod = NULL, $mAType = NULL): array { return Trader::mavp($real, $periods, $minPeriod, $maxPeriod, $mAType); } }
if (!function_exists('trader_max')) { function trader_max($real, $timePeriod = NULL): array { return Trader::max($real, $timePeriod); } }
if (!function_exists('trader_maxindex')) { function trader_maxindex($real, $timePeriod = NULL): array { return Trader::maxindex($real, $timePeriod); } }
if (!function_exists('trader_medprice')) { function trader_medprice($high, $low): array { return Trader::medprice($high, $low); } }
if (!function_exists('trader_mfi')) { function trader_mfi($high, $low, $close, $volume, $timePeriod = NULL): array { return Trader::mfi($high, $low, $close, $volume, $timePeriod); } }
if (!function_exists('trader_midpoint')) { function trader_midpoint($real, $timePeriod = NULL): array { return Trader::midpoint($real, $timePeriod); } }
if (!function_exists('trader_midprice')) { function trader_midprice($high, $low, $timePeriod = NULL): array { return Trader::midprice($high, $low, $timePeriod); } }
if (!function_exists('trader_min')) { function trader_min($real, $timePeriod = NULL): array { return Trader::min($real, $timePeriod); } }
if (!function_exists('trader_minindex')) { function trader_minindex($real, $timePeriod = NULL): array { return Trader::minindex($real, $timePeriod); } }
if (!function_exists('trader_minmax')) { function trader_minmax($real, $timePeriod = NULL): array { return Trader::minmax($real, $timePeriod); } }
if (!function_exists('trader_minmaxindex')) { function trader_minmaxindex($real, $timePeriod = NULL): array { return Trader::minmaxindex($real, $timePeriod); } }
if (!function_exists('trader_minus_di')) { function trader_minus_di($high, $low, $close, $timePeriod = NULL): array { return Trader::minus_di($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_minus_dm')) { function trader_minus_dm($high, $low, $timePeriod = NULL): array { return Trader::minus_dm($high, $low, $timePeriod); } }
if (!function_exists('trader_mom')) { function trader_mom($real, $timePeriod = NULL): array { return Trader::mom($real, $timePeriod); } }
if (!function_exists('trader_mult')) { function trader_mult($real0, $real1): array { return Trader::mult($real0, $real1); } }
if (!function_exists('trader_natr')) { function trader_natr($high, $low, $close, $timePeriod = NULL): array { return Trader::natr($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_obv')) { function trader_obv($real, $volume): array { return Trader::obv($real, $volume); } }
if (!function_exists('trader_plus_di')) { function trader_plus_di($high, $low, $close, $timePeriod = NULL): array { return Trader::plus_di($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_plus_dm')) { function trader_plus_dm($high, $low, $timePeriod = NULL): array { return Trader::plus_dm($high, $low, $timePeriod); } }
if (!function_exists('trader_ppo')) { function trader_ppo($real, $fastPeriod = NULL, $slowPeriod = NULL, $mAType = NULL): array { return Trader::ppo($real, $fastPeriod, $slowPeriod, $mAType); } }
if (!function_exists('trader_roc')) { function trader_roc($real, $timePeriod = NULL): array { return Trader::roc($real, $timePeriod); } }
if (!function_exists('trader_rocp')) { function trader_rocp($real, $timePeriod = NULL): array { return Trader::rocp($real, $timePeriod); } }
if (!function_exists('trader_rocr')) { function trader_rocr($real, $timePeriod = NULL): array { return Trader::rocr($real, $timePeriod); } }
if (!function_exists('trader_rocr100')) { function trader_rocr100($real, $timePeriod = NULL): array { return Trader::rocr100($real, $timePeriod); } }
if (!function_exists('trader_rsi')) { function trader_rsi($real, $timePeriod = NULL): array { return Trader::rsi($real, $timePeriod); } }
if (!function_exists('trader_sar')) { function trader_sar($high, $low, $acceleration = NULL, $maximum = NULL): array { return Trader::sar($high, $low, $acceleration, $maximum); } }
if (!function_exists('trader_sarext')) { function trader_sarext($high, $low, $startValue = NULL, $offsetOnReverse = NULL, $accelerationInitLong = NULL, $accelerationLong = NULL, $accelerationMaxLong = NULL, $accelerationInitShort = NULL, $accelerationShort = NULL, $accelerationMaxShort = NULL): array { return Trader::sarext($high, $low, $startValue, $offsetOnReverse, $accelerationInitLong, $accelerationLong, $accelerationMaxLong, $accelerationInitShort, $accelerationShort, $accelerationMaxShort); } }
if (!function_exists('trader_sin')) { function trader_sin($real): array { return Trader::sin($real); } }
if (!function_exists('trader_sinh')) { function trader_sinh($real): array { return Trader::sinh($real); } }
if (!function_exists('trader_sma')) { function trader_sma($real, $timePeriod = NULL): array { return Trader::sma($real, $timePeriod); } }
if (!function_exists('trader_sqrt')) { function trader_sqrt($real): array { return Trader::sqrt($real); } }
if (!function_exists('trader_stddev')) { function trader_stddev($real, $timePeriod = NULL, $nbDev = NULL): array { return Trader::stddev($real, $timePeriod, $nbDev); } }
if (!function_exists('trader_stoch')) { function trader_stoch($high, $low, $close, $fastK_Period = NULL, $slowK_Period = NULL, $slowK_MAType = NULL, $slowD_Period = NULL, $slowD_MAType = NULL): array { return Trader::stoch($high, $low, $close, $fastK_Period, $slowK_Period, $slowK_MAType, $slowD_Period, $slowD_MAType); } }
if (!function_exists('trader_stochf')) { function trader_stochf($high, $low, $close, $fastK_Period = NULL, $fastD_Period = NULL, $fastD_MAType = NULL): array { return Trader::stochf($high, $low, $close, $fastK_Period, $fastD_Period, $fastD_MAType); } }
if (!function_exists('trader_stochrsi')) { function trader_stochrsi($real, $timePeriod = NULL, $fastK_Period = NULL, $fastD_Period = NULL, $fastD_MAType = NULL): array { return Trader::stochrsi($real, $timePeriod, $fastK_Period, $fastD_Period, $fastD_MAType); } }
if (!function_exists('trader_sub')) { function trader_sub($real0, $real1): array { return Trader::sub($real0, $real1); } }
if (!function_exists('trader_sum')) { function trader_sum($real, $timePeriod = NULL): array { return Trader::sum($real, $timePeriod); } }
if (!function_exists('trader_t3')) { function trader_t3($real, $timePeriod = NULL, $vFactor = NULL): array { return Trader::t3($real, $timePeriod, $vFactor); } }
if (!function_exists('trader_tan')) { function trader_tan($real): array { return Trader::tan($real); } }
if (!function_exists('trader_tanh')) { function trader_tanh($real): array { return Trader::tanh($real); } }
if (!function_exists('trader_tema')) { function trader_tema($real, $timePeriod = NULL): array { return Trader::tema($real, $timePeriod); } }
if (!function_exists('trader_trange')) { function trader_trange($high, $low, $close): array { return Trader::trange($high, $low, $close); } }
if (!function_exists('trader_trima')) { function trader_trima($real, $timePeriod = NULL): array { return Trader::trima($real, $timePeriod); } }
if (!function_exists('trader_trix')) { function trader_trix($real, $timePeriod = NULL): array { return Trader::trix($real, $timePeriod); } }
if (!function_exists('trader_tsf')) { function trader_tsf($real, $timePeriod = NULL): array { return Trader::tsf($real, $timePeriod); } }
if (!function_exists('trader_typprice')) { function trader_typprice($high, $low, $close): array { return Trader::typprice($high, $low, $close); } }
if (!function_exists('trader_ultosc')) { function trader_ultosc($high, $low, $close, $timePeriod1 = NULL, $timePeriod2 = NULL, $timePeriod3 = NULL): array { return Trader::ultosc($high, $low, $close, $timePeriod1, $timePeriod2, $timePeriod3); } }
if (!function_exists('trader_var')) { function trader_var($real, $timePeriod = NULL, $nbDev = NULL): array { return Trader::var($real, $timePeriod, $nbDev); } }
if (!function_exists('trader_wclprice')) { function trader_wclprice($high, $low, $close): array { return Trader::wclprice($high, $low, $close); } }
if (!function_exists('trader_willr')) { function trader_willr($high, $low, $close, $timePeriod = NULL): array { return Trader::willr($high, $low, $close, $timePeriod); } }
if (!function_exists('trader_wma')) { function trader_wma($real, $timePeriod = NULL): array { return Trader::wma($real, $timePeriod); } }
