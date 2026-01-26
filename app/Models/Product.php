<?php

namespace App\Models;

use App\Traits\CreateAtHuman;
use App\Traits\InertiaTranslatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasTranslations, InteractsWithMedia, CreateAtHuman, InertiaTranslatable;

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

    protected $appends = ['main_image_url', 'image_url', 'has_variants', 'total_variant_stock', 'effective_stock'];

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

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function primaryVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderItem::class, 'product_id', 'id', 'id', 'order_id');
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

    /**
     * Check if product has variants
     */
    public function getHasVariantsAttribute(): bool
    {
        return $this->variants()->count() > 0;
    }

    /**
     * Get total stock from all variants
     */
    public function getTotalVariantStockAttribute(): int
    {
        return $this->variants()->where('is_active', true)->sum('stock');
    }

    /**
     * Get effective stock (from variants if exists, otherwise from product)
     * هذا هو المخزون الفعلي المتاح للبيع
     */
    public function getEffectiveStockAttribute(): int
    {
        if ($this->has_variants) {
            return $this->total_variant_stock;
        }
        return $this->stock ?? 0;
    }

    /**
     * Get active variants with their attribute values
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        return $this->effective_stock > 0;
    }

    /**
     * Get available attributes for this product (from its variants)
     */
    public function getAvailableAttributesAttribute()
    {
        return $this->variants()
            ->with('attributeValues.attribute')
            ->get()
            ->pluck('attributeValues')
            ->flatten()
            ->pluck('attribute')
            ->unique('id')
            ->values();
    }

    /**
     * Sync stock from variants (call this after updating variants)
     */
    public function syncStockFromVariants(): void
    {
        if ($this->has_variants) {
            $this->update(['stock' => $this->total_variant_stock]);
        }
    }
}
