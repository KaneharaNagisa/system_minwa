<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryImage extends Model
{
    protected $fillable = [
        'story_id',
        'path',
        'original_name',
        'display_order',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
