/*
	Copyright (c) 2012, Anatoliy Belsky <ab@php.net>
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions
	are met:

	- Redistributions of source code must retain the above copyright
	notice, this list of conditions and the following disclaimer.

	- Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
	"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
	LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
	A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
	HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
	SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
	LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
	DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
	THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
	OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/* $Id$ */

#ifndef TA_PHP_FUNC_H
#define TA_PHP_FUNC_H

PHP_FUNCTION(trader_accbands);
PHP_FUNCTION(trader_acos);
PHP_FUNCTION(trader_ad);
PHP_FUNCTION(trader_add);
PHP_FUNCTION(trader_adosc);
PHP_FUNCTION(trader_adx);
PHP_FUNCTION(trader_adxr);
PHP_FUNCTION(trader_apo);
PHP_FUNCTION(trader_aroon);
PHP_FUNCTION(trader_aroonosc);
PHP_FUNCTION(trader_asin);
PHP_FUNCTION(trader_atan);
PHP_FUNCTION(trader_atr);
PHP_FUNCTION(trader_avgprice);
PHP_FUNCTION(trader_avgdev);
PHP_FUNCTION(trader_bbands);
PHP_FUNCTION(trader_beta);
PHP_FUNCTION(trader_bop);
PHP_FUNCTION(trader_cci);
PHP_FUNCTION(trader_cdl2crows);
PHP_FUNCTION(trader_cdl3blackcrows);
PHP_FUNCTION(trader_cdl3inside);
PHP_FUNCTION(trader_cdl3linestrike);
PHP_FUNCTION(trader_cdl3outside);
PHP_FUNCTION(trader_cdl3starsinsouth);
PHP_FUNCTION(trader_cdl3whitesoldiers);
PHP_FUNCTION(trader_cdlabandonedbaby);
PHP_FUNCTION(trader_cdladvanceblock);
PHP_FUNCTION(trader_cdlbelthold);
PHP_FUNCTION(trader_cdlbreakaway);
PHP_FUNCTION(trader_cdlclosingmarubozu);
PHP_FUNCTION(trader_cdlconcealbabyswall);
PHP_FUNCTION(trader_cdlcounterattack);
PHP_FUNCTION(trader_cdldarkcloudcover);
PHP_FUNCTION(trader_cdldoji);
PHP_FUNCTION(trader_cdldojistar);
PHP_FUNCTION(trader_cdldragonflydoji);
PHP_FUNCTION(trader_cdlengulfing);
PHP_FUNCTION(trader_cdleveningdojistar);
PHP_FUNCTION(trader_cdleveningstar);
PHP_FUNCTION(trader_cdlgapsidesidewhite);
PHP_FUNCTION(trader_cdlgravestonedoji);
PHP_FUNCTION(trader_cdlhammer);
PHP_FUNCTION(trader_cdlhangingman);
PHP_FUNCTION(trader_cdlharami);
PHP_FUNCTION(trader_cdlharamicross);
PHP_FUNCTION(trader_cdlhighwave);
PHP_FUNCTION(trader_cdlhikkake);
PHP_FUNCTION(trader_cdlhikkakemod);
PHP_FUNCTION(trader_cdlhomingpigeon);
PHP_FUNCTION(trader_cdlidentical3crows);
PHP_FUNCTION(trader_cdlinneck);
PHP_FUNCTION(trader_cdlinvertedhammer);
PHP_FUNCTION(trader_cdlkicking);
PHP_FUNCTION(trader_cdlkickingbylength);
PHP_FUNCTION(trader_cdlladderbottom);
PHP_FUNCTION(trader_cdllongleggeddoji);
PHP_FUNCTION(trader_cdllongline);
PHP_FUNCTION(trader_cdlmarubozu);
PHP_FUNCTION(trader_cdlmatchinglow);
PHP_FUNCTION(trader_cdlmathold);
PHP_FUNCTION(trader_cdlmorningdojistar);
PHP_FUNCTION(trader_cdlmorningstar);
PHP_FUNCTION(trader_cdlonneck);
PHP_FUNCTION(trader_cdlpiercing);
PHP_FUNCTION(trader_cdlrickshawman);
PHP_FUNCTION(trader_cdlrisefall3methods);
PHP_FUNCTION(trader_cdlseparatinglines);
PHP_FUNCTION(trader_cdlshootingstar);
PHP_FUNCTION(trader_cdlshortline);
PHP_FUNCTION(trader_cdlspinningtop);
PHP_FUNCTION(trader_cdlstalledpattern);
PHP_FUNCTION(trader_cdlsticksandwich);
PHP_FUNCTION(trader_cdltakuri);
PHP_FUNCTION(trader_cdltasukigap);
PHP_FUNCTION(trader_cdlthrusting);
PHP_FUNCTION(trader_cdltristar);
PHP_FUNCTION(trader_cdlunique3river);
PHP_FUNCTION(trader_cdlupsidegap2crows);
PHP_FUNCTION(trader_cdlxsidegap3methods);
PHP_FUNCTION(trader_ceil);
PHP_FUNCTION(trader_cmo);
PHP_FUNCTION(trader_correl);
PHP_FUNCTION(trader_cos);
PHP_FUNCTION(trader_cosh);
PHP_FUNCTION(trader_dema);
PHP_FUNCTION(trader_div);
PHP_FUNCTION(trader_dx);
PHP_FUNCTION(trader_ema);
PHP_FUNCTION(trader_exp);
PHP_FUNCTION(trader_floor);
PHP_FUNCTION(trader_ht_dcperiod);
PHP_FUNCTION(trader_ht_dcphase);
PHP_FUNCTION(trader_ht_phasor);
PHP_FUNCTION(trader_ht_sine);
PHP_FUNCTION(trader_ht_trendline);
PHP_FUNCTION(trader_ht_trendmode);
PHP_FUNCTION(trader_imi);
PHP_FUNCTION(trader_kama);
PHP_FUNCTION(trader_linearreg);
PHP_FUNCTION(trader_linearreg_angle);
PHP_FUNCTION(trader_linearreg_intercept);
PHP_FUNCTION(trader_linearreg_slope);
PHP_FUNCTION(trader_ln);
PHP_FUNCTION(trader_log10);
PHP_FUNCTION(trader_ma);
PHP_FUNCTION(trader_macd);
PHP_FUNCTION(trader_macdext);
PHP_FUNCTION(trader_macdfix);
PHP_FUNCTION(trader_mama);
PHP_FUNCTION(trader_mavp);
PHP_FUNCTION(trader_max);
PHP_FUNCTION(trader_maxindex);
PHP_FUNCTION(trader_medprice);
PHP_FUNCTION(trader_mfi);
PHP_FUNCTION(trader_midpoint);
PHP_FUNCTION(trader_midprice);
PHP_FUNCTION(trader_min);
PHP_FUNCTION(trader_minindex);
PHP_FUNCTION(trader_minmax);
PHP_FUNCTION(trader_minmaxindex);
PHP_FUNCTION(trader_minus_di);
PHP_FUNCTION(trader_minus_dm);
PHP_FUNCTION(trader_mom);
PHP_FUNCTION(trader_mult);
PHP_FUNCTION(trader_natr);
PHP_FUNCTION(trader_obv);
PHP_FUNCTION(trader_plus_di);
PHP_FUNCTION(trader_plus_dm);
PHP_FUNCTION(trader_ppo);
PHP_FUNCTION(trader_roc);
PHP_FUNCTION(trader_rocp);
PHP_FUNCTION(trader_rocr);
PHP_FUNCTION(trader_rocr100);
PHP_FUNCTION(trader_rsi);
PHP_FUNCTION(trader_sar);
PHP_FUNCTION(trader_sarext);
PHP_FUNCTION(trader_sin);
PHP_FUNCTION(trader_sinh);
PHP_FUNCTION(trader_sma);
PHP_FUNCTION(trader_sqrt);
PHP_FUNCTION(trader_stddev);
PHP_FUNCTION(trader_stoch);
PHP_FUNCTION(trader_stochf);
PHP_FUNCTION(trader_stochrsi);
PHP_FUNCTION(trader_sub);
PHP_FUNCTION(trader_sum);
PHP_FUNCTION(trader_t3);
PHP_FUNCTION(trader_tan);
PHP_FUNCTION(trader_tanh);
PHP_FUNCTION(trader_tema);
PHP_FUNCTION(trader_trange);
PHP_FUNCTION(trader_trima);
PHP_FUNCTION(trader_trix);
PHP_FUNCTION(trader_tsf);
PHP_FUNCTION(trader_typprice);
PHP_FUNCTION(trader_ultosc);
PHP_FUNCTION(trader_var);
PHP_FUNCTION(trader_wclprice);
PHP_FUNCTION(trader_willr);
PHP_FUNCTION(trader_wma);

#endif /* TA_PHP_FUNC_H */

