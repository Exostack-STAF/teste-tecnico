<?php

namespace Tests\Unit;

use App\Events\OrderStatusChanged;
use App\Jobs\ProcessOrderNotification;
use App\Listeners\ProcessOrderStatusChange;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderStatusChangeListenerDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function it_processes_event_only_once_even_when_called_multiple_times()
    {
        Queue::fake();

        ProcessOrderStatusChange::clearProcessedEvents();

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');

        $listener = new ProcessOrderStatusChange();

        $listener->handle($event);
        $listener->handle($event);
        $listener->handle($event);

        Queue::assertPushed(ProcessOrderNotification::class, 1);
        
        Queue::assertPushed(ProcessOrderNotification::class, function ($job) use ($order) {
            return $job->order->id === $order->id && $job->type === 'confirmation';
        });
    }

    public function it_processes_different_events_multiple_times()
    {
        Queue::fake();

        ProcessOrderStatusChange::clearProcessedEvents();

        $order1 = Order::create([
            'customer_name' => 'Test Customer 1',
            'status' => 'pending'
        ]);

        $order2 = Order::create([
            'customer_name' => 'Test Customer 2',
            'status' => 'pending'
        ]);

        $event1 = new OrderStatusChanged($order1, 'pending', 'paid');
        $event2 = new OrderStatusChanged($order2, 'pending', 'paid');

        $listener = new ProcessOrderStatusChange();

        $listener->handle($event1);
        $listener->handle($event2);

        Queue::assertPushed(ProcessOrderNotification::class, 2);
    }
}