<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_product()
    {
        $productData = [
            'name' => 'Test Product',
            'price' => 19.99,
            'stock_quantity' => 10
        ];

        $product = Product::create($productData);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 19.99,
            'stock_quantity' => 10
        ]);

        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(19.99, $product->price);
        $this->assertEquals(10, $product->stock_quantity);
    }

    public function test_list_products()
    {
        Product::factory()->count(3)->create();

        $products = Product::all();

        $this->assertCount(3, $products);
    }

    public function test_update_product()
    {
        $product = Product::factory()->create([
            'name' => 'Old Product Name',
            'price' => 10.99,
            'stock_quantity' => 5
        ]);

        $updatedData = [
            'name' => 'New Product Name',
            'price' => 15.99,
            'stock_quantity' => 8
        ];

        $product->update($updatedData);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Product Name',
            'price' => 15.99,
            'stock_quantity' => 8
        ]);
    }

    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $productId = $product->id;
        $product->delete();

        $this->assertDatabaseMissing('products', [
            'id' => $productId
        ]);
    }

    public function test_product_stock_cannot_be_negative()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10.99,
            'stock_quantity' => -5
        ]);

        $this->assertEquals(0, $product->stock_quantity);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 0
        ]);

        $product->update(['stock_quantity' => -1]);
        $product->refresh();
        
        $this->assertEquals(0, $product->stock_quantity);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 0
        ]);
    }
}