<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderResourceFormTest extends TestCase
{
    use RefreshDatabase;

    public function it_only_shows_pending_status_for_new_orders()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10.99,
            'stock_quantity' => 100
        ]);

        Livewire::test(OrderResource\Pages\CreateOrder::class)
            ->assertFormFieldExists('status')
            ->assertFormSet([
                'status' => 'pending'
            ]);
    }

    public function it_shows_valid_transitions_for_pending_order()
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

        Livewire::test(OrderResource\Pages\EditOrder::class, ['record' => $order->id])
            ->assertFormFieldExists('status')
            ->assertFormSet([
                'status' => 'pending'
            ]);
    }

    public function it_shows_valid_transitions_for_paid_order()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'paid'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10.99,
            'stock_quantity' => 100
        ]);

        Livewire::test(OrderResource\Pages\EditOrder::class, ['record' => $order->id])
            ->assertFormFieldExists('status')
            ->assertFormSet([
                'status' => 'paid'
            ]);
    }

    public function it_shows_valid_transitions_for_shipped_order()
    {
        $order = Order::create([
            'customer_name' => 'Test Customer',
            'status' => 'shipped'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10.99,
            'stock_quantity' => 100
        ]);

        Livewire::test(OrderResource\Pages\EditOrder::class, ['record' => $order->id])
            ->assertFormFieldExists('status')
            ->assertFormSet([
                'status' => 'shipped'
            ]);
    }
}