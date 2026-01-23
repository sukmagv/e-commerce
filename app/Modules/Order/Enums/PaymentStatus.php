<?php

namespace App\Modules\Order\Enums;

enum PaymentStatus: string
{
    case PENDING  = 'pending';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';

    public function getRelatedOrderStatus(): OrderStatus {
        return match($this) {
            self::ACCEPTED => OrderStatus::FINISHED,
            self::DECLINED => OrderStatus::DECLINED,
        };
    }
}
