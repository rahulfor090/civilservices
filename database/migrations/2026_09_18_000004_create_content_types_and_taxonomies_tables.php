<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // daily-news-analysis, daily-editorial, etc.
            $table->string('name', 100);
            $table->string('url_prefix', 100)->index(); // 'daily-updates/daily-news-analysis', 'blog'
            $table->string('template', 50)->default('standard'); // standard, editorial, map, infographic
            $table->string('icon', 50)->nullable();
            $table->boolean('supports_translation')->default(true);
            $table->boolean('is_hierarchical')->default(false);
            $table->json('settings_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('content_type_id')->constrained('content_types')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150);
            $table->text('description')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['section_id', 'language_id']);
            $table->index(['language_id', 'slug']);
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->unsignedInteger('use_count')->default(0);
            $table->timestamps();

            $table->unique(['language_id', 'slug']);
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('designation', 150)->nullable();
            $table->text('bio_html')->nullable();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('twitter_url', 255)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('section_translations');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('content_types');
    }
};
