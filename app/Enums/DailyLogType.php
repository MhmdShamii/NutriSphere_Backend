<?php

namespace App\Enums;

enum DailyLogType: string
{
    case MEAL     = 'meal';
    case CUSTOM   = 'custom';
    case ESTIMATE = 'estimate';
}
