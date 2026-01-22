<template>
    <div :dir="$page.props.locale === 'ar' ? 'rtl' : 'ltr'" class="bg-white dark:bg-gray-950 min-h-screen">

        <header class="py-16 px-6 text-center border-b dark:border-gray-900">
            <h1 class="text-4xl md:text-6xl font-serif text-gray-900 dark:text-white mb-4 uppercase tracking-tighter">
                {{ $t('messages.exclusive_collection') }}
            </h1>
            <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
                {{ $t('messages.collection_subtitle') }}
            </p>
        </header>

        <main class="max-w-8xl mx-auto px-4 py-12">
            <div class="flex flex-col lg:flex-row gap-8">

                <aside class="w-full lg:w-64 space-y-8">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-[0.2em] mb-4">{{ $t('messages.filter_by_brand')
                            }}</h4>
                        <div class="flex flex-col gap-2">
                            <label v-for="brand in brands" :key="brand.id"
                                class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" class="rounded border-gray-300 text-black focus:ring-0" />
                                <span class="text-sm text-gray-600 group-hover:text-black transition-colors">{{
                                    brand.name }}</span>
                            </label>
                        </div>
                    </div>
                </aside>

                <div class="flex-1">
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-px bg-gray-100 dark:bg-gray-800 border border-gray-100 dark:border-gray-800">
                        <ProductCard v-for="product in products.data" :key="product.id" :product="product"
                            @quick-view="handleQuickView" />
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import ProductCard from '@/Components/ProductCard.vue';
defineProps({ products: Object, brands: Array });

const handleQuickView = (product) => {
    console.log("فتح العرض السريع لـ:", product.name.ar);
};
</script>

<style>
/* خطوط فخمة مخصصة */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;700&display=swap');

.font-serif {
    font-family: 'Playfair Display', serif;
}

body {
    font-family: 'Inter', sans-serif;
}

[dir="rtl"] body {
    font-family: 'Cairo', sans-serif;
}
</style>
