<?php

namespace App\Enums;

enum UserGoal: string
{
    case LOSE_WEIGHT = 'lose_weight';
    case GAIN_MUSCLE = 'gain_muscle';
    case MAINTAIN = 'maintain';
}
