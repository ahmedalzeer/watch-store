<?php

namespace App\Models;

use App\Traits\CreateAtHuman;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, SoftDeletes, CreateAtHuman;

    protected $fillable = [
        'name',
        'slug',
        'store_id',
        'parent_id',
        'is_active',
    ];

    protected $dates = ['deleted_at'];

    protected $appends = [
        'icon_url',
    ];

    public array $translatable = ['name'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getIconUrlAttribute()
    {
        $media = $this->getFirstMedia('category_icons');
        return $media ? $media->getUrl() : null;
    }
}
