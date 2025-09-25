<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = new NotificationService();
    }

    public function test_it_creates_confirmation_notification()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $this->notificationService->saveNotification($order, 'confirmation');

        $this->assertDatabaseHas('notifications', [
            'order_id' => $order->id,
            'message' => "Order #{$order->id} for Test Customer has been confirmed and is being processed."
        ]);
    }

    public function test_it_does_not_create_notification_for_shipped_type()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);

        $this->notificationService->saveNotification($order, 'shipped');

        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);
    }

    public function test_it_does_not_create_notification_for_invalid_type()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $this->notificationService->saveNotification($order, 'invalid_type');

        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);
    }
}