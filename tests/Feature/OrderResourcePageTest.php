<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderResourcePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_page_can_be_instantiated()
    {
        $page = new CreateOrder();
        $this->assertInstanceOf(CreateOrder::class, $page);
    }

    public function test_edit_order_page_can_be_instantiated()
    {
        $order = Order::factory()->create();
        $page = new EditOrder();
        $this->assertInstanceOf(EditOrder::class, $page);
    }

    public function test_list_orders_page_can_be_instantiated()
    {
        $page = new ListOrders();
        $this->assertInstanceOf(ListOrders::class, $page);
    }
}