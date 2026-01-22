<template>
    <div
        class="group relative flex flex-col bg-white dark:bg-gray-950 transition-all duration-700 overflow-hidden hover:shadow-[0_20px_50px_rgba(0,0,0,0.1)] border-transparent border hover:border-gray-100 dark:hover:border-gray-800">

        <div class="relative aspect-[3/4] overflow-hidden bg-[#f9f9f9] dark:bg-gray-900">
            <div v-if="product.discount_price"
                class="absolute top-0 ltr:left-0 rtl:right-0 z-10 bg-purple-600 text-white text-[9px] font-black px-3 py-1.5 uppercase tracking-widest shadow-lg">
                {{ calculateDiscount(product.price, product.discount_price) }}% {{ $t('messages.off') }}
            </div>

            <img :src="product.image_url" :alt="getTranslation(product.name)"
                class="w-full h-full object-cover transition-transform duration-[1.5s] cubic-bezier(0.4, 0, 0.2, 1) group-hover:scale-110" />

            <div
                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col items-center justify-end p-6 gap-3">
                <button @click="$emit('quick-view', product)"
                    class="w-full bg-white text-black py-3 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-purple-600 hover:text-white transition-colors transform translate-y-4 group-hover:translate-y-0 duration-500">
                    {{ $t('messages.quick_view') }}
                </button>
            </div>
        </div>

        <div class="p-6 flex flex-col items-center text-center space-y-3">
            <span class="text-[9px] uppercase tracking-[0.4em] text-purple-600 font-black italic">
                {{ getTranslation(product.brand?.name) || 'Exquisite Timepiece' }}
            </span>

            <h3 class="text-sm md:text-base font-serif italic text-gray-900 dark:text-white leading-tight">
                <a :href="route('products.show', product.slug || '#')" class="hover:opacity-70 transition-opacity">
                    {{ getTranslation(product.name) }}
                </a>
            </h3>

            <div class="flex items-center gap-3 pt-1">
                <span v-if="product.discount_price"
                    class="text-[11px] text-gray-400 line-through decoration-purple-500/50">
                    {{ product.price }}
                </span>
                <span class="text-base font-serif text-gray-950 dark:text-gray-100 tracking-tighter">
                    {{ product.discount_price || product.price }}
                    <span class="text-[9px] font-sans uppercase tracking-widest ml-1">EGP</span>
                </span>
            </div>

            <div class="pt-4 flex gap-6 opacity-40 group-hover:opacity-100 transition-opacity duration-500">
                <div v-for="(spec, index) in product.specifications?.slice(0, 2)" :key="index"
                    class="flex flex-col items-center border-x border-gray-100 dark:border-gray-800 px-4">
                    <span class="text-[8px] text-gray-400 uppercase tracking-tighter mb-1">{{ getTranslation(spec.key)
                    }}</span>
                    <span class="text-[9px] font-bold text-gray-700 dark:text-gray-300">{{ getTranslation(spec.value)
                    }}</span>
                </div>
            </div>
        </div>

        <button
            class="w-full bg-gray-950 dark:bg-purple-800 text-white py-4 text-[10px] font-black uppercase tracking-[0.3em] hover:bg-purple-700 transition-colors border-t border-white/5">
            {{ $t('messages.add_to_cart') }}
        </button>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';

const props = defineProps({ product: Object });
defineEmits(['quick-view']);

const page = usePage();

// دالة ذكية لمعالجة الترجمة ومنع ظهور الكود الخام (JSON)
const getTranslation = (field) => {
    if (!field) return '';
    const locale = page.props.locale;

    // إذا كان الحقل كائن (Object) ناتج عن حزمة Translatable
    if (typeof field === 'object') {
        return field[locale] || field['en'] || Object.values(field)[0];
    }

    // إذا كان نصاً عادياً (Fallback)
    try {
        const parsed = JSON.parse(field);
        return parsed[locale] || parsed['en'] || field;
    } catch (e) {
        return field;
    }
};

const calculateDiscount = (price, discount) => {
    return Math.round(((price - discount) / price) * 100);
};
</script>
