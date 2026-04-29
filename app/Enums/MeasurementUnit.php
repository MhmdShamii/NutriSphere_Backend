<?php

namespace App\Enums;

enum MeasurementUnit: string
{
    case G     = 'g';
    case ML    = 'ml';
    case TBSP  = 'tbsp';
    case TSP   = 'tsp';
    case KG    = 'kg';
    case L     = 'l';
    case PIECE = 'piece';
    case CUP   = 'cup';
    case OZ    = 'oz';
    case LB    = 'lb';
}
