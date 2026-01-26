<?php

namespace App\Services;

use App\Models\Store;

class StoreService
{
    /**
     * Get SEO metadata for a store
     */
    public function getStoreSeoMetadata(Store $store, string $locale = 'ar'): array
    {
        // Use localized SEO fields if available, fallback to defaults
        $seoTitle = $store->seo_title[$locale] ?? $store->meta_title ?? $store->getTranslation('name', $locale);
        $seoDescription = $store->seo_description[$locale] ?? $store->meta_description ?? $store->getTranslation('description', $locale);
        $seoKeywords = $store->seo_keywords[$locale] ?? $store->meta_keywords ?? '';

        // Limit description to 160 characters
        if (strlen($seoDescription) > 160) {
            $seoDescription = substr($seoDescription, 0, 157) . '...';
        }

        return [
            'title' => $seoTitle,
            'description' => $seoDescription,
            'keywords' => $seoKeywords,
            'og_title' => $seoTitle,
            'og_description' => $seoDescription,
            'og_image' => $store->getLogoUrlAttribute(),
            'og_url' => $this->getStoreUrl($store),
            'canonical_url' => $this->getStoreUrl($store),
            'language' => $store->primary_language ?? 'ar',
        ];
    }

    /**
     * Generate structured data (Schema.org) for the store
     */
    public function generateStoreSchemaData(Store $store, string $locale = 'ar'): array
    {
        $businessInfo = $store->business_info ?? [];

        return [
            '@context' => 'https://schema.org/',
            '@type' => 'Store',
            'name' => $store->getTranslation('name', $locale),
            'description' => $store->getTranslation('description', $locale),
            'url' => $this->getStoreUrl($store),
            'image' => $store->getLogoUrlAttribute(),
            'telephone' => $businessInfo['phone'] ?? $store->contact_email ?? '',
            'email' => $businessInfo['email'] ?? $store->contact_email ?? '',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $businessInfo['street_address'] ?? '',
                'addressLocality' => $businessInfo['city'] ?? '',
                'addressCountry' => $businessInfo['country'] ?? 'SA',
            ],
            'openingHoursSpecification' => $businessInfo['opening_hours'] ?? [],
            'sameAs' => $store->social_links ?? [],
            'priceRange' => '$$', // Can be customized based on products
        ];
    }

    /**
     * Get store URL based on subdomain or main domain
     */
    public function getStoreUrl(Store $store): string
    {
        $appUrl = config('app.url');
        $host = parse_url($appUrl, PHP_URL_HOST);

        // If subdomain is configured, use it
        if ($store->subdomain && $store->subdomain !== 'www') {
            return str_replace($host, "{$store->subdomain}.{$host}", $appUrl);
        }

        return $appUrl;
    }

    /**
     * Get robots.txt directives
     */
    public function getRobotsMeta(Store $store): string
    {
        $directives = ['index', 'follow'];

        if (!$store->is_active) {
            $directives = ['noindex', 'nofollow'];
        }

        return implode(', ', $directives);
    }

    /**
     * Generate sitemap entries for a store
     */
    public function generateSitemapUrls(Store $store): array
    {
        $baseUrl = $this->getStoreUrl($store);
        $urls = [];

        // Home page
        $urls[] = [
            'url' => $baseUrl,
            'lastmod' => $store->updated_at->toDateTimeString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        // Category pages
        $categories = $store->categories()->where('is_active', true)->get();
        foreach ($categories as $category) {
            $urls[] = [
                'url' => "{$baseUrl}/category/{$category->slug}",
                'lastmod' => $category->updated_at->toDateTimeString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        // Product pages
        $products = $store->products()->where('is_active', true)->get();
        foreach ($products as $product) {
            $urls[] = [
                'url' => "{$baseUrl}/product/{$product->slug}",
                'lastmod' => $product->updated_at->toDateTimeString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        return $urls;
    }
}
