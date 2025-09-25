<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\ProcessOrderNotification;

class ProcessOrderStatusChange
{
    protected static array $processedEvents = [];
    
    protected static ?int $lastCleared = null;
    
    public function handle(OrderStatusChanged $event)
    {
        $now = time();
        if (static::$lastCleared === null || ($now - static::$lastCleared) > 60) {
            static::$processedEvents = [];
            static::$lastCleared = $now;
        }
        
        $eventId = md5($event->order->id . $event->oldStatus . $event->newStatus . serialize($event));
        
        if (in_array($eventId, static::$processedEvents)) {
            return;
        }
        
        static::$processedEvents[] = $eventId;

        if ($event->oldStatus === 'pending' && $event->newStatus === 'paid') {
            $notificationType = 'confirmation';
            
            ProcessOrderNotification::dispatch($event->order, $notificationType);
        }
    }
    
    public static function clearProcessedEvents()
    {
        static::$processedEvents = [];
        static::$lastCleared = time();
    }
}