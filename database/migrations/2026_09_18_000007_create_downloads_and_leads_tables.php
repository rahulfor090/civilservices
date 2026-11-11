<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('download_categories')->nullOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['language_id', 'slug']);
        });

        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('download_categories')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('file_path', 255);
            $table->string('file_size', 50)->nullable(); // e.g. "2.4 MB"
            $table->string('exam', 100)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->boolean('is_gated')->default(false);
            $table->boolean('requires_login')->default(false);
            $table->unsignedBigInteger('download_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 150);
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 150)->index();
            $table->string('mobile', 20)->index();
            $table->string('country_code', 10)->default('+91');
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->string('interest', 100)->nullable(); // UPSC, State PCS, Optional, etc.
            $table->json('interest_tags_json')->nullable();
            $table->string('source', 50)->default('website_form'); // form, download, webinar, mock, slot, whatsapp
            $table->string('source_page', 255)->nullable();
            $table->json('utm_json')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'counselling_scheduled', 'won', 'lost'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('consent')->default(true);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 50); // call, email, sms, note, status_change
            $table->string('outcome', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('download_categories');
    }
};
