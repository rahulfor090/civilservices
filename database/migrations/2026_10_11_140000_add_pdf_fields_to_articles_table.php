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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('pdf_path', 500)->nullable()->after('featured_image');
            $table->foreignId('pdf_media_id')->nullable()->after('pdf_path')->constrained('media')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['pdf_media_id']);
            $table->dropColumn(['pdf_path', 'pdf_media_id']);
        });
    }
};
