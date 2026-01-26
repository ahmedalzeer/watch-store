<template>
    <ShopLayout>
        <div class="bg-gray-50 min-h-screen py-12">
            <div class="container mx-auto px-4 max-w-7xl">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-8 uppercase tracking-widest">{{
                    $t('messages.checkout') }}</h1>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Left: Checkout Form -->
                    <div class="lg:col-span-8 space-y-8">
                        <!-- Step 1: Billing/Shipping Details -->
                        <div class="bg-white p-8 rounded-sm shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <span
                                    class="h-8 w-8 bg-black text-white rounded-full flex items-center justify-center text-sm">1</span>
                                {{ $t('messages.billing_details') }}
                            </h2>
                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{
                                            $t('messages.first_name') }}</label>
                                        <input v-model="form.first_name" type="text"
                                            class="w-full border-gray-200 rounded-sm focus:ring-black focus:border-black py-3 px-4 text-sm"
                                            required>
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{
                                            $t('messages.last_name') }}</label>
                                        <input v-model="form.last_name" type="text"
                                            class="w-full border-gray-200 rounded-sm focus:ring-black focus:border-black py-3 px-4 text-sm"
                                            required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{
                                            $t('messages.email') }}</label>
                                        <input v-model="form.email" type="email"
                                            class="w-full border-gray-200 rounded-sm focus:ring-black focus:border-black py-3 px-4 text-sm"
                                            required>
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{
                                            $t('messages.phone') }}</label>
                                        <input v-model="form.phone" type="tel"
                                            class="w-full border-gray-200 rounded-sm focus:ring-black focus:border-black py-3 px-4 text-sm"
                                            required>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{
                                        $t('messages.address') }}</label>
                                    <input v-model="form.address" type="text"
                                        class="w-full border-gray-200 rounded-sm focus:ring-black focus:border-black py-3 px-4 text-sm"
                                        required>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{
                                            $t('messages.city') }}</label>
                                        <input v-model="form.city" type="text"
                                            class="w-full border-gray-200 rounded-sm focus:ring-black focus:border-black py-3 px-4 text-sm"
                                            required>
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{
                                            $t('messages.country') }}</label>
                                        <input v-model="form.country" type="text"
                                            class="w-full border-gray-200 rounded-sm focus:ring-black focus:border-black py-3 px-4 text-sm"
                                            required>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Step 2: Payment Method -->
                        <div class="bg-white p-8 rounded-sm shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <span
                                    class="h-8 w-8 bg-black text-white rounded-full flex items-center justify-center text-sm">2</span>
                                {{ $t('messages.payment_method') }}
                            </h2>
                            <div class="space-y-4">
                                <div v-for="gateway in paymentGateways" :key="gateway.id"
                                    @click="form.payment_method = gateway.code"
                                    class="border p-4 rounded-sm cursor-pointer transition-all flex items-center justify-between"
                                    :class="form.payment_method === gateway.code ? 'border-black bg-gray-50' : 'border-gray-100 hover:border-gray-300'">
                                    <div class="flex items-center gap-4">
                                        <div class="h-5 w-5 border-2 rounded-full flex items-center justify-center"
                                            :class="form.payment_method === gateway.code ? 'border-black' : 'border-gray-300'">
                                            <div v-if="form.payment_method === gateway.code"
                                                class="h-2.5 w-2.5 bg-black rounded-full"></div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ gateway.code === 'cod' ?
                                                $t('messages.cash_on_delivery') : $t('messages.credit_card') }}</p>
                                            <p class="text-xs text-gray-500">{{ gateway.code === 'cod' ?
                                                $t('messages.pay_when_received') : $t('messages.secure_payment') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 grayscale opacity-70">
                                        <i v-if="gateway.code === 'stripe'" class="fab fa-cc-visa text-2xl"></i>
                                        <i v-if="gateway.code === 'stripe'" class="fab fa-cc-mastercard text-2xl"></i>
                                        <i v-if="gateway.code === 'cod'" class="fas fa-money-bill-wave text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Order Summary -->
                    <div class="lg:col-span-4">
                        <div class="bg-white p-8 rounded-sm shadow-sm border border-gray-100 sticky top-24">
                            <h2 class="text-xl font-bold text-gray-800 mb-6 uppercase tracking-widest border-b pb-4">{{
                                $t('messages.order_summary') }}</h2>

                            <div class="space-y-4 mb-8 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                                <div v-for="item in cart" :key="item.id" class="flex gap-4">
                                    <img :src="item.image || '/placeholder.png'" :alt="item.name"
                                        class="w-16 h-16 object-cover rounded-sm border">
                                    <div class="flex-grow">
                                        <p class="text-xs font-bold text-gray-800 line-clamp-1 uppercase">{{ item.name
                                            }}</p>
                                        <p class="text-[10px] text-gray-500">{{ item.quantity }} x {{ item.price }}</p>
                                    </div>
                                    <p class="text-xs font-bold text-gray-800">{{ (item.price *
                                        item.quantity).toFixed(2) }}</p>
                                </div>
                            </div>

                            <div class="space-y-3 border-t pt-6 text-sm">
                                <div class="flex justify-between text-gray-500">
                                    <span>{{ $t('messages.subtotal') }}</span>
                                    <span>{{ subtotal.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>{{ $t('messages.shipping') }}</span>
                                    <span class="text-green-600 font-bold uppercase text-[10px]">{{ $t('messages.free')
                                        || 'FREE' }}</span>
                                </div>
                                <div
                                    class="flex justify-between text-lg font-black text-gray-900 border-t pt-4 mt-2 uppercase tracking-widest">
                                    <span>{{ $t('messages.total') }}</span>
                                    <span>{{ total.toFixed(2) }}</span>
                                </div>
                            </div>

                            <button @click="handleSubmit" :disabled="form.processing"
                                class="w-full bg-black text-white font-bold py-4 px-6 rounded-sm mt-8 uppercase tracking-[0.2em] text-xs hover:bg-gray-900 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                                <span v-if="form.processing"
                                    class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full font-sans"></span>
                                {{ $t('messages.place_order') }}
                            </button>

                            <p class="text-[10px] text-gray-400 mt-4 text-center italic">
                                <i class="fas fa-shield-alt mr-1"></i> {{ $t('messages.secured_payment_guarantee') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    cart: Object,
    subtotal: Number,
    shipping: Number,
    total: Number,
    paymentGateways: Array
});

const form = useForm({
    first_name: '',
    last_name: '',
    email: usePage().props.auth.user?.email || '',
    phone: '',
    address: '',
    city: '',
    country: '',
    payment_method: 'cod', // Default to COD
});

const handleSubmit = () => {
    form.post(route('checkout.store'), {
        onSuccess: () => {
            Swal.fire({
                title: usePage().props.locale === 'ar' ? 'تم بنجاح!' : 'Success!',
                text: usePage().props.locale === 'ar' ? 'تم تقديم طلبك بنجاح.' : 'Your order has been placed successfully.',
                icon: 'success',
                confirmButtonColor: '#000',
                confirmButtonText: usePage().props.locale === 'ar' ? 'حسناً' : 'OK'
            });
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire({
                title: usePage().props.locale === 'ar' ? 'خطأ!' : 'Error!',
                text: usePage().props.locale === 'ar' ? 'يرجى مراجعة البيانات المدخلة.' : 'Please check the provided input.',
                icon: 'error',
                confirmButtonColor: '#000',
            });
        }
    });
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #999;
}
</style>
