<?php

namespace Tests\Feature;

use App\Filament\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationResourceTest extends TestCase
{
    use RefreshDatabase;

    public function it_displays_order_id_column_in_notification_table()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $notification = Notification::create([
            'order_id' => $order->id,
            'message' => 'Test notification message'
        ]);

        $table = NotificationResource::table(new \Filament\Tables\Table(\Livewire\Livewire::component('test')));
        
        $columns = $table->getColumns();
        $this->assertArrayHasKey('order_id', $columns);
        
        $this->assertEquals('Order ID', $columns['order_id']->getLabel());
    }
}