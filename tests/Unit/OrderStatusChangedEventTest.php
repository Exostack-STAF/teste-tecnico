<?php

namespace Tests\Unit;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusChangedEventTest extends TestCase
{
    use RefreshDatabase;

    public function it_can_be_instantiated_with_correct_parameters()
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

    public function it_serializes_the_order_model_correctly()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');

        $serialized = serialize($event);
        $unserialized = unserialize($serialized);

        $this->assertEquals($order->id, $unserialized->order->id);
        $this->assertEquals($order->customer_name, $unserialized->order->customer_name);
        $this->assertEquals('pending', $unserialized->oldStatus);
        $this->assertEquals('paid', $unserialized->newStatus);
    }
}