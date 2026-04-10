<?php

namespace App\Enums;

enum HealthConditionType: string
{
    case DISEASE     = 'disease';
    case ALLERGY     = 'allergy';
    case INTOLERANCE = 'intolerance';
    case CONDITION   = 'condition';
}
