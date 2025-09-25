<?php

namespace Tests\Feature;

use App\Events\OrderStatusChanged;
use App\Jobs\ProcessOrderNotification;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;
    
    public function it_dispatches_event_when_order_status_changes_from_pending_to_paid()
    {
        Event::fake();
        Queue::fake();
        
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);
        
        $order->update(['status' => 'paid']);
        
        Event::assertDispatched(OrderStatusChanged::class, function ($event) use ($order) {
            return $event->order->id === $order->id &&
                   $event->oldStatus === 'pending' &&
                   $event->newStatus === 'paid';
        });
    }
    
    public function it_does_not_dispatch_event_when_order_status_changes_from_paid_to_shipped()
    {
        Event::fake();
        Queue::fake();
        
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);
        
        $order->update(['status' => 'shipped']);
        
        Event::assertNotDispatched(OrderStatusChanged::class);
    }
    
    public function it_processes_notification_job_when_status_changes_from_pending_to_paid()
    {
        Event::fake();
        Queue::fake();
        
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);
        
        event(new OrderStatusChanged($order, 'pending', 'paid'));
        
        Queue::assertPushed(ProcessOrderNotification::class, function ($job) use ($order) {
            return $job->order->id === $order->id && $job->type === 'confirmation';
        });
    }
    
    public function it_does_not_process_notification_job_when_status_changes_from_paid_to_shipped()
    {
        Event::fake();
        Queue::fake();
        
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);
        
        event(new OrderStatusChanged($order, 'paid', 'shipped'));
        
        Queue::assertNotPushed(ProcessOrderNotification::class);
    }
    
    public function it_creates_notification_record_when_job_is_processed()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);
        
        $job = new ProcessOrderNotification($order, 'confirmation');
        $job->handle(app(\App\Services\NotificationService::class));
        
        $this->assertDatabaseHas('notifications', [
            'order_id' => $order->id,
            'message' => "Order #{$order->id} for Test Customer has been confirmed and is being processed."
        ]);
    }
    
    public function it_does_not_create_notification_record_for_non_confirmation_type()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);
        
        $job = new ProcessOrderNotification($order, 'shipped');
        $job->handle(app(\App\Services\NotificationService::class));
        
        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);
    }
}