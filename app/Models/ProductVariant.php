<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'discount_price',
        'stock',
        'is_primary',
        'is_active'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    protected $appends = ['variant_name', 'effective_price', 'image_url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_product_variant', 'product_variant_id', 'attribute_value_id');
    }

    /**
     * Get variant name from attribute values (e.g., "Gold / 42mm / Black")
     */
    public function getVariantNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->attributeValues
            ->map(fn($av) => $av->getTranslation('value', $locale))
            ->implode(' / ');
    }

    /**
     * Get effective price (discount_price if available, otherwise price)
     */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price ?? $this->product->price ?? 0);
    }

    /**
     * Get variant image or fallback to product main image
     */
    public function getImageUrlAttribute(): string
    {
        $variantImage = $this->getFirstMediaUrl('variant_images');
        if ($variantImage) {
            return $variantImage;
        }
        return $this->product->main_image_url ?? 'https://ui-avatars.com/api/?name=Variant';
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock(): bool
    {
        return $this->is_active && $this->stock > 0;
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('variant_images')
            ->singleFile();
    }

    /**
     * Boot method to sync product stock after variant changes
     */
    protected static function booted(): void
    {
        static::saved(function ($variant) {
            $variant->product->syncStockFromVariants();
        });

        static::deleted(function ($variant) {
            $variant->product->syncStockFromVariants();
        });
    }
}
