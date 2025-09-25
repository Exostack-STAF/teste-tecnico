<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderNotificationDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function it_shows_only_one_notification_when_creating_order()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10.99,
            'stock_quantity' => 100
        ]);

        Livewire::test(\App\Filament\Resources\OrderResource\Pages\CreateOrder::class)
            ->fillForm([
                'customer_name' => 'Test Customer',
                'status' => 'pending',
                'order_items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'price' => 10.99
                    ]
                ]
            ])
            ->call('create')
            ->assertHasNoErrors()
            ->assertNotified();
    }

    public function it_shows_only_one_notification_when_updating_order()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'pending'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10.99,
            'stock_quantity' => 100
        ]);

        Livewire::test(\App\Filament\Resources\OrderResource\Pages\EditOrder::class, ['record' => $order->id])
            ->fillForm([
                'customer_name' => 'Updated Customer',
                'status' => 'pending'
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertNotified();
    }
}