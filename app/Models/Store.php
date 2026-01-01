<?php

namespace App\Models;

use App\Traits\CreateAtHuman;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, CreateAtHuman;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'user_id',
        'name',
        'subdomain',
        'theme_color',
        'description',
        'contact_email',
        'phone',
        'currency_id',
        'social_links',
        'is_active'
    ];

    protected $casts = [
        'social_links' => 'array',
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
