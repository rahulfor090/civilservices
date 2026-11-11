<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_type_id',
        'quiz_id',
        'section_id',
        'author_id',
        'translation_group_id',
        'language_id',
        'slug',
        'title',
        'subtitle',
        'excerpt',
        'body_html',
        'featured_image',
        'pdf_path',
        'pdf_media_id',
        'hero_media_id',
        'og_media_id',
        'reading_minutes',
        'status',
        'published_at',
        'scheduled_at',
        'expires_at',
        'is_featured',
        'is_premium',
        'allow_comments',
        'view_count',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url_override',
        'robots_index',
        'robots_follow',
        'source_name',
        'source_url',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_premium' => 'boolean',
        'allow_comments' => 'boolean',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
    ];

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function heroMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_media_id');
    }

    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    public function pdfMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'pdf_media_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ArticleSection::class)->orderBy('sort_order');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(ArticleBlock::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tags');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function translationTwin(): ?Article
    {
        return Article::where('translation_group_id', $this->translation_group_id)
            ->where('id', '!=', $this->id)
            ->first();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->whereHas('language', function ($q) use ($locale) {
            $q->where('code', $locale);
        });
    }

    public function getUrlAttribute(): string
    {
        $prefix = $this->language && $this->language->code === 'hi' ? '/hindi' : '';
        $typePrefix = $this->contentType ? $this->contentType->url_prefix : 'daily-updates/daily-news-analysis';
        return url($prefix . '/' . trim($typePrefix, '/') . '/' . $this->slug);
    }

    public function getFeatureImageUrlAttribute(): ?string
    {
        if ($this->heroMedia) {
            return $this->heroMedia->url;
        }
        if ($this->ogMedia) {
            return $this->ogMedia->url;
        }
        if ($this->featured_image) {
            return preg_match('#^https?://#i', $this->featured_image) 
                ? $this->featured_image 
                : asset($this->featured_image);
        }
        return null;
    }

    public function getHasUploadedPdfAttribute(): bool
    {
        return !empty($this->pdf_media_id) || !empty($this->pdf_path);
    }

    public function getUploadedPdfUrlAttribute(): ?string
    {
        if ($this->pdfMedia) {
            return $this->pdfMedia->url;
        }
        if ($this->pdf_path) {
            return preg_match('#^https?://#i', $this->pdf_path) 
                ? $this->pdf_path 
                : asset($this->pdf_path);
        }
        return null;
    }
}
