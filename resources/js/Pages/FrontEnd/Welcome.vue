<template>
    <ShopLayout>

        <!-- SEO Head Component -->
        <SeoHead
            :seo-data="seoData"
            :schema-data="schemaData"
            :current-store="currentStore"
            :primary-language="primaryLanguage"
            :theme-color="themeColor"
        />

        <Head :title="currentStore?.name || $t('messages.home')" />

        <!-- Hero Section -->
        <HeroSection :banners="banners" />

        <!-- Categories Slider -->
        <CategorySlider :categories="categories" />

        <!-- Trending Products (Slider) -->
        <section class="py-24 bg-white container mx-auto px-4" v-if="products.data?.length > 0">
            <div class="flex items-center justify-between mb-16">
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">
                    {{ $t('messages.trending_now') }}
                </h2>
                <Link :href="route('shop.index')"
                    class="text-xs font-bold uppercase tracking-widest border-b-2 border-black pb-1 hover:text-blue-600 hover:border-blue-600 transition-all">
                    {{ $t('messages.view_all') }}
                </Link>
            </div>

            <Swiper :modules="[Autoplay]" :slides-per-view="1" :rtl="$page.props.locale === 'ar'" :breakpoints="{
                640: { slidesPerView: 2, spaceBetween: 20 },
                768: { slidesPerView: 3, spaceBetween: 30 },
                1024: { slidesPerView: 4, spaceBetween: 30 }
            }" :autoplay="{ delay: 5000, disableOnInteraction: false }" :loop="true">
                <SwiperSlide v-for="product in products.data?.slice(0, 8)" :key="product.id">
                    <ProductCard :product="product" @toggle-wishlist="handleToggleWishlist"
                        @add-to-cart="handleAddToCart" />
                </SwiperSlide>
            </Swiper>
        </section>

        <!-- Sale Banner -->
        <section class="py-12 container mx-auto px-4">
            <div class="relative h-[400px] rounded-3xl overflow-hidden group">
                <img src="https://images.unsplash.com/photo-1547996160-81dfa63595aa?q=80&w=1500&auto=format&fit=crop"
                    alt="Sale"
                    class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-center p-12">
                    <span class="text-white font-bold uppercase tracking-[0.5em] text-xs mb-4">{{ $t('messages.limited_edition') }}</span>
                    <h2 class="text-5xl md:text-7xl font-black text-white mb-8">{{ $t('messages.summer_sale') }}</h2>
                    <Link :href="route('shop.index')"
                        class="bg-white text-black px-10 py-4 text-xs font-bold uppercase tracking-widest hover:bg-black hover:text-white transition-all transform hover:-translate-y-1">
                        {{ $t('messages.get_it_now') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- Featured Products (Grid) -->
        <section class="py-24 bg-white container mx-auto px-4" v-if="products.data?.length > 0">
            <div class="text-center mb-16 space-y-4">
                <span class="text-blue-600 font-bold uppercase tracking-[0.3em] text-[10px]">{{ $t('messages.curated_collection') }}</span>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight">{{ $t('messages.our_favorites') }}</h1>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <ProductCard v-for="product in products.data" :key="product.id" :product="product"
                    @toggle-wishlist="handleToggleWishlist" @add-to-cart="handleAddToCart" />
            </div>

            <div class="text-center mt-20">
                <Link :href="route('shop.index')"
                    class="inline-block px-12 py-5 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 transform">
                    {{ $t('messages.shop_collection') }}
                </Link>
            </div>
        </section>

        <!-- Features (Why Choose Us) -->
        <FeaturesSection />

    </ShopLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import SeoHead from '@/Components/SEO/SeoHead.vue';
import HeroSection from '@/Pages/FrontEnd/Partials/HeroSection.vue';
import CategorySlider from '@/Pages/FrontEnd/Partials/CategorySlider.vue';
import FeaturesSection from '@/Pages/FrontEnd/Partials/FeaturesSection.vue';
import ProductCard from '@/Pages/FrontEnd/Products/ProductCard.vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';
import Swal from 'sweetalert2';

defineProps({
    products: Object,
    banners: Array,
    categories: Array,
    brands: Array,
    currentStore: Object,
    seoData: Object,
    schemaData: Object,
    primaryLanguage: String,
    themeColor: String
});

const handleToggleWishlist = (product) => {
    router.post(route('customer.wishlist.toggle'), { product_id: product.id }, {
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Wishlist updated',
                showConfirmButton: false,
                timer: 1500
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Login Required',
                text: 'Please login to add items to your wishlist',
                confirmButtonColor: '#000'
            });
        }
    });
};

const handleAddToCart = (product) => {
    router.post(route('cart.add'), {
        product_id: product.id,
        quantity: 1
    }, {
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Added to cart',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
};
</script>
