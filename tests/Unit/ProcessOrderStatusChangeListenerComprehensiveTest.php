<?php

namespace Tests\Unit;

use App\Events\OrderStatusChanged;
use App\Listeners\ProcessOrderStatusChange;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessOrderStatusChangeListenerComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_listener_can_be_instantiated()
    {
        $listener = new ProcessOrderStatusChange();
        $this->assertInstanceOf(ProcessOrderStatusChange::class, $listener);
    }

    public function test_listener_handles_pending_to_paid_transition()
    {
        Queue::fake();

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');
        $listener = new ProcessOrderStatusChange();

        $listener->handle($event);

        // Assert that the job was dispatched
        Queue::assertPushed(\App\Jobs\ProcessOrderNotification::class);
    }

    public function test_listener_does_not_handle_paid_to_shipped_transition()
    {
        Queue::fake();

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);

        $event = new OrderStatusChanged($order, 'paid', 'shipped');
        $listener = new ProcessOrderStatusChange();

        $listener->handle($event);

        // Assert that no job was dispatched
        Queue::assertNotPushed(\App\Jobs\ProcessOrderNotification::class);
    }

    public function test_listener_clears_processed_events()
    {
        $listener = new ProcessOrderStatusChange();
        
        // Use reflection to access private/protected properties
        $reflection = new \ReflectionClass($listener);
        $processedEventsProperty = $reflection->getProperty('processedEvents');
        $processedEventsProperty->setAccessible(true);
        
        // Set some processed events
        $processedEventsProperty->setValue($listener, ['event1', 'event2']);
        
        // Clear processed events
        $listener->clearProcessedEvents();
        
        // Check that processed events are now empty
        $this->assertEmpty($processedEventsProperty->getValue($listener));
    }
}