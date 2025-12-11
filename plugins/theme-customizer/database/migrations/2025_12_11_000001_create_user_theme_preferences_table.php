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
        if (!Schema::hasTable('user_theme_preferences')) {
            Schema::create('user_theme_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contact_id')->unique();
                $table->boolean('dark_mode')->default(false);
                $table->timestamps();
                
                $table->index('contact_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_theme_preferences');
    }
};
