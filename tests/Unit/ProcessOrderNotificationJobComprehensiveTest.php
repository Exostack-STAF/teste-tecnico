<?php

namespace Tests\Unit;

use App\Jobs\ProcessOrderNotification;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessOrderNotificationJobComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_can_be_instantiated_with_correct_parameters()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $job = new ProcessOrderNotification($order, 'confirmation');

        $this->assertEquals($order->id, $job->order->id);
        $this->assertEquals('confirmation', $job->type);
    }

    public function test_job_handles_confirmation_notification_correctly()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $job = new ProcessOrderNotification($order, 'confirmation');
        $notificationService = app(NotificationService::class);

        // Before job execution, no notifications should exist
        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);

        // Execute the job
        $job->handle($notificationService);

        // After job execution, a notification should exist
        $this->assertDatabaseHas('notifications', [
            'order_id' => $order->id,
            'message' => "Order #{$order->id} for Test Customer has been confirmed and is being processed."
        ]);
    }

    public function test_job_does_not_create_notification_for_non_confirmation_type()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);

        $job = new ProcessOrderNotification($order, 'shipped');
        $notificationService = app(NotificationService::class);

        // Execute the job
        $job->handle($notificationService);

        // No notification should be created for 'shipped' type
        $this->assertDatabaseMissing('notifications', [
            'order_id' => $order->id
        ]);
    }

    public function test_job_implements_should_queue_interface()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $job = new ProcessOrderNotification($order, 'confirmation');

        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $job);
    }

    public function test_job_can_be_serialized_and_unserialized()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $job = new ProcessOrderNotification($order, 'confirmation');

        $serialized = serialize($job);
        $unserialized = unserialize($serialized);

        $this->assertEquals($job->order->id, $unserialized->order->id);
        $this->assertEquals($job->type, $unserialized->type);
    }
}