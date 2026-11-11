<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->constrained('content_types')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('authors')->nullOnDelete();
            $table->uuid('translation_group_id')->index(); // links en and hi articles together
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            
            $table->string('slug', 191);
            $table->string('title', 255);
            $table->string('subtitle', 255)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body_html')->nullable();
            $table->string('featured_image', 500)->nullable();
            
            $table->foreignId('hero_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedSmallInteger('reading_minutes')->default(5);
            
            $table->enum('status', ['draft', 'in_review', 'scheduled', 'published', 'unpublished', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->integer('sort_order')->default(0);
            
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('canonical_url_override', 255)->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            
            $table->string('source_name', 100)->nullable(); // The Hindu, Indian Express, PIB, etc.
            $table->string('source_url', 255)->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for sub-second retrieval at 100k+ articles
            $table->unique(['language_id', 'slug']);
            $table->index(['content_type_id', 'language_id', 'status', 'published_at'], 'articles_listing_idx');
            $table->index(['section_id', 'status', 'published_at'], 'articles_section_idx');
            $table->index(['published_at']);
        });

        Schema::create('article_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('heading', 255);
            $table->string('anchor', 100);
            $table->integer('sort_order')->default(0);
            $table->longText('body_html');
            $table->timestamps();

            $table->index(['article_id', 'sort_order']);
        });

        Schema::create('article_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('type', 50); // pyq, source_citation, callout, key_points, faq
            $table->integer('sort_order')->default(0);
            $table->json('payload_json');
            $table->timestamps();

            $table->index(['article_id', 'type']);
        });

        Schema::create('article_tags', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->primary(['article_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tags');
        Schema::dropIfExists('article_blocks');
        Schema::dropIfExists('article_sections');
        Schema::dropIfExists('articles');
    }
};
