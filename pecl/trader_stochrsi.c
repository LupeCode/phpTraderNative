/*
	Copyright (c) 2012-2018, Anatol Belski <ab@php.net>
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

#include "php.h"
#include "php_trader.h"

#include <ta_func.h>
#include <ta_common.h>

ZEND_EXTERN_MODULE_GLOBALS(trader)

/* {{{ proto array trader_stochrsi(array real [, int timePeriod [, int fastK_Period [, int fastD_Period [, int fastD_MAType]]]])
	Stochastic Relative Strength Index */
PHP_FUNCTION(trader_stochrsi)
{
	int optimalOutAlloc, lookback;
	zval *zinReal;
	double *inReal, *outFastK, *outFastD;
	int startIdx = 0, endIdx = 0, outBegIdx = 0, outNBElement = 0;
	zend_long optInTimePeriod = 2, optInFastK_Period = 1, optInFastD_Period = 1, optInFastD_MAType = 0;
	

#if PHP_MAJOR_VERSION >= 7
	ZEND_PARSE_PARAMETERS_START(1, 5)
		Z_PARAM_ARRAY(zinReal)
		Z_PARAM_OPTIONAL
		Z_PARAM_LONG(optInTimePeriod)
		Z_PARAM_LONG(optInFastK_Period)
		Z_PARAM_LONG(optInFastD_Period)
		Z_PARAM_LONG(optInFastD_MAType)
	ZEND_PARSE_PARAMETERS_END_EX(RETURN_FALSE);
#else
	if (zend_parse_parameters(ZEND_NUM_ARGS(), "a|llll", &zinReal, &optInTimePeriod, &optInFastK_Period, &optInFastD_Period, &optInFastD_MAType) == FAILURE) {
		RETURN_FALSE;
	}
#endif

	TRADER_CHECK_MA_TYPE(optInFastD_MAType)
	TRADER_LONG_SET_BOUNDABLE(2, 100000, optInTimePeriod);
	TRADER_LONG_SET_BOUNDABLE(1, 100000, optInFastK_Period);
	TRADER_LONG_SET_BOUNDABLE(1, 100000, optInFastD_Period);	

	TRADER_SET_MIN_INT1(endIdx, zend_hash_num_elements(Z_ARRVAL_P(zinReal)))
	endIdx--; /* it's <= in the ta-lib */
	

	lookback = TA_STOCHRSI_Lookback((int)optInTimePeriod, (int)optInFastK_Period, (int)optInFastD_Period, (int)optInFastD_MAType);
	optimalOutAlloc = (lookback > endIdx) ? 0 : (endIdx - lookback + 1);
	if (optimalOutAlloc > 0) {
		outFastK = emalloc(sizeof(double)*optimalOutAlloc);
		outFastD = emalloc(sizeof(double)*optimalOutAlloc);
		TRADER_DBL_ZARR_TO_ARR(zinReal, inReal)

		TRADER_G(last_error) = TA_STOCHRSI(startIdx, endIdx, inReal, (int)optInTimePeriod, (int)optInFastK_Period, (int)optInFastD_Period, (int)optInFastD_MAType, &outBegIdx, &outNBElement, outFastK, outFastD);
		if (TRADER_G(last_error) != TA_SUCCESS) {
			efree(inReal);
			efree(outFastK);
			efree(outFastD);

			RETURN_FALSE;
		}

		TRADER_DBL_ARR_TO_ZRET2(outFastK, outFastD, return_value, endIdx, outBegIdx, outNBElement)

		efree(inReal);
		efree(outFastK);
		efree(outFastD);
	} else {
		/* The current input args combination would cause TA-Lib to produce
			 zero output, don't bother making any allocs or calls. */
		TRADER_G(last_error) = TA_BAD_PARAM;
		RETURN_FALSE;
	}
}
/* }}} */

