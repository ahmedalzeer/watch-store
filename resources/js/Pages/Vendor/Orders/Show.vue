<template>

    <Head :title="$t('messages.order_details') + ' #' + order.id" />

    <VendorLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('vendor.orders.index', { store_id: storeId })"
                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-200">
                            {{ $t('messages.order_details') }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            {{ $t('messages.order_id') }}: <span class="font-mono font-bold text-purple-600">#{{ order.id }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="printOrder" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all active:scale-95">
                        <i class="fas fa-print ltr:mr-2 rtl:ml-2"></i>
                        {{ $t('messages.print_order') }}
                    </button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $t('messages.order_items') }}
                        </h3>
                    </div>
                    <div class="divide-y dark:divide-gray-700">
                        <div v-for="item in order.items" :key="item.id" class="p-4 flex items-center gap-4">
                            <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                <img v-if="item.product?.image_url" :src="item.product.image_url" class="w-full h-full object-cover" />
                                <i v-else class="fas fa-box text-gray-400 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 dark:text-white">
                                    {{ item.product?.name?.[$page.props.locale] || item.product?.name?.en || 'Product' }}
                                </h4>
                                <p v-if="item.variant" class="text-xs text-gray-500">
                                    SKU: {{ item.variant?.sku }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $t('messages.quantity') }}: {{ item.quantity }}
                                </p>
                            </div>
                            <div class="text-end">
                                <p class="font-bold text-gray-800 dark:text-white">{{ formatCurrency(item.price * item.quantity) }}</p>
                                <p class="text-xs text-gray-500">{{ formatCurrency(item.price) }} × {{ item.quantity }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $t('messages.total') }}</span>
                            <span class="text-2xl font-bold text-purple-600">{{ formatCurrency(order.total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $t('messages.shipping_details') }}
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.name') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ order.shipping_details?.name || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.email') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ order.shipping_details?.email || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.phone') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ order.shipping_details?.phone || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.city') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ order.shipping_details?.city || '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.address') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ order.shipping_details?.address || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.country') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ order.shipping_details?.country || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.postal_code') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ order.shipping_details?.postal_code || '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Status -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $t('messages.order_status') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <span :class="getStatusClass(order.status)"
                                class="px-4 py-2 font-bold rounded-full uppercase text-sm">
                                {{ $t('messages.' + order.status) }}
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.update_status') }}</label>
                            <select v-model="newStatus"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="pending">{{ $t('messages.pending') }}</option>
                                <option value="processing">{{ $t('messages.processing') }}</option>
                                <option value="completed">{{ $t('messages.completed') }}</option>
                                <option value="cancelled">{{ $t('messages.cancelled') }}</option>
                            </select>
                            <button @click="updateStatus" :disabled="newStatus === order.status"
                                class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed active:scale-95">
                                {{ $t('messages.update') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $t('messages.payment_details') }}
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.payment_method') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium capitalize">{{ order.payment_method }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.status') }}</label>
                            <span :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase">
                                {{ $t('messages.' + order.payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $t('messages.customer_info') }}
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <span class="text-purple-600 font-bold text-lg">
                                    {{ order.user?.name?.charAt(0)?.toUpperCase() || 'G' }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ order.user?.name || 'Guest' }}</p>
                                <p class="text-sm text-gray-500">{{ order.user?.email || '-' }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.placed_on') }}</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ formatDate(order.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </VendorLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { useAlert } from '@/useAlert';

const props = defineProps({
    order: Object,
    storeId: [Number, String]
});

const { success } = useAlert();
const page = usePage();
const newStatus = ref(props.order.status);

const updateStatus = () => {
    router.put(route('vendor.orders.update', { order: props.order.id, store_id: props.storeId }), {
        status: newStatus.value
    }, {
        onSuccess: () => success('messages.order_status_updated')
    });
};

const printOrder = () => {
    window.print();
};

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        'processing': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'completed': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'cancelled': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<style>
@media print {
    nav, aside, button, .no-print {
        display: none !important;
    }
}
</style>
