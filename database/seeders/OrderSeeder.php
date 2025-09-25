<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample orders
        $order1 = Order::create([
            'customer_name' => 'John Doe',
            'status' => 'pending',
        ]);
        
        // Add items to the first order
        $products = Product::inRandomOrder()->limit(2)->get();
        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $product->id,
                'quantity' => rand(1, 3),
                'price' => $product->price,
            ]);
        }
        
        // Create another order
        $order2 = Order::create([
            'customer_name' => 'Jane Smith',
            'status' => 'paid',
        ]);
        
        // Add items to the second order
        $products = Product::inRandomOrder()->limit(3)->get();
        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $product->id,
                'quantity' => rand(1, 2),
                'price' => $product->price,
            ]);
        }
    }
}