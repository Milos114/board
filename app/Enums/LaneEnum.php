<?php

namespace App\Enums;

enum LaneEnum: string
{
    case BACK_LOG = 'back_log';
    case TO_DO = 'to_do';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
