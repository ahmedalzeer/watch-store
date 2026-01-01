<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Currency extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'code', 'symbol', 'exchange_rate', 'is_active'];

    public array $translatable = ['name'];
}
