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

/* {{{ proto array trader_sarext(array high, array low [, float startValue [, float offsetOnReverse [, float accelerationInitLong [, float accelerationLong [, float accelerationMaxLong [, float accelerationInitShort [, float accelerationShort [, float accelerationMaxShort]]]]]]]])
	Parabolic SAR - Extended */
PHP_FUNCTION(trader_sarext)
{
	int optimalOutAlloc, lookback;
	zval *zinHigh, *zinLow;
	double *inHigh, *inLow, *outReal;
	int startIdx = 0, endIdx = 0, outBegIdx = 0, outNBElement = 0;
	
	double optInStartValue = TA_REAL_MIN, optInOffsetOnReverse = 0, optInAccelerationInitLong = 0, optInAccelerationLong = 0, optInAccelerationMaxLong = 0, optInAccelerationInitShort = 0, optInAccelerationShort = 0, optInAccelerationMaxShort = 0;

#if PHP_MAJOR_VERSION >= 7
	ZEND_PARSE_PARAMETERS_START(2, 10)
		Z_PARAM_ARRAY(zinHigh)
		Z_PARAM_ARRAY(zinLow)
		Z_PARAM_OPTIONAL
		Z_PARAM_DOUBLE(optInStartValue)
		Z_PARAM_DOUBLE(optInOffsetOnReverse)
		Z_PARAM_DOUBLE(optInAccelerationInitLong)
		Z_PARAM_DOUBLE(optInAccelerationLong)
		Z_PARAM_DOUBLE(optInAccelerationMaxLong)
		Z_PARAM_DOUBLE(optInAccelerationInitShort)
		Z_PARAM_DOUBLE(optInAccelerationShort)
		Z_PARAM_DOUBLE(optInAccelerationMaxShort)
	ZEND_PARSE_PARAMETERS_END_EX(RETURN_FALSE);
#else
	if (zend_parse_parameters(ZEND_NUM_ARGS(), "aa|dddddddd", &zinHigh, &zinLow, &optInStartValue, &optInOffsetOnReverse, &optInAccelerationInitLong, &optInAccelerationLong, &optInAccelerationMaxLong, &optInAccelerationInitShort, &optInAccelerationShort, &optInAccelerationMaxShort) == FAILURE) {
		RETURN_FALSE;
	}
#endif

	
	TRADER_DBL_SET_BOUNDABLE(TA_REAL_MIN, TA_REAL_MAX, optInStartValue);
	TRADER_DBL_SET_BOUNDABLE(0, TA_REAL_MAX, optInOffsetOnReverse);
	TRADER_DBL_SET_BOUNDABLE(0, TA_REAL_MAX, optInAccelerationInitLong);
	TRADER_DBL_SET_BOUNDABLE(0, TA_REAL_MAX, optInAccelerationLong);
	TRADER_DBL_SET_BOUNDABLE(0, TA_REAL_MAX, optInAccelerationMaxLong);
	TRADER_DBL_SET_BOUNDABLE(0, TA_REAL_MAX, optInAccelerationInitShort);
	TRADER_DBL_SET_BOUNDABLE(0, TA_REAL_MAX, optInAccelerationShort);
	TRADER_DBL_SET_BOUNDABLE(0, TA_REAL_MAX, optInAccelerationMaxShort);	

	TRADER_SET_MIN_INT2(endIdx, zend_hash_num_elements(Z_ARRVAL_P(zinHigh)),
		zend_hash_num_elements(Z_ARRVAL_P(zinLow)))
	endIdx--; /* it's <= in the ta-lib */
	

	lookback = TA_SAREXT_Lookback(optInStartValue, optInOffsetOnReverse, optInAccelerationInitLong, optInAccelerationLong, optInAccelerationMaxLong, optInAccelerationInitShort, optInAccelerationShort, optInAccelerationMaxShort);
	optimalOutAlloc = (lookback > endIdx) ? 0 : (endIdx - lookback + 1);
	if (optimalOutAlloc > 0) {
		outReal = emalloc(sizeof(double)*optimalOutAlloc);
		TRADER_DBL_ZARR_TO_ARR(zinHigh, inHigh)
		TRADER_DBL_ZARR_TO_ARR(zinLow, inLow)

		TRADER_G(last_error) = TA_SAREXT(startIdx, endIdx, inHigh, inLow, optInStartValue, optInOffsetOnReverse, optInAccelerationInitLong, optInAccelerationLong, optInAccelerationMaxLong, optInAccelerationInitShort, optInAccelerationShort, optInAccelerationMaxShort, &outBegIdx, &outNBElement, outReal);
		if (TRADER_G(last_error) != TA_SUCCESS) {
			efree(inHigh);
			efree(inLow);
			efree(outReal);

			RETURN_FALSE;
		}

		TRADER_DBL_ARR_TO_ZRET1(outReal, return_value, endIdx, outBegIdx, outNBElement)

		efree(inHigh);
		efree(inLow);
		efree(outReal);
	} else {
		/* The current input args combination would cause TA-Lib to produce
			 zero output, don't bother making any allocs or calls. */
		TRADER_G(last_error) = TA_BAD_PARAM;
		RETURN_FALSE;
	}
}
/* }}} */

