<?php

// SEO Configuration file
return [
    /*
    |--------------------------------------------------------------------------
    | SEO Default Settings
    |--------------------------------------------------------------------------
    */

    // Default theme color if store doesn't have one
    'default_theme_color' => '#7F9CF5',

    // Default primary language
    'default_language' => 'ar',

    // Meta tag character limits
    'meta_title_max_length' => 60,
    'meta_description_max_length' => 160,
    'meta_keywords_max_length' => 255,

    // SEO keywords per locale (used as fallback suggestions)
    'default_keywords' => [
        'ar' => 'متجر إلكتروني, تسوق أون لاين, منتجات أصلية',
        'en' => 'online store, e-commerce, original products',
    ],

    // Open Graph default image (if store doesn't have logo)
    'og_default_image' => '/images/og-default.jpg',

    // Organization type for schema.org
    'schema_type' => 'Store',

    // Enable automatic sitemap generation
    'generate_sitemaps' => true,

    // Enable structured data (JSON-LD)
    'enable_structured_data' => true,

    // Security headers
    'security_headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
    ],

    // Supported locales for SEO
    'supported_locales' => ['ar', 'en'],

    // Robots.txt settings
    'robots' => [
        'user_agent' => '*',
        'allow' => '/',
        'disallow' => ['/admin', '/vendor', '/dashboard'],
        'sitemap' => '/sitemap.xml',
    ],

    // Schema.org business categories
    'business_categories' => [
        'general_store' => 'Store',
        'electronics' => 'ElectronicsStore',
        'fashion' => 'ClothingStore',
        'jewelry' => 'JewelryStore',
        'grocery' => 'GroceryStore',
    ],

    // Social media platforms for schema
    'social_platforms' => ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest'],

    // Cache SEO data (in seconds, 0 = no cache)
    'cache_duration' => 3600,
];
