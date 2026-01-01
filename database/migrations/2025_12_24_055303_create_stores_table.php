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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('name');
            $table->json('description')->nullable();
            $table->string('subdomain')->unique();
            $table->string('theme_color')->default('#7e3af2');
            $table->string('contact_email')->nullable();
            $table->string('phone')->nullable();
            $table->json('social_links')->nullable();
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
