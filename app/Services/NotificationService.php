<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Notification;

class NotificationService
{
    public function saveNotification(Order $order, string $type)
    {
        if ($type !== 'confirmation') {
            return;
        }
        
        $message = "Order #{$order->id} for {$order->customer_name} has been confirmed and is being processed.";
        
        Notification::create([
            'order_id' => $order->id,
            'message' => $message
        ]);
    }
}