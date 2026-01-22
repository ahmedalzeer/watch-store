<template>
    <section class="bg-white overflow-hidden relative" v-if="displayBanners.length > 0">
        <Swiper :modules="[Autoplay, EffectFade, Pagination]" :effect="'fade'"
            :autoplay="{ delay: 5000, disableOnInteraction: false }" :pagination="{ clickable: true }"
            :rtl="$page.props.locale === 'ar'" class="banner-slider h-[500px] md:h-[700px]">

            <SwiperSlide v-for="(banner, index) in displayBanners" :key="index">
                <div class="h-full w-full relative flex items-center container mx-auto px-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center w-full">
                        <!-- Left Content -->
                        <div
                            class="text-left rtl:text-right space-y-8 z-20 md:pl-12 rtl:md:pl-0 rtl:md:pr-12 opacity-0 transform translate-y-10 transition-all duration-1000 slide-content">
                            <div class="space-y-4">
                                <span
                                    class="text-[10px] md:text-xs font-bold uppercase tracking-[0.5em] text-blue-600 block transition-all delay-300">New
                                    Arrival</span>
                                <h1
                                    class="text-5xl md:text-8xl font-black text-gray-900 leading-[0.9] transition-all delay-500">
                                    {{ banner.title }}
                                </h1>
                            </div>
                            <p
                                class="text-sm md:text-lg text-gray-400 max-w-lg leading-relaxed transition-all delay-700">
                                {{ banner.description }}
                            </p>

                            <div class="pt-6 transition-all delay-1000">
                                <Link :href="banner.link || route('shop.index')"
                                    class="inline-block bg-black text-white px-10 py-4 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all transform hover:-translate-y-1">
                                    Shop Now
                                </Link>
                            </div>
                        </div>

                        <!-- Right Image -->
                        <div
                            class="relative h-[300px] md:h-[600px] flex items-center justify-center opacity-0 transform scale-95 transition-all duration-1000 slide-image">
                            <!-- Artistic Background Circles -->
                            <div class="absolute w-64 h-64 md:w-[500px] md:h-[500px] bg-gray-50 rounded-full -z-10">
                            </div>
                            <div
                                class="absolute w-48 h-48 md:w-[400px] md:h-[400px] border border-gray-100 rounded-full -z-10 animate-spin-slow">
                            </div>

                            <img :src="banner.image_url" :alt="banner.title"
                                class="max-h-full max-w-full object-contain drop-shadow-[0_35px_35px_rgba(0,0,0,0.1)] hover:scale-105 transition-transform duration-700"
                                @error="handleImageError" />
                        </div>
                    </div>
                </div>
            </SwiperSlide>
        </Swiper>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, EffectFade, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/pagination';

const props = defineProps({
    banners: Array
});

// High Quality Placeholders following the theme
const fallbackBanners = [
    {
        title: 'LUXURY WATCHES',
        description: 'Discover our exclusive collection of premium timepieces that define elegance and precision.',
        image_url: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=1000&auto=format&fit=crop'
    },
    {
        title: 'ELITE STYLE',
        description: 'Explore the finest craftsmanship with our latest arrivals in premium watchmaking.',
        image_url: 'https://images.unsplash.com/photo-1508685096489-7aac29a7dff7?q=80&w=1000&auto=format&fit=crop'
    }
];

const displayBanners = computed(() => {
    return props.banners && props.banners.length > 0 ? props.banners : fallbackBanners;
});

const handleImageError = (e) => {
    e.target.src = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1000&auto=format&fit=crop';
};
</script>

<style scoped>
/* Swiper Active Slide Animations */
:deep(.swiper-slide-active) .slide-content,
:deep(.swiper-slide-active) .slide-image {
    opacity: 1 !important;
    transform: translate(0) scale(1) !important;
}

:deep(.swiper-pagination-bullet) {
    width: 30px;
    height: 2px;
    border-radius: 0;
    background: #000;
    opacity: 0.1;
    transition: all 0.3s;
}

:deep(.swiper-pagination-bullet-active) {
    background: #2563eb;
    opacity: 1;
    width: 60px;
}

.animate-spin-slow {
    animation: spin 20s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>
