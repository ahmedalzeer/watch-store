<?php

namespace App\Models;

use App\Traits\CreateAtHuman;
use App\Traits\InertiaTranslatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, CreateAtHuman, InertiaTranslatable;

    public $translatable = ['name', 'description', 'seo_title', 'seo_description', 'seo_keywords'];

    protected $fillable = [
        'user_id',
        'name',
        'subdomain',
        'theme_color',
        'theme_color_hex',
        'description',
        'contact_email',
        'contact_phone',
        'phone',
        'currency_id',
        'social_links',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'primary_language',
        'business_info'
    ];

    protected $casts = [
        'social_links' => 'array',
        'business_info' => 'array',
        'seo_title' => 'array',
        'seo_description' => 'array',
        'seo_keywords' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'logo_url',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('store_logo')
            ->singleFile();
    }

    public function getLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('store_logo') ?: "https://ui-avatars.com/api/?name=" . urlencode($this->getTranslation('name', 'ar')) . "&color=7F9CF5&background=EBF4FF";
    }
}
