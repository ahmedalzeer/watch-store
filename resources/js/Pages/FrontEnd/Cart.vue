<template>
    <ShopLayout>
        <Head title="Shopping Cart" />

        <div class="py-24 bg-white">
            <div class="container mx-auto px-4">
                <!-- Header -->
                <div class="text-center mb-16 space-y-4">
                    <span class="text-blue-600 font-bold uppercase tracking-[0.3em] text-[10px]">Your Selection</span>
                    <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight">Shopping Cart</h1>
                </div>

                <div v-if="Object.keys(cart).length === 0" class="flex flex-col items-center justify-center py-20 space-y-8">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center">
                        <i class="fa fa-shopping-bag text-3xl text-gray-200"></i>
                    </div>
                    <div class="text-center space-y-2">
                        <h2 class="text-xl font-bold text-gray-900">Your cart is empty</h2>
                        <p class="text-gray-500 text-sm">Add items to your cart to see them here.</p>
                    </div>
                    <Link :href="route('shop.index')" 
                        class="bg-black text-white px-10 py-4 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl">
                        Shop Now
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                    <!-- Cart Items -->
                    <div class="lg:col-span-8 space-y-6">
                        <div v-for="(item, key) in cart" :key="key" 
                            class="flex flex-col md:flex-row items-center gap-6 p-6 border border-gray-50 rounded-sm hover:shadow-lg transition-shadow bg-gray-50/50">
                            
                            <!-- Image -->
                            <div class="w-24 h-24 bg-white p-2 rounded-sm border border-gray-50 flex items-center justify-center overflow-hidden">
                                <img :src="item.image" :alt="item.name" class="max-h-full max-w-full object-contain">
                            </div>

                            <!-- Details -->
                            <div class="flex-grow text-center md:text-left">
                                <h3 class="font-bold text-gray-900 text-base mb-1">{{ item.name }}</h3>
                                <p v-if="item.attributes && Object.keys(item.attributes).length > 0" class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">
                                    <span v-for="(val, label) in item.attributes" :key="label">
                                        {{ label }}: <span class="text-gray-600">{{ val }}</span>
                                    </span>
                                </p>
                                <p class="text-blue-600 font-black mt-2">${{ item.price }}</p>
                            </div>

                            <!-- Quantity -->
                            <div class="flex items-center border border-gray-200 bg-white">
                                <button @click="updateQuantity(item, -1)" class="px-3 py-2 hover:bg-gray-50 text-gray-500">-</button>
                                <span class="px-4 py-2 text-xs font-bold w-12 text-center border-x border-gray-200">{{ item.quantity }}</span>
                                <button @click="updateQuantity(item, 1)" class="px-3 py-2 hover:bg-gray-50 text-gray-500">+</button>
                            </div>

                            <!-- Actions -->
                            <button @click="removeItem(item)" class="text-gray-300 hover:text-red-500 transition-colors p-2">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="lg:col-span-4 bg-gray-50 p-8 rounded-sm space-y-6">
                        <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-4">Order Summary</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 uppercase tracking-widest text-[10px] font-bold">Subtotal</span>
                                <span class="font-black text-gray-900">${{ subtotal }}</span>
                            </div>
                            <div class="flex justify-between text-sm border-b border-gray-200 pb-4">
                                <span class="text-gray-500 uppercase tracking-widest text-[10px] font-bold">Shipping</span>
                                <span class="font-black text-gray-900">Calculated at next step</span>
                            </div>
                            <div class="flex justify-between text-lg pt-4">
                                <span class="font-black text-gray-900 uppercase tracking-widest text-xs">Total</span>
                                <span class="font-black text-blue-600">${{ subtotal }}</span>
                            </div>
                        </div>

                        <Link :href="route('checkout.index')" 
                            class="block w-full bg-black text-white text-center py-5 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl transform hover:-translate-y-1">
                            Proceed to Checkout
                        </Link>

                        <p class="text-[10px] text-gray-400 text-center uppercase tracking-widest leading-relaxed">
                            Secured Payment & Free Shipping Guarantee
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    cart: Object
});

const subtotal = computed(() => {
    return Object.values(props.cart).reduce((total, item) => total + (item.price * item.quantity), 0);
});

const updateQuantity = (item, delta) => {
    const newQty = item.quantity + delta;
    if (newQty < 1) return;

    router.post(route('cart.update'), {
        cart: [{
            product_id: item.product_id,
            variant_id: item.variant_id,
            quantity: newQty
        }]
    }, {
        preserveScroll: true
    });
};

const removeItem = (item) => {
    Swal.fire({
        title: 'Remove item?',
        text: "This item will be removed from your cart.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('cart.remove'), {
                product_id: item.product_id,
                variant_id: item.variant_id
            });
        }
    });
};
</script>
