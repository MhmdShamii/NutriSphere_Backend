<?php

namespace App\Enums;

enum HealthConditionSeverity: string
{
    case BLOCK  = 'block';
    case WARN   = 'warn';
    case ADJUST = 'adjust';
}
