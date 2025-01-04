<?php

namespace App\Enums;

enum ProcessStatusEnum: string
{
    case Created = 'created';
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
}
