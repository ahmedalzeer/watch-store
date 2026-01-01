<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    public $translatable = ['name', 'description'];

    protected $casts = [
        'specifications' => 'array', // مهم جداً للتعامل مع مواصفات الساعات
    ];

    protected $fillable = [
        'store_id',
        'category_id',
        'brand_id',
        'name',
        'description',
        'price',
        'discount_price',
        'stock',
        'specifications',
        'is_active'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
