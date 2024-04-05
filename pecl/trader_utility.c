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

#include "php.h"
#include "php_trader.h"

#include <ta_func.h>
#include <ta_common.h>

ZEND_EXTERN_MODULE_GLOBALS(trader)

/* {{{ proto void trader_set_unstable_period(int functionId, int timePeriod)
 see more here http://www.ta-lib.org/d_api/ta_setunstableperiod.html */
PHP_FUNCTION(trader_set_unstable_period)
{
	long functionId, timePeriod;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "ll", &functionId, &timePeriod) == FAILURE) {
		RETURN_FALSE;
	}

	if (TA_SetUnstablePeriod((int)functionId, (int)timePeriod) != TA_SUCCESS) {
		/* XXX error handling here */
	}
}
/* }}} */

/* {{{ proto int trader_get_unstable_period(int functionId) */
PHP_FUNCTION(trader_get_unstable_period)
{
	long functionId;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "l", &functionId) == FAILURE) {
		RETURN_FALSE;
	}

	/* XXX error handling here */
	RETURN_LONG(TA_GetUnstablePeriod((int)functionId));
}
/* }}} */

/* {{{ proto void trader_set_compat(compatId) */
PHP_FUNCTION(trader_set_compat)
{
	long compatId;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "l", &compatId) == FAILURE) {
		RETURN_FALSE;
	}

	if (TA_SetCompatibility((int)compatId) != TA_SUCCESS) {
		/* XXX error handling here */
	}
}
/* }}} */

/* {{{ proto int trader_get_compat(void)*/
PHP_FUNCTION(trader_get_compat)
{
	if (zend_parse_parameters_none() == FAILURE) {
		RETURN_FALSE;
	}

	RETURN_LONG(TA_GetCompatibility());
}
/* }}} */


/* {{{ proto int trader_errno(void)*/
PHP_FUNCTION(trader_errno)
{
	if (zend_parse_parameters_none() == FAILURE) {
		RETURN_FALSE;
	}

	RETURN_LONG(TRADER_G(last_error));
}
/* }}} */

