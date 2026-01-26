<template>
    <ShopLayout>

        <Head :title="$t('messages.my_wishlist')" />

        <div class="py-24 bg-white">
            <div class="container mx-auto px-4">
                <!-- Header -->
                <div class="text-center mb-16 space-y-4">
                    <span class="text-blue-600 font-bold uppercase tracking-[0.3em] text-[10px]">{{
                        $t('messages.saved_items') }}</span>
                    <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight">{{
                        $t('messages.my_wishlist') }}</h1>
                </div>

                <!-- Empty State -->
                <div v-if="products.length === 0" class="flex flex-col items-center justify-center py-20 space-y-8">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center">
                        <i class="fa-regular fa-heart text-3xl text-gray-200"></i>
                    </div>
                    <div class="text-center space-y-2">
                        <h2 class="text-xl font-bold text-gray-900">{{ $t('messages.wishlist_empty') }}</h2>
                        <p class="text-gray-500 text-sm">{{ $t('messages.wishlist_empty_desc') }}</p>
                    </div>
                    <Link :href="route('shop.index')"
                        class="bg-black text-white px-10 py-4 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl">
                        {{ $t('messages.explore_collection') }}
                    </Link>
                </div>

                <!-- Products Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <ProductCard v-for="product in products" :key="product.id" :product="product"
                        @toggle-wishlist="handleToggleWishlist" @add-to-cart="handleAddToCart" />
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Pages/FrontEnd/Products/ProductCard.vue';
import Swal from 'sweetalert2';

defineProps({
    products: Array
});

const page = usePage();
const t = (key) => {
    const keys = key.split('.');
    let translation = page.props.translations || {};
    keys.forEach((k) => {
        translation = translation ? translation[k] : null;
    });
    return translation || key;
};

const handleToggleWishlist = (product) => {
    router.post(route('customer.wishlist.toggle'), { product_id: product.id }, {
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: t('messages.wishlist_updated'),
                showConfirmButton: false,
                timer: 1500
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
                title: t('messages.added_to_cart'),
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
};
</script>
