<template>
    <section class="relative h-[90vh] w-full overflow-hidden bg-gray-900">
        <Swiper :modules="[SwiperAutoplay, SwiperEffectFade, SwiperPagination]" :effect="'fade'"
            :autoplay="{ delay: 6000, disableOnInteraction: false }" :pagination="{ clickable: true }"
            class="h-full w-full">

            <SwiperSlide v-for="(banner, index) in banners" :key="index">
                <div class="relative h-full w-full group">
                    <img :src="banner.image" @error="handleImageError"
                        class="h-full w-full object-cover transition-transform duration-[10s] group-hover:scale-110"
                        alt="Watch Collection" />

                    <div
                        class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/20 to-black/80 flex items-center justify-center text-center">
                        <div class="max-w-5xl px-6">
                            <h2
                                class="text-5xl md:text-7xl lg:text-8xl font-serif text-white uppercase tracking-normal leading-[1.1] mb-6 drop-shadow-2xl animate-fadeInUp">
                                {{ banner.title }}
                            </h2>
                            <p
                                class="text-white/90 text-sm md:text-xl font-light tracking-[0.3em] uppercase max-w-2xl mx-auto mb-10 italic">
                                {{ banner.subtitle }}
                            </p>
                            <Link :href="route('shop.index')"
                                class="inline-block px-12 py-4 bg-white text-black text-[11px] font-black uppercase tracking-widest hover:bg-purple-700 hover:text-white transition-all transform hover:-translate-y-1 shadow-2xl">
                                {{ $t('messages.explore_collection') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </SwiperSlide>
        </Swiper>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay as SwiperAutoplay, EffectFade as SwiperEffectFade, Pagination as SwiperPagination } from 'swiper/modules';

defineProps({ banners: Array });

const handleImageError = (e) => {

    e.target.src = 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=1920';
};
</script>
