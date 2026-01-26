<template>
    <ShopLayout>

        <Head :title="$t('messages.our_collection')" />

        <div class="bg-white dark:bg-gray-950 min-h-screen">

            <header class="py-12 border-b dark:border-gray-800 px-6 text-center">
                <h1 class="text-3xl md:text-5xl font-serif text-gray-900 dark:text-white uppercase tracking-widest">
                    {{ $t('messages.luxury_watches') }}
                </h1>
                <p class="mt-4 text-gray-500 text-sm tracking-wide max-w-xl mx-auto uppercase">
                    {{ $t('messages.discover_timeless_elegance') }}
                </p>
            </header>

            <main class="max-w-7xl mx-auto px-4 py-12">
                <div v-if="products.data.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">

                    <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                </div>

                <div v-else class="text-center py-20">
                    <p class="text-gray-400 italic">{{ $t('messages.no_products_found') }}</p>
                </div>

                <div v-if="products.links.length > 3" class="mt-16 flex justify-center">
                    <nav class="flex gap-2">
                        <Link v-for="(link, k) in products.links" :key="k" :href="link.url || '#'" v-html="link.label"
                            class="px-4 py-2 text-xs border transition-all"
                            :class="link.active ? 'bg-black text-white border-black' : 'text-gray-500 border-gray-200 hover:border-black'" />
                    </nav>
                </div>
            </main>
        </div>
    </ShopLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Pages/FrontEnd/Products/ProductCard.vue';

defineProps({
    products: Object,
    brands: Array
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

.font-serif {
    font-family: 'Playfair Display', serif;
}
</style>
