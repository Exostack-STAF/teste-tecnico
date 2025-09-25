<?php

namespace Tests\Unit;

use App\Jobs\ProcessOrderNotification;
use App\Models\Order;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function it_processes_confirmation_notification_correctly()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $job = new ProcessOrderNotification($order, 'confirmation');
        
        $job->handle(app(NotificationService::class));

        $this->assertDatabaseHas('notifications', [
            'order_id' => $order->id,
            'message' => "Order #{$order->id} for Test Customer has been confirmed and is being processed."
        ]);
    }

    public function it_does_not_create_notification_for_shipped_type()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);

        $job = new ProcessOrderNotification($order, 'shipped');
        
        $job->handle(app(NotificationService::class));

        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);
    }

    public function it_is_queued_correctly()
    {
        Queue::fake();

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        ProcessOrderNotification::dispatch($order, 'confirmation');

        Queue::assertPushed(ProcessOrderNotification::class, function ($job) use ($order) {
            return $job->order->id === $order->id && $job->type === 'confirmation';
        });
    }
}