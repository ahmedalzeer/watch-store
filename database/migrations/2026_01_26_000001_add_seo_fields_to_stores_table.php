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
        Schema::table('stores', function (Blueprint $table) {
            // SEO Fields
            $table->text('meta_title')->nullable()->after('theme_color');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');

            // Localized SEO Fields
            $table->json('seo_title')->nullable()->after('meta_keywords');
            $table->json('seo_description')->nullable()->after('seo_title');
            $table->json('seo_keywords')->nullable()->after('seo_description');

            // Language and Theme Settings
            $table->string('primary_language')->default('ar')->after('seo_keywords');

            // Structural Data
            $table->json('business_info')->nullable()->after('primary_language'); // Store address, phone, email for schema
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'seo_title',
                'seo_description',
                'seo_keywords',
                'primary_language',
                'business_info'
            ]);
        });
    }
};
