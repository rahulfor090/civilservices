<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->string('revisionable_type', 100);
            $table->unsignedBigInteger('revisionable_id');
            $table->json('snapshot_json');
            $table->string('change_summary', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['revisionable_type', 'revisionable_id']);
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path', 191)->unique();
            $table->string('to_path', 255);
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedBigInteger('hits')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50); // header_main, mega_menu, footer_centres, footer_links
            $table->string('name', 100);
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['code', 'language_id']);
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
            $table->string('label', 150);
            $table->string('url', 255);
            $table->string('link_type', 50)->default('internal'); // internal, external, category, modal
            $table->string('link_target', 20)->default('_self');
            $table->string('icon_class', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('homepage_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('key', 50); // hero_slider, toppers_carousel, latest_programmes, recent_updates, achievers_strip
            $table->string('title', 150);
            $table->string('slot_type', 50); // hero, carousel, tabbed_list, card_grid, banner
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('config_json')->nullable();
            $table->timestamps();

            $table->unique(['language_id', 'key']);
        });

        Schema::create('homepage_slot_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained('homepage_slots')->cascadeOnDelete();
            $table->string('item_type', 50)->default('custom'); // custom, article, video, topper
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreignId('image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('headline', 255);
            $table->text('subheadline')->nullable();
            $table->string('cta_label', 100)->nullable();
            $table->string('cta_url', 255)->nullable();
            $table->string('cta_label_alt', 100)->nullable(); // alternative language CTA
            $table->string('cta_url_alt', 255)->nullable();
            $table->json('utm_json')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('placement', 50)->default('top_strip'); // top_strip, sidebar, inline
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->foreignId('desktop_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('mobile_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('headline', 255);
            $table->string('subheadline', 255)->nullable();
            $table->string('cta_label', 100)->nullable();
            $table->string('url', 255);
            $table->string('target', 20)->default('_self');
            $table->json('utm_json')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('homepage_slot_items');
        Schema::dropIfExists('homepage_slots');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigation_menus');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('revisions');
    }
};
