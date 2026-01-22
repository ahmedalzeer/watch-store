<template>

    <Head :title="product.name?.[$page.props.locale]" />

    <div :dir="$page.props.locale === 'ar' ? 'rtl' : 'ltr'" class="bg-white dark:bg-gray-900 min-h-screen pb-20">
        <div class="max-w-7xl mx-auto px-4 pt-10">
            <div class="flex flex-col lg:flex-row gap-12">

                <div class="w-full lg:w-3/5 space-y-4">
                    <div class="aspect-square bg-gray-50 dark:bg-gray-800 overflow-hidden rounded-sm">
                        <img :src="activeImage || product.image_url"
                            class="w-full h-full object-cover transition-opacity duration-500" />
                    </div>
                    <div v-if="product.media?.length > 0" class="grid grid-cols-4 gap-4">
                        <button v-for="media in product.media" :key="media.id" @click="activeImage = media.original_url"
                            class="aspect-square border hover:border-black transition-all overflow-hidden"
                            :class="activeImage === media.original_url ? 'border-black' : 'border-gray-200'">
                            <img :src="media.original_url" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <div class="w-full lg:w-2/5 space-y-8">
                    <div>
                        <span class="text-xs uppercase tracking-[0.3em] text-gray-400 font-bold">
                            {{ product.brand?.name }}
                        </span>
                        <h1 class="text-3xl font-serif mt-2 text-gray-900 dark:text-white uppercase">
                            {{ product.name?.[$page.props.locale] }}
                        </h1>
                        <p class="text-2xl mt-4 font-light text-gray-900 dark:text-gray-100">
                            {{ product.discount_price || product.price }} EGP
                        </p>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                        {{ product.description?.[$page.props.locale] }}
                    </p>

                    <div v-if="product.specifications?.length > 0" class="border-t border-b py-6 dark:border-gray-800">
                        <h3 class="text-xs font-bold uppercase tracking-widest mb-4">{{
                            $t('messages.technical_specifications') }}</h3>
                        <div class="grid grid-cols-1 gap-y-3">
                            <div v-for="(spec, index) in product.specifications" :key="index"
                                class="flex justify-between text-sm border-b border-gray-50 dark:border-gray-800 pb-2">
                                <span class="text-gray-400 uppercase text-[10px] font-bold">{{
                                    spec.key?.[$page.props.locale] }}</span>
                                <span class="text-gray-800 dark:text-gray-200 font-medium">{{
                                    spec.value?.[$page.props.locale] }}</span>
                            </div>
                        </div>
                    </div>

                    <button
                        class="w-full bg-black text-white py-4 uppercase text-xs font-bold tracking-[0.2em] hover:bg-gray-800 transition-all active:scale-95 shadow-lg">
                        {{ $t('messages.add_to_cart') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    product: Object
});

const activeImage = ref(props.product.image_url);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

.font-serif {
    font-family: 'Playfair Display', serif;
}
</style>
