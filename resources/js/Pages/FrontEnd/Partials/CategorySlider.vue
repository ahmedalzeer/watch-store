<template>
    <section class="py-24 bg-white overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="space-y-4 rtl:text-right">
                    <span class="text-blue-600 font-bold uppercase tracking-[0.3em] text-[10px]">Categories</span>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight">
                        Explore Popular
                    </h2>
                </div>
                <!-- Controls -->
                <div class="flex gap-4">
                    <button
                        class="cat-prev h-12 w-12 rounded-full border border-gray-100 flex items-center justify-center hover:bg-black hover:text-white transition-all">
                        <i class="fas fa-chevron-left rtl:rotate-180 text-xs"></i>
                    </button>
                    <button
                        class="cat-next h-12 w-12 rounded-full border border-gray-100 flex items-center justify-center hover:bg-black hover:text-white transition-all">
                        <i class="fas fa-chevron-right rtl:rotate-180 text-xs"></i>
                    </button>
                </div>
            </div>

            <Swiper :modules="[Autoplay, Navigation]" :slides-per-view="1.2" :space-between="20"
                :rtl="$page.props.locale === 'ar'" :navigation="{ prevEl: '.cat-prev', nextEl: '.cat-next' }"
                :breakpoints="{
                    640: { slidesPerView: 2, spaceBetween: 30 },
                    768: { slidesPerView: 3, spaceBetween: 40 },
                    1024: { slidesPerView: 4, spaceBetween: 40 }
                }" :autoplay="{ delay: 4000 }" :loop="true" class="category-slider">

                <SwiperSlide v-for="(cat, index) in categories" :key="index">
                    <Link :href="route('shop.index', { category: cat.slug })" class="group block">
                        <div
                            class="relative aspect-square rounded-full bg-gray-50 flex items-center justify-center p-12 transition-all duration-500 group-hover:bg-blue-50">
                            <img :src="cat.image_url" :alt="cat.name"
                                class="max-h-full max-w-full object-contain transition-transform duration-700 group-hover:scale-110 group-hover:-rotate-6 drop-shadow-xl">

                            <!-- Overlay Label -->
                            <div
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <div
                                    class="bg-white px-6 py-2 shadow-2xl rounded-full text-[10px] font-bold uppercase tracking-widest text-black transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    Explore
                                </div>
                            </div>
                        </div>
                        <h3
                            class="text-center mt-8 text-sm font-bold uppercase tracking-widest text-gray-400 group-hover:text-black transition-colors">
                            {{ cat.name }}</h3>
                    </Link>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';

const categories = [
    { name: 'Classic', slug: 'classic', image_url: 'https://images.unsplash.com/photo-1524592093033-6fb35070fc16?q=80&w=500&auto=format&fit=crop' },
    { name: 'Luxury', slug: 'luxury', image_url: 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?q=80&w=500&auto=format&fit=crop' },
    { name: 'Smart', slug: 'smart', image_url: 'https://images.unsplash.com/photo-1508685096489-7aac29a7dff7?q=80&w=500&auto=format&fit=crop' },
    { name: 'Sport', slug: 'sport', image_url: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=500&auto=format&fit=crop' },
];
</script>
