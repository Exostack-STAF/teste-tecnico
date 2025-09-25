<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductResourcePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_product_page_can_be_instantiated()
    {
        $page = new CreateProduct();
        $this->assertInstanceOf(CreateProduct::class, $page);
    }

    public function test_edit_product_page_can_be_instantiated()
    {
        $product = Product::factory()->create();
        $page = new EditProduct();
        $this->assertInstanceOf(EditProduct::class, $page);
    }

    public function test_list_products_page_can_be_instantiated()
    {
        $page = new ListProducts();
        $this->assertInstanceOf(ListProducts::class, $page);
    }
}