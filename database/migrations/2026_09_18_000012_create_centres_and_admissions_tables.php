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
        Schema::create('centres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('city');
            $table->string('state');
            $table->text('address');
            $table->string('pincode')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('google_maps_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('facilities')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('counselling_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->foreignId('centre_id')->nullable()->constrained('centres')->nullOnDelete();
            $table->string('medium')->default('en');
            $table->string('call_time_window')->default('morning'); // morning, afternoon, evening
            $table->string('target_year')->default('2026');
            $table->string('interest_category')->nullable();
            $table->string('status')->default('pending'); // pending, contacted, confirmed, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('interview_guidance_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('mobile');
            $table->string('email');
            $table->string('upsc_roll_number');
            $table->string('optional_subject');
            $table->string('daf_file_path')->nullable();
            $table->string('medium')->default('en');
            $table->date('interview_slot_date')->nullable();
            $table->foreignId('centre_id')->nullable()->constrained('centres')->nullOnDelete();
            $table->string('status')->default('registered'); // registered, slot_assigned, mock_completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('recruitment_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('mobile');
            $table->string('email');
            $table->string('role_applied'); // faculty, content_writer
            $table->string('subject_specialization');
            $table->integer('experience_years')->default(0);
            $table->string('resume_path')->nullable();
            $table->string('status')->default('applied'); // applied, shortlisted, interviewed, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_applications');
        Schema::dropIfExists('interview_guidance_bookings');
        Schema::dropIfExists('counselling_bookings');
        Schema::dropIfExists('centres');
    }
};
