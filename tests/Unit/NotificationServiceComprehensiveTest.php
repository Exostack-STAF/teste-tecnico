<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = new NotificationService();
    }

    public function test_service_can_be_instantiated()
    {
        $this->assertInstanceOf(NotificationService::class, $this->notificationService);
    }

    public function test_service_creates_confirmation_notification()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        // Before service call, no notifications should exist
        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);

        // Call the service method
        $this->notificationService->saveNotification($order, 'confirmation');

        // After service call, a notification should exist
        $this->assertDatabaseHas('notifications', [
            'order_id' => $order->id,
            'message' => "Order #{$order->id} for Test Customer has been confirmed and is being processed."
        ]);
    }

    public function test_service_does_not_create_notification_for_non_confirmation_type()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);

        // Call the service method with 'shipped' type
        $this->notificationService->saveNotification($order, 'shipped');

        // No notification should be created
        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);
    }

    public function test_service_does_not_create_notification_for_invalid_type()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        // Call the service method with invalid type
        $this->notificationService->saveNotification($order, 'invalid_type');

        // No notification should be created
        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);
    }
}