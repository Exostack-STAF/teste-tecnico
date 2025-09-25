<?php

namespace Tests\Unit;

use App\Events\OrderStatusChanged;
use App\Jobs\ProcessOrderNotification;
use App\Listeners\ProcessOrderStatusChange;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderStatusChangeListenerTest extends TestCase
{
    use RefreshDatabase;

    public function it_dispatches_job_for_pending_to_paid_transition()
    {
        Queue::fake();

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'paid');

        $listener = new ProcessOrderStatusChange();

        $listener->handle($event);

        Queue::assertPushed(ProcessOrderNotification::class, function ($job) use ($order) {
            return $job->order->id === $order->id && $job->type === 'confirmation';
        });
    }

    public function it_does_not_dispatch_job_for_paid_to_shipped_transition()
    {
        Queue::fake();

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);

        $event = new OrderStatusChanged($order, 'paid', 'shipped');

        $listener = new ProcessOrderStatusChange();

        $listener->handle($event);

        Queue::assertNotPushed(ProcessOrderNotification::class);
    }

    public function it_does_not_dispatch_job_for_same_status()
    {
        Queue::fake();

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $event = new OrderStatusChanged($order, 'pending', 'pending');

        $listener = new ProcessOrderStatusChange();

        $listener->handle($event);

        Queue::assertNotPushed(ProcessOrderNotification::class);
    }
}