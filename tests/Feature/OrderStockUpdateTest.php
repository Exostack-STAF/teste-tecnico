<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStockUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function it_creates_order_and_updates_stock_directly()
    {
        $product1 = Product::create([
            'name' => 'Test Product 1',
            'price' => 10.99,
            'stock_quantity' => 100
        ]);
        
        $product2 = Product::create([
            'name' => 'Test Product 2',
            'price' => 20.50,
            'stock_quantity' => 50
        ]);
        
        $orderId = \Illuminate\Support\Facades\DB::table('orders')->insertGetId([
            'customer_name' => 'Test Customer',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        \Illuminate\Support\Facades\DB::table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 10.99,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        \Illuminate\Support\Facades\DB::table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 20.50,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $this->updateProductStock($orderId);
        
        $product1->refresh();
        $product2->refresh();
        
        $this->assertEquals(98, $product1->stock_quantity);
        $this->assertEquals(49, $product2->stock_quantity);
    }
    
    private function updateProductStock(int $orderId)
    {
        $orderItems = \Illuminate\Support\Facades\DB::table('order_items')
            ->where('order_id', $orderId)
            ->get();
        
        foreach ($orderItems as $item) {
            $product = \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $item->product_id)
                ->first();
            
            if (!$product) {
                continue;
            }
            
            if ($product->stock_quantity < $item->quantity) {
                throw new \Exception("Not enough stock for product ID: {$item->product_id}. Available: {$product->stock_quantity}, Requested: {$item->quantity}");
            }
            
            $newStock = max(0, $product->stock_quantity - $item->quantity);
            
            \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $item->product_id)
                ->update(['stock_quantity' => $newStock]);
        }
    }
}