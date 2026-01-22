<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\InertiaTranslatable;
use Spatie\Translatable\HasTranslations;

class Currency extends Model
{
    use HasTranslations, HasFactory, InertiaTranslatable;

    protected $fillable = ['name', 'code', 'symbol', 'exchange_rate', 'is_active'];

    public array $translatable = ['name'];
}
