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

#ifndef TA_PHP_ARGINFO_H
#define TA_PHP_ARGINFO_H

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_accbands, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_acos, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ad, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_ARRAY_INFO(0,  volume, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_add, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real0, 0)
	ZEND_ARG_ARRAY_INFO(0,  real1, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_adosc, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_ARRAY_INFO(0,  volume, 0)
	ZEND_ARG_INFO(0,  fastPeriod)
	ZEND_ARG_INFO(0,  slowPeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_adx, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_adxr, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_apo, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  fastPeriod)
	ZEND_ARG_INFO(0,  slowPeriod)
	ZEND_ARG_INFO(0,  mAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_aroon, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_aroonosc, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_asin, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_atan, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_atr, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_avgprice, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_avgdev, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_bbands, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
	ZEND_ARG_INFO(0,  nbDevUp)
	ZEND_ARG_INFO(0,  nbDevDn)
	ZEND_ARG_INFO(0,  mAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_beta, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real0, 0)
	ZEND_ARG_ARRAY_INFO(0,  real1, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_bop, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cci, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdl2crows, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdl3blackcrows, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdl3inside, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdl3linestrike, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdl3outside, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdl3starsinsouth, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdl3whitesoldiers, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlabandonedbaby, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  penetration)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdladvanceblock, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlbelthold, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlbreakaway, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlclosingmarubozu, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlconcealbabyswall, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlcounterattack, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdldarkcloudcover, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  penetration)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdldoji, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdldojistar, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdldragonflydoji, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlengulfing, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdleveningdojistar, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  penetration)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdleveningstar, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  penetration)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlgapsidesidewhite, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlgravestonedoji, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlhammer, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlhangingman, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlharami, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlharamicross, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlhighwave, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlhikkake, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlhikkakemod, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlhomingpigeon, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlidentical3crows, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlinneck, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlinvertedhammer, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlkicking, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlkickingbylength, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlladderbottom, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdllongleggeddoji, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdllongline, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlmarubozu, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlmatchinglow, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlmathold, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  penetration)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlmorningdojistar, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  penetration)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlmorningstar, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  penetration)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlonneck, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlpiercing, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlrickshawman, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlrisefall3methods, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlseparatinglines, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlshootingstar, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlshortline, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlspinningtop, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlstalledpattern, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlsticksandwich, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdltakuri, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdltasukigap, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlthrusting, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdltristar, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlunique3river, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlupsidegap2crows, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cdlxsidegap3methods, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ceil, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cmo, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_correl, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real0, 0)
	ZEND_ARG_ARRAY_INFO(0,  real1, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cos, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_cosh, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_dema, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_div, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real0, 0)
	ZEND_ARG_ARRAY_INFO(0,  real1, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_dx, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ema, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_exp, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_floor, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ht_dcperiod, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ht_dcphase, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ht_phasor, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ht_sine, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ht_trendline, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ht_trendmode, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_imi, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  open, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_kama, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_linearreg, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_linearreg_angle, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_linearreg_intercept, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_linearreg_slope, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ln, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_log10, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ma, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
	ZEND_ARG_INFO(0,  mAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_macd, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  fastPeriod)
	ZEND_ARG_INFO(0,  slowPeriod)
	ZEND_ARG_INFO(0,  signalPeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_macdext, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  fastPeriod)
	ZEND_ARG_INFO(0,  fastMAType)
	ZEND_ARG_INFO(0,  slowPeriod)
	ZEND_ARG_INFO(0,  slowMAType)
	ZEND_ARG_INFO(0,  signalPeriod)
	ZEND_ARG_INFO(0,  signalMAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_macdfix, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  signalPeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_mama, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  fastLimit)
	ZEND_ARG_INFO(0,  slowLimit)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_mavp, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_ARRAY_INFO(0,  periods, 0)
	ZEND_ARG_INFO(0,  minPeriod)
	ZEND_ARG_INFO(0,  maxPeriod)
	ZEND_ARG_INFO(0,  mAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_max, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_maxindex, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_medprice, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_mfi, 0, 0, 4)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_ARRAY_INFO(0,  volume, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_midpoint, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_midprice, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_min, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_minindex, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_minmax, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_minmaxindex, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_minus_di, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_minus_dm, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_mom, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_mult, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real0, 0)
	ZEND_ARG_ARRAY_INFO(0,  real1, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_natr, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_obv, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_ARRAY_INFO(0,  volume, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_plus_di, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_plus_dm, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ppo, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  fastPeriod)
	ZEND_ARG_INFO(0,  slowPeriod)
	ZEND_ARG_INFO(0,  mAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_roc, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_rocp, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_rocr, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_rocr100, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_rsi, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sar, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_INFO(0,  acceleration)
	ZEND_ARG_INFO(0,  maximum)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sarext, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_INFO(0,  startValue)
	ZEND_ARG_INFO(0,  offsetOnReverse)
	ZEND_ARG_INFO(0,  accelerationInitLong)
	ZEND_ARG_INFO(0,  accelerationLong)
	ZEND_ARG_INFO(0,  accelerationMaxLong)
	ZEND_ARG_INFO(0,  accelerationInitShort)
	ZEND_ARG_INFO(0,  accelerationShort)
	ZEND_ARG_INFO(0,  accelerationMaxShort)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sin, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sinh, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sma, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sqrt, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_stddev, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
	ZEND_ARG_INFO(0,  nbDev)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_stoch, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  fastK_Period)
	ZEND_ARG_INFO(0,  slowK_Period)
	ZEND_ARG_INFO(0,  slowK_MAType)
	ZEND_ARG_INFO(0,  slowD_Period)
	ZEND_ARG_INFO(0,  slowD_MAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_stochf, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  fastK_Period)
	ZEND_ARG_INFO(0,  fastD_Period)
	ZEND_ARG_INFO(0,  fastD_MAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_stochrsi, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
	ZEND_ARG_INFO(0,  fastK_Period)
	ZEND_ARG_INFO(0,  fastD_Period)
	ZEND_ARG_INFO(0,  fastD_MAType)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sub, 0, 0, 2)
	ZEND_ARG_ARRAY_INFO(0,  real0, 0)
	ZEND_ARG_ARRAY_INFO(0,  real1, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_sum, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_t3, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
	ZEND_ARG_INFO(0,  vFactor)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_tan, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_tanh, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_tema, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_trange, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_trima, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_trix, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_tsf, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_typprice, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_ultosc, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod1)
	ZEND_ARG_INFO(0,  timePeriod2)
	ZEND_ARG_INFO(0,  timePeriod3)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_var, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
	ZEND_ARG_INFO(0,  nbDev)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_wclprice, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_willr, 0, 0, 3)
	ZEND_ARG_ARRAY_INFO(0,  high, 0)
	ZEND_ARG_ARRAY_INFO(0,  low, 0)
	ZEND_ARG_ARRAY_INFO(0,  close, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arg_info_trader_wma, 0, 0, 1)
	ZEND_ARG_ARRAY_INFO(0,  real, 0)
	ZEND_ARG_INFO(0,  timePeriod)
ZEND_END_ARG_INFO();

#endif /* TA_PHP_ARGINFO_H */

