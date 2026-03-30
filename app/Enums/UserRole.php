<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'client';
    case COACH = 'coach';
    case ADMIN = 'admin';
}
