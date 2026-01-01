<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'store_id',
        'parent_id',
        'is_active',
    ];

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
        return $this->belongsTo(Category::class, 'parent_id');
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
