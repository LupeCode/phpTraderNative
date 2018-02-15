<?php

namespace LupeCode\phpTraderNative\TALib\Core;

use LupeCode\phpTraderNative\TALib\Classes\MyInteger;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;

class MathOperators extends Core
{

    /**
     * @param int      $startIdx
     * @param int      $endIdx
     * @param float[]  $inReal0
     * @param float[]  $inReal1
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param float[]  $outReal
     *
     * @return int
     */
    public function add(int $startIdx, int $endIdx, array $inReal0, array $inReal1, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] + $inReal1[$i];
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal0
     * @param array     $inReal1
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function div(int $startIdx, int $endIdx, array $inReal0, array $inReal1, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] / $inReal1[$i];
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function max(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //double $highest, $tmp;
        //int $outIdx, $nbInitialElementNeeded;
        //int $trailingIdx, $today, $i, $highestIdx;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $outReal[$outIdx++] = $highest;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outInteger
     *
     * @return int
     */
    public function maxIndex(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outInteger): int
    {
        //double $highest, $tmp;
        //int $outIdx, $nbInitialElementNeeded;
        //int $trailingIdx, $today, $i, $highestIdx;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmp;
                    }
                }
            } elseif ($tmp >= $highest) {
                $highestIdx = $today;
                $highest    = $tmp;
            }
            $outInteger[$outIdx++] = $highestIdx;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function min(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //double $lowest, $tmp;
        //int $outIdx, $nbInitialElementNeeded;
        //int $trailingIdx, $lowestIdx, $today, $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $outReal[$outIdx++] = $lowest;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outInteger
     *
     * @return int
     */
    public function minIndex(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outInteger): int
    {
        //double $lowest, $tmp;
        //int $outIdx, $nbInitialElementNeeded;
        //int $trailingIdx, $lowestIdx, $today, $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmp = $inReal[$today];
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmp = $inReal[$i];
                    if ($tmp < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmp;
                    }
                }
            } elseif ($tmp <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmp;
            }
            $outInteger[$outIdx++] = $lowestIdx;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outMin
     * @param array     $outMax
     *
     * @return int
     */
    public function minMax(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outMin, array &$outMax): int
    {
        //double $highest, $lowest, $tmpHigh, $tmpLow;
        //int $outIdx, $nbInitialElementNeeded;
        //int $trailingIdx, $today, $i, $highestIdx, $lowestIdx;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmpLow = $tmpHigh = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmpHigh = $inReal[$i];
                    if ($tmpHigh > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmpHigh;
                    }
                }
            } elseif ($tmpHigh >= $highest) {
                $highestIdx = $today;
                $highest    = $tmpHigh;
            }
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmpLow = $inReal[$i];
                    if ($tmpLow < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmpLow;
                    }
                }
            } elseif ($tmpLow <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmpLow;
            }
            $outMax[$outIdx] = $highest;
            $outMin[$outIdx] = $lowest;
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outMinIdx
     * @param array     $outMaxIdx
     *
     * @return int
     */
    public function minMaxIndex(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outMinIdx, array &$outMaxIdx): int
    {
        //double $highest, $lowest, $tmpHigh, $tmpLow;
        //int $outIdx, $nbInitialElementNeeded;
        //int $trailingIdx, $today, $i, $highestIdx, $lowestIdx;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $nbInitialElementNeeded = ($optInTimePeriod - 1);
        if ($startIdx < $nbInitialElementNeeded) {
            $startIdx = $nbInitialElementNeeded;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $outIdx      = 0;
        $today       = $startIdx;
        $trailingIdx = $startIdx - $nbInitialElementNeeded;
        $highestIdx  = -1;
        $highest     = 0.0;
        $lowestIdx   = -1;
        $lowest      = 0.0;
        while ($today <= $endIdx) {
            $tmpLow = $tmpHigh = $inReal[$today];
            if ($highestIdx < $trailingIdx) {
                $highestIdx = $trailingIdx;
                $highest    = $inReal[$highestIdx];
                $i          = $highestIdx;
                while (++$i <= $today) {
                    $tmpHigh = $inReal[$i];
                    if ($tmpHigh > $highest) {
                        $highestIdx = $i;
                        $highest    = $tmpHigh;
                    }
                }
            } elseif ($tmpHigh >= $highest) {
                $highestIdx = $today;
                $highest    = $tmpHigh;
            }
            if ($lowestIdx < $trailingIdx) {
                $lowestIdx = $trailingIdx;
                $lowest    = $inReal[$lowestIdx];
                $i         = $lowestIdx;
                while (++$i <= $today) {
                    $tmpLow = $inReal[$i];
                    if ($tmpLow < $lowest) {
                        $lowestIdx = $i;
                        $lowest    = $tmpLow;
                    }
                }
            } elseif ($tmpLow <= $lowest) {
                $lowestIdx = $today;
                $lowest    = $tmpLow;
            }
            $outMaxIdx[$outIdx] = $highestIdx;
            $outMinIdx[$outIdx] = $lowestIdx;
            $outIdx++;
            $trailingIdx++;
            $today++;
        }
        $outBegIdx->value    = $startIdx;
        $outNBElement->value = $outIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal0
     * @param array     $inReal1
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function mult(int $startIdx, int $endIdx, array $inReal0, array $inReal1, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] * $inReal1[$i];
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal0
     * @param array     $inReal1
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function sub(int $startIdx, int $endIdx, array $inReal0, array $inReal1, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //int $outIdx;
        //int $i;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        for ($i = $startIdx, $outIdx = 0; $i <= $endIdx; $i++, $outIdx++) {
            $outReal[$outIdx] = $inReal0[$i] - $inReal1[$i];
        }
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

    /**
     * @param int       $startIdx
     * @param int       $endIdx
     * @param array     $inReal
     * @param int       $optInTimePeriod
     * @param MyInteger $outBegIdx
     * @param MyInteger $outNBElement
     * @param array     $outReal
     *
     * @return int
     */
    public function sum(int $startIdx, int $endIdx, array $inReal, int $optInTimePeriod, MyInteger &$outBegIdx, MyInteger &$outNBElement, array &$outReal): int
    {
        //double $periodTotal, $tempReal;
        //int $i, $outIdx, $trailingIdx, $lookbackTotal;
        if ($startIdx < 0) {
            return ReturnCode::OutOfRangeStartIndex;
        }
        if (($endIdx < 0) || ($endIdx < $startIdx)) {
            return ReturnCode::OutOfRangeEndIndex;
        }
        if ((int)$optInTimePeriod == (PHP_INT_MIN)) {
            $optInTimePeriod = 30;
        } elseif (((int)$optInTimePeriod < 2) || ((int)$optInTimePeriod > 100000)) {
            return ReturnCode::BadParam;
        }
        $lookbackTotal = ($optInTimePeriod - 1);
        if ($startIdx < $lookbackTotal) {
            $startIdx = $lookbackTotal;
        }
        if ($startIdx > $endIdx) {
            $outBegIdx->value    = 0;
            $outNBElement->value = 0;

            return ReturnCode::Success;
        }
        $periodTotal = 0;
        $trailingIdx = $startIdx - $lookbackTotal;
        $i           = $trailingIdx;
        if ($optInTimePeriod > 1) {
            while ($i < $startIdx) {
                $periodTotal += $inReal[$i++];
            }
        }
        $outIdx = 0;
        do {
            $periodTotal        += $inReal[$i++];
            $tempReal           = $periodTotal;
            $periodTotal        -= $inReal[$trailingIdx++];
            $outReal[$outIdx++] = $tempReal;
        } while ($i <= $endIdx);
        $outNBElement->value = $outIdx;
        $outBegIdx->value    = $startIdx;

        return ReturnCode::Success;
    }

}
