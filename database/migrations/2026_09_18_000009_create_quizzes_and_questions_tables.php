<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quiz Categories
        Schema::create('quiz_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 191)->index();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('help-circle');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Quizzes (Daily tests, Full Mocks, Sectional quizzes)
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_category_id')->nullable()->constrained('quiz_categories')->nullOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 191)->index();
            $table->text('instructions')->nullable();
            $table->unsignedInteger('duration_minutes')->default(15);
            $table->decimal('total_marks', 8, 2)->default(20.00);
            $table->decimal('marks_per_question', 5, 2)->default(2.00);
            $table->decimal('negative_marks_per_question', 5, 2)->default(0.66);
            $table->decimal('pass_percentage', 5, 2)->default(40.00);
            $table->boolean('is_free')->default(true);
            $table->boolean('allow_reattempt')->default(true);
            $table->boolean('shuffle_questions')->default(false);
            $table->enum('status', ['draft', 'scheduled', 'published', 'archived'])->default('published')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Question Bank
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->text('stem'); // Question text / markdown
            $table->text('explanation')->nullable(); // Detailed model solution
            $table->string('subject_tag', 100)->nullable()->index();
            $table->string('topic_tag', 100)->nullable()->index();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('question_type', ['single_choice', 'multiple_choice'])->default('single_choice');
            $table->decimal('default_marks', 5, 2)->default(2.00);
            $table->decimal('default_negative_marks', 5, 2)->default(0.66);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Question Options
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false)->index();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();
        });

        // 5. Quiz Questions Pivot
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->string('section_name', 100)->default('General Studies');
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['quiz_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('quiz_categories');
    }
};
