<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Candidate Quiz Attempts
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->enum('status', ['in_progress', 'completed', 'abandoned', 'expired'])->default('in_progress')->index();
            $table->decimal('score', 8, 2)->default(0.00);
            $table->decimal('max_score', 8, 2)->default(0.00);
            $table->decimal('marks_positive', 8, 2)->default(0.00);
            $table->decimal('marks_negative', 8, 2)->default(0.00);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('incorrect_count')->default(0);
            $table->unsignedInteger('unanswered_count')->default(0);
            $table->unsignedInteger('marked_for_review_count')->default(0);
            $table->decimal('accuracy_pct', 5, 2)->default(0.00);
            $table->unsignedInteger('all_india_rank')->nullable();
            $table->decimal('percentile', 5, 2)->nullable();
            $table->unsignedInteger('tab_switches_count')->default(0);
            $table->json('section_analytics_json')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'quiz_id']);
        });

        // 2. Candidate Responses per Question
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->json('selected_option_ids')->nullable(); // Array of option IDs selected
            $table->boolean('is_marked_for_review')->default(false);
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 5, 2)->default(0.00);
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
    }
};
