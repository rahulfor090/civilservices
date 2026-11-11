<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // 'en', 'hi'
            $table->string('name', 50); // English, Hindi
            $table->string('native_name', 50); // English, हिन्दी
            $table->string('direction', 5)->default('ltr'); // ltr, rtl
            $table->string('url_prefix', 20)->default(''); // '' for en, 'hindi' for hi
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
