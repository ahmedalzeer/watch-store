<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\InertiaTranslatable;
use Spatie\Translatable\HasTranslations;

class AttributeValue extends Model
{
    use HasTranslations, InertiaTranslatable, HasFactory;

    protected $fillable = ['attribute_id', 'value'];

    public $translatable = ['value'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'attribute_value_product_variant', 'attribute_value_id', 'product_variant_id');
    }
}
