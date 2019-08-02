<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'image', 'images', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];

    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段,
        'images' => 'json'
    ];

    public function skus()
    {
        return $this->hasMany('App\Models\ProductSku', 'product_id','id');
    }

    public function getImageUrlAttribute()
    {
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return "/uploads/{$this->image}";
    }
}
