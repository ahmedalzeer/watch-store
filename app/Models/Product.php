<?php

namespace App\Models;

use App\Traits\CreateAtHuman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasTranslations, InteractsWithMedia, CreateAtHuman;

    protected $fillable = [
        'store_id',
        'category_id',
        'brand_id',
        'name',
        'description',
        'slug',
        'sku',
        'price',
        'discount_price',
        'stock',
        'specifications',
        'is_active',
        'main_menu',
        'main_store',
        'condition'
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'specifications' => 'json',
        'is_active' => 'boolean',
        'main_menu' => 'boolean',
        'main_store' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['main_image_url', 'image_url'];

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

    public function thumbnail()
    {
        return $this->morphOne(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'model')
            ->where('collection_name', 'product_gallery')
            ->orderBy('custom_properties->is_main', 'desc')
            ->orderBy('order_column');
    }

    public function getMainImageUrlAttribute()
    {
        $mainMedia = $this->getMedia('product_gallery')
            ->first(fn($media) => $media->getCustomProperty('is_main') === true);

        if ($mainMedia) {
            return $mainMedia->getFullUrl();
        }

        return $this->getFirstMediaUrl('product_gallery') ?: 'https://ui-avatars.com/api/?name=Product';
    }

    public function getImageUrlAttribute()
    {
        return $this->thumbnail ? $this->thumbnail->getFullUrl() : 'https://ui-avatars.com/api/?name=Product';
    }
}
