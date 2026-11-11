<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mains_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('gs_paper')->default('gs1'); // gs1, gs2, gs3, gs4, essay
            $table->integer('total_marks')->default(250);
            $table->integer('duration_minutes')->default(180);
            $table->string('question_paper_pdf')->nullable();
            $table->string('model_answer_pdf')->nullable();
            $table->text('instructions')->nullable();
            $table->dateTime('submission_deadline')->nullable();
            $table->string('status')->default('active'); // draft, active, archived
            $table->timestamps();
        });

        Schema::create('mains_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mains_test_id')->constrained('mains_tests')->cascadeOnDelete();
            $table->integer('question_number')->default(1);
            $table->text('stem');
            $table->decimal('max_marks', 5, 2)->default(15.00);
            $table->integer('word_limit')->default(250);
            $table->timestamps();
        });

        Schema::create('mains_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mains_test_id')->constrained('mains_tests')->cascadeOnDelete();
            $table->string('candidate_roll_no')->nullable();
            $table->string('submitted_copy_pdf');
            $table->string('evaluated_copy_pdf')->nullable();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('submitted'); // submitted, assigned, under_evaluation, evaluated, rejected
            $table->decimal('total_score', 5, 2)->nullable();
            $table->text('evaluator_remarks')->nullable();
            $table->dateTime('submitted_at');
            $table->dateTime('evaluated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mains_evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mains_submission_id')->constrained('mains_submissions')->cascadeOnDelete();
            $table->foreignId('mains_test_question_id')->constrained('mains_test_questions')->cascadeOnDelete();
            $table->decimal('intro_score', 4, 2)->default(0.00);
            $table->decimal('content_score', 4, 2)->default(0.00);
            $table->decimal('structure_score', 4, 2)->default(0.00);
            $table->decimal('conclusion_score', 4, 2)->default(0.00);
            $table->decimal('total_question_score', 4, 2)->default(0.00);
            $table->text('evaluator_comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mains_evaluation_scores');
        Schema::dropIfExists('mains_submissions');
        Schema::dropIfExists('mains_test_questions');
        Schema::dropIfExists('mains_tests');
    }
};
