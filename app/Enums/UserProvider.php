<?php

namespace App\Enums;

enum UserProvider: string
{
    case LOCAL = 'local';
    case GOOGLE = 'google';
}
