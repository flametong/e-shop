<?php

namespace ishop\helpers\data\enums;

enum Timestamp: int
{
    case Hour = 3600;
    case Day = 86400;
    case Week = 604800;
    case Month = 18144000;
}