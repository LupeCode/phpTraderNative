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

/* {{{ proto array MY_FUNC_NAME_LOWER(MY_FUNC_DOC_PARAMS)
	MY_FUNC_DESC */
PHP_FUNCTION(MY_FUNC_NAME_LOWER)
{
	int optimalOutAlloc, lookback;
	MY_IN_PHP_ARRAY_DEFS
	MY_FUNC_ARRAY_PARA_DEFS
	MY_FUNC_INT_PARA_DEFS
	MY_IN_PHP_LONG_DEFS
	MY_IN_PHP_DOUBLE_DEFS

#if PHP_MAJOR_VERSION >= 7
	MY_ZEND_FAST_ZPP
#else
	if (zend_parse_parameters(ZEND_NUM_ARGS(), MY_ZEND_PARAMS_STR, MY_ZEND_PARAM_LIST) == FAILURE) {
		RETURN_FALSE;
	}
#endif

	MY_FUNC_CHECK_MA_TYPES
	MY_FUNC_SET_BOUNDABLE	

	MY_FUNC_SET_MIN_END_IDX
	MY_FUNC_SET_START_IDX

	MY_FUNC_OPTIMAL_OUT_ALLOC
	if (optimalOutAlloc > 0) {
		MY_FUNC_ARRAY_PARA_ALLOCS

		TRADER_G(last_error) = MY_FUNC_NAME(MY_FUNC_PARAMS);
		if (TRADER_G(last_error) != TA_SUCCESS) {
			MY_FUNC_ARRAY_PARA_DEALLOCS2

			RETURN_FALSE;
		}

		MY_PHP_MAKE_RETURN

		MY_FUNC_ARRAY_PARA_DEALLOCS1
	} else {
		/* The current input args combination would cause TA-Lib to produce
			 zero output, don't bother making any allocs or calls. */
		TRADER_G(last_error) = TA_BAD_PARAM;
		RETURN_FALSE;
	}
}
/* }}} */

