<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'slug', 'store_id', 'website'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
