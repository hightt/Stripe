<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

}