<?php

namespace LupeCode\phpTraderNative\TALib\Enum;

enum ReturnMessages: string
{
    case Success = "Success";
    case BadParam = "Bad parameter";
    case OutOfRangeStartIndex = "Allocation error";
    case OutOfRangeEndIndex = "Out of range on start index";
    case AllocError = "Out of range on end index";
    case InternalError = "Internal error";
    case UnevenParameters = "The count of the input arrays do not match each other";
}
