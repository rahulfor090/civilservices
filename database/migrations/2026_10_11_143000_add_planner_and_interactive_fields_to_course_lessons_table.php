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
        Schema::table('course_lessons', function (Blueprint $table) {
            // Flexible string lesson_type to support video, live, quiz, mains, phone_session, test_series, pdf
            // We keep column but modify it or add helper
            $table->string('activity_type', 50)->default('video')->after('title');
            
            // Scheduling & Planner
            $table->date('scheduled_date')->nullable()->after('activity_type');
            $table->time('scheduled_time')->nullable()->after('scheduled_date');
            $table->dateTime('scheduled_at')->nullable()->after('scheduled_time');
            $table->dateTime('unlock_at')->nullable()->after('scheduled_at');
            
            // Phone interactive session fields
            $table->string('mentor_name', 150)->nullable()->after('unlock_at');
            $table->string('mentor_phone', 50)->nullable()->after('mentor_name');
            $table->string('call_window', 100)->nullable()->after('mentor_phone'); // e.g. "06:00 PM - 07:00 PM"
            $table->text('session_instructions')->nullable()->after('call_window');
            
            // Live interaction / meeting fields
            $table->string('meeting_platform', 50)->nullable()->after('session_instructions'); // Zoom, Google Meet, etc.
            $table->string('meeting_link', 500)->nullable()->after('meeting_platform');
            $table->string('meeting_password', 50)->nullable()->after('meeting_link');
            
            // Mains subjective question fields
            $table->foreignId('mains_test_id')->nullable()->after('meeting_password')->constrained('mains_tests')->nullOnDelete();
            $table->text('mains_question_text')->nullable()->after('mains_test_id');
            $table->unsignedSmallInteger('mains_marks')->nullable()->after('mains_question_text');
            $table->unsignedSmallInteger('mains_word_limit')->nullable()->after('mains_marks');
            
            // General description & alerts
            $table->text('description')->nullable()->after('mains_word_limit');
            $table->unsignedSmallInteger('alert_minutes_before')->default(30)->after('description'); // e.g. 30 mins before (4:30 PM for 5:00 PM class)
        });

        Schema::table('course_batches', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_batches', function (Blueprint $table) {
            $table->dropColumn(['end_date']);
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropForeign(['mains_test_id']);
            $table->dropColumn([
                'activity_type',
                'scheduled_date',
                'scheduled_time',
                'scheduled_at',
                'unlock_at',
                'mentor_name',
                'mentor_phone',
                'call_window',
                'session_instructions',
                'meeting_platform',
                'meeting_link',
                'meeting_password',
                'mains_test_id',
                'mains_question_text',
                'mains_marks',
                'mains_word_limit',
                'description',
                'alert_minutes_before',
            ]);
        });
    }
};
