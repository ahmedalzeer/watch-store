<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'store_id',
        'title',
        'description',
        'type',
        'link',
        'active',
        'order'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
