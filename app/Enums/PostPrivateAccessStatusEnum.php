<?php

namespace App\Enums;

enum PostPrivateAccessStatusEnum : string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
}
