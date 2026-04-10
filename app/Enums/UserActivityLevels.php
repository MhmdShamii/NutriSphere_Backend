<?php

namespace App\Enums;

enum UserActivityLevels: string
{
    case SEDENTARY = 'sedentary';
    case LIGHT = 'light';
    case MODERATE = 'moderate';
    case ACTIVE = 'active';
    case VERY_ACTIVE = 'very_active';
}
