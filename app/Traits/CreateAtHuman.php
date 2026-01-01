<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait CreateAtHuman
{
    public function initializeCreateAtHuman()
    {
        $this->append('created_at_human');
    }

    protected function createdAtHuman(): Attribute
    {
        return Attribute::get(fn() => $this->created_at?->diffForHumans());
    }
}
