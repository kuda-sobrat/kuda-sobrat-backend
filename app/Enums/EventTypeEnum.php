<?php

namespace App\Enums;

enum EventTypeEnum: string
{
    case Paid = 'paid';
    case Free = 'free';
    case ByAppointment = 'by_appointment';
}
