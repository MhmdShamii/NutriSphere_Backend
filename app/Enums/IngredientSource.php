<?php

namespace App\Enums;

enum IngredientSource: string
{
    case SYSTEM = 'system';
    case USER   = 'user';
}
