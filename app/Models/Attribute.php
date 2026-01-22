<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\InertiaTranslatable;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations, InertiaTranslatable, HasFactory;

    protected $fillable = ['store_id', 'name'];

    public $translatable = ['name'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
