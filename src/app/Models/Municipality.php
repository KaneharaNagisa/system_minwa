<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    protected $fillable = [
        'name',
        'region_name',
    ];

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }
}
