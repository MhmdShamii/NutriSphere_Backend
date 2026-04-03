<?php

namespace App\Enums;

enum UserOnboardingSteps: string
{
    case MAIN_INFO  = 'main_info';
    case BASIC_INFO = 'basic_info';
    case TARGETS    = 'targets';
    case COMPLETE   = 'complete';

    public function order(): int
    {
        return match ($this) {
            self::MAIN_INFO  => 1,
            self::BASIC_INFO => 2,
            self::TARGETS    => 3,
            self::COMPLETE   => 4,
        };
    }
}
