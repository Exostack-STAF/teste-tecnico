<?php

namespace Tests\Unit;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use Tests\TestCase;

class ResourceIconTest extends TestCase
{
    public function order_resource_has_correct_icon()
    {
        $this->assertEquals('heroicon-o-shopping-cart', OrderResource::$navigationIcon);
    }

    public function product_resource_has_correct_icon()
    {
        $this->assertEquals('heroicon-o-tag', ProductResource::$navigationIcon);
    }
}