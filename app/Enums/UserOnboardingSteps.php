<?php

namespace App\Enums;

enum UserOnboardingSteps: string
{
    case BASIC_INFO = 'basic_info';
    case TARGETS = 'targets';
    case COMPLETE = 'complete';
}
