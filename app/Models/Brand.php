<?php

namespace App\Models;

use App\Traits\CreateAtHuman;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasTranslations, SoftDeletes, CreateAtHuman;

    public $translatable = ['name'];
    protected $fillable = ['name', 'slug', 'store_id', 'website'];

    protected $dates = ['deleted_at'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
