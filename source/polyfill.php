<?php

/*
 * This file is part of the LupeCode\phpTraderNative package.
 *
 * (c) LupeCode <joshua@lupecode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use LupeCode\phpTraderNative\Trader;

/*
 * Define any missing Trader constants
 */
// ENUM TRADER_MA_TYPE
require_once __DIR__ . '/polyfill/enumMaType.php';
if (!defined('TRADER_REAL_MIN')) {
    define('TRADER_REAL_MIN', (-3e+37));
}
if (!defined('TRADER_REAL_MAX')) {
    define('TRADER_REAL_MAX', (3e+37));
}

if (!defined('TRADER_INTEGER_MIN')) {
    define('TRADER_INTEGER_MIN', (-2147483647 + 1));
}
if (!defined('TRADER_INTEGER_MAX')) {
    define('TRADER_INTEGER_MAX', (2147483647));
}
// ENUM TRADER_FUNC_UNST
require_once __DIR__ . '/polyfill/enumFunctionUnstablePeriod.php';
// ENUM TRADER_COMPATIBILITY
if (!defined('TRADER_COMPATIBILITY_DEFAULT')) {
    define('TRADER_COMPATIBILITY_DEFAULT', 0);
}
if (!defined('TRADER_COMPATIBILITY_METASTOCK')) {
    define('TRADER_COMPATIBILITY_METASTOCK', 1);
}
// ENUM TRADER_ERR
require_once __DIR__ . '/polyfill/enumErrorCode.php';

/*
 * Define any missing Trader functions
 */
if (!function_exists('trader_set_unstable_period')) {
    function trader_set_unstable_period(int $functionId, int $timePeriod): void { Trader::set_unstable_period($functionId, $timePeriod); }
}
if (!function_exists('trader_get_unstable_period')) {
    function trader_get_unstable_period(int $functionId): int { return Trader::get_unstable_period($functionId); }
}
if (!function_exists('trader_set_compat')) {
    function trader_set_compat(int $compatId): void { Trader::set_compat($compatId); }
}
if (!function_exists('trader_get_compat')) {
    function trader_get_compat(): int { return Trader::get_compat(); }
}

require_once __DIR__ . '/polyfill/functions.php';

/*
 * Define friendly aliases for all functions
 */
function traderMathArcCosine(array $real) { return \LupeCode\phpTraderNative\TraderFriendly::mathArcCosine($real); }
