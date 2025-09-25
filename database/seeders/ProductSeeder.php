<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'price' => 1299.99,
            'stock_quantity' => 10
        ]);
        
        Product::create([
            'name' => 'Smartphone',
            'price' => 699.99,
            'stock_quantity' => 25
        ]);
        
        Product::create([
            'name' => 'Headphones',
            'price' => 149.99,
            'stock_quantity' => 50
        ]);
        
        Product::create([
            'name' => 'Tablet',
            'price' => 399.99,
            'stock_quantity' => 15
        ]);
    }
}