<?php

namespace App\Enums;

enum NotificationType: string
{
    case LIKE    = 'like';
    case COMMENT = 'comment';
    case REPLY   = 'reply';
    case RELOG   = 'relog';
    case FOLLOW  = 'follow';
}
