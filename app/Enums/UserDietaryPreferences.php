<?php

namespace App\Enums;

enum UserDietaryPreferences: string
{
    case VEGETARIAN = 'vegetarian';
    case VEGAN = 'vegan';
    case PESCATARIA = 'pescatarian';
    case NONE = 'none';
}
