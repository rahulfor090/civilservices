<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleSection extends Model
{
    protected $fillable = [
        'article_id',
        'heading',
        'anchor',
        'sort_order',
        'body_html',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
