<?php

namespace App\Enums;

enum UserOnboardingSteps: string
{
    case MAIN_INFO = 'main_info';
    case BASIC_INFO = 'basic_info';
    case TARGETS = 'targets';
    case COMPLETE = 'complete';
}
