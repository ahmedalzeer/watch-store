<template>
    <section class="py-24 transition-colors duration-500" :class="customClass">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                <div class="space-y-2">
                    <span class="text-purple-600 font-black text-[10px] uppercase tracking-[0.4em] block italic">
                        {{ subtitle }}
                    </span>
                    <h2 class="text-4xl md:text-5xl font-serif dark:text-white uppercase italic">
                        {{ title }}
                    </h2>
                </div>

                <div class="flex gap-4">
                    <button :id="`prev-${id}`"
                        class="p-4 border border-black/10 dark:border-white/10 rounded-full hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition-all">
                        <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button :id="`next-${id}`"
                        class="p-4 border border-black/10 dark:border-white/10 rounded-full hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition-all">
                        <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <Swiper :modules="[SwiperNavigation, SwiperAutoplay]" :slides-per-view="1.2" :space-between="20"
                :navigation="{ prevEl: `#prev-${id}`, nextEl: `#next-${id}` }"
                :breakpoints="{ '640': { slidesPerView: 2 }, '1024': { slidesPerView: 4 } }" class="!pb-10">
                <SwiperSlide v-for="product in products" :key="product.id">
                    <ProductCard :product="product" />
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation as SwiperNavigation, Autoplay as SwiperAutoplay } from 'swiper/modules';
import ProductCard from '@/Pages/FrontEnd/Products/ProductCard.vue';

defineProps({
    id: String,
    title: String,
    subtitle: String,
    products: Array,
    customClass: String
});
</script>
