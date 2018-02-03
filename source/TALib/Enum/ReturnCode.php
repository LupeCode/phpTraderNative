<?php

namespace LupeCode\phpTraderNative\TALib\Enum;

class ReturnCode
{
    const Success              = 0;
    const BadParam             = 1;
    const OutOfRangeStartIndex = 2;
    const OutOfRangeEndIndex   = 3;
    const AllocError           = 4;
    const InternalError        = 5;
}
