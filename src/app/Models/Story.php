<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Story extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'municipality_id',
        'title',
        'title_kana',
        'summary',
        'body_markdown',
        'main_image_path',
        'spot_name',
        'spot_address',
        'spot_map_url',
        'related_site_url',
        'admin_memo',
        'source',
        'source_citation_checked',
        'expression_rewritten_checked',
    ];

    protected $casts = [
        'source_citation_checked' => 'boolean',
        'expression_rewritten_checked' => 'boolean',
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
