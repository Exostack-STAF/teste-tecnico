<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock_quantity',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    public function setStockQuantityAttribute($value)
    {
        $this->attributes['stock_quantity'] = max(0, (int) $value);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}