<?php

namespace Tests\Unit;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusChangedEventComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_can_be_instantiated_with_correct_parameters()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');

        $this->assertEquals($order->id, $event->order->id);
        $this->assertEquals('pending', $event->oldStatus);
        $this->assertEquals('paid', $event->newStatus);
    }

    public function test_event_uses_dispatchable_trait()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');

        // Check that the event uses the Dispatchable trait
        $this->assertTrue(method_exists($event, 'dispatch'));
    }

    public function test_event_has_order_property()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');

        // Check that the event has the order property
        $this->assertTrue(property_exists($event, 'order'));
        $this->assertEquals($order->id, $event->order->id);
    }

    public function test_event_can_be_serialized_and_unserialized()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');

        $serialized = serialize($event);
        $unserialized = unserialize($serialized);

        $this->assertEquals($event->order->id, $unserialized->order->id);
        $this->assertEquals($event->oldStatus, $unserialized->oldStatus);
        $this->assertEquals($event->newStatus, $unserialized->newStatus);
    }
}