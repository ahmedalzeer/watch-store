<template>
    <div
        class="group bg-white rounded-sm border border-gray-100 min-h-[400px] flex flex-col transition-all duration-500 hover:shadow-2xl hover:border-transparent relative overflow-hidden">

        <!-- Badges -->
        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
            <span v-if="product.discount"
                class="bg-red-500 text-white text-[9px] font-bold px-2 py-1 uppercase tracking-widest rounded-full">
                -{{ product.discount }}%
            </span>
            <span v-if="product.is_new"
                class="bg-blue-600 text-white text-[9px] font-bold px-2 py-1 uppercase tracking-widest rounded-full">
                {{ $t('messages.new_badge') }}
            </span>
        </div>

        <!-- Product Image & Actions Overlay -->
        <div class="relative aspect-[4/5] overflow-hidden bg-gray-50 flex items-center justify-center p-6">
            <img :src="product.image_url" :alt="product.name"
                class="max-h-full max-w-full object-contain transition-transform duration-1000 group-hover:scale-110">

            <!-- Actions Overlay -->
            <div
                class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                <button @click="handleQuickView(product)"
                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-900 shadow-xl hover:bg-black hover:text-white transition-all transform translate-y-8 group-hover:translate-y-0 delay-75">
                    <i class="fa fa-shopping-bag text-sm"></i>
                </button>
                <button @click="$emit('toggle-wishlist', product)"
                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-900 shadow-xl hover:bg-red-500 hover:text-white transition-all transform translate-y-8 group-hover:translate-y-0 delay-150">
                    <i class="fa-regular fa-heart text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 flex flex-col items-center text-center flex-grow space-y-3">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ product.category?.name ||
                $t('messages.watch_fallback') }}</span>
            <Link :href="route('products.show', product.slug)"
                class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors line-clamp-2">
                {{ product.name }}
            </Link>

            <div class="flex items-center gap-2 pt-2">
                <span class="text-base font-black text-gray-900">${{ product.price }}</span>
                <span v-if="product.old_price" class="text-xs text-gray-400 line-through">${{ product.old_price
                    }}</span>
            </div>

            <!-- Star Rating placeholder -->
            <div class="flex gap-1 text-[10px] text-yellow-400">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star-half-alt"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { quickSelectionStore } from '@/Stores/quickSelectionStore.js';

defineProps({
    product: Object
});

defineEmits(['toggle-wishlist']);

const handleQuickView = (product) => {
    quickSelectionStore.open(product.slug);
};
</script>
