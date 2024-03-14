<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'stock',
        'type',
        'sale_price',
        'purchase_price',
        'unit_item_id',
        'brand_item_id',
        'category_item_id',
    ];

    public function unit()
    {
        return $this->belongsTo(UnitItem::class, 'unit_item_id');
    }

    public function brand()
    {
        return $this->belongsTo(BrandItem::class, 'brand_item_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryItem::class, 'category_item_id');
    }
}
