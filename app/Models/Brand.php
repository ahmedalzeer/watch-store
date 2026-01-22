<?php

namespace App\Models;

use App\Traits\CreateAtHuman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use HasFactory, HasTranslations, SoftDeletes, CreateAtHuman, InteractsWithMedia;

    public $translatable = ['name'];
    protected $fillable = ['name', 'slug', 'store_id', 'website', 'is_featured', 'is_active', 'main_menu', 'main_store'];

    protected $appends = ['logo_url'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('brand_logos') ?: null;
    }
}
