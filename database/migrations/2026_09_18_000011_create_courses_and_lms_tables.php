<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 191)->unique();
            $table->text('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->string('target_exam', 50)->default('upsc_cse'); // upsc_cse, uppcs, bpsc, ras
            $table->enum('level', ['foundation', 'prelims', 'mains', 'interview', 'optional'])->default('foundation');
            $table->string('thumbnail', 255)->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('mrp', 10, 2)->default(0.00);
            $table->unsignedInteger('validity_days')->default(365);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->timestamps();
        });

        // 2. Batches (Classroom / Live / Recorded)
        Schema::create('course_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('batch_code', 50)->unique();
            $table->string('title', 255);
            $table->enum('delivery_mode', ['online', 'offline', 'hybrid'])->default('online');
            $table->string('centre_name', 100)->nullable(); // e.g. Karol Bagh, Mukherjee Nagar, Prayagraj
            $table->date('start_date')->nullable();
            $table->string('class_timing', 100)->nullable(); // e.g. 08:00 AM - 10:30 AM
            $table->string('faculty_names', 255)->nullable();
            $table->boolean('is_enrolment_open')->default(true);
            $table->timestamps();
        });

        // 3. Course Lessons (Videos / Lecture Notes)
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_batch_id')->constrained('course_batches')->cascadeOnDelete();
            $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();
            $table->string('title', 255);
            $table->enum('lesson_type', ['video', 'pdf', 'live', 'quiz'])->default('video');
            $table->string('video_url', 255)->nullable();
            $table->string('notes_pdf_url', 255)->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();
        });

        // 4. Enrollments
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_batch_id')->nullable()->constrained('course_batches')->nullOnDelete();
            $table->enum('status', ['active', 'expired', 'suspended', 'cancelled'])->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id', 'course_batch_id']);
        });

        // 5. Lesson Watch Progress
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_lesson_id')->constrained('course_lessons')->cascadeOnDelete();
            $table->unsignedInteger('watched_seconds')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('last_watched_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'course_lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_batches');
        Schema::dropIfExists('courses');
    }
};
