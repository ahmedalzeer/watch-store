<template>

    <Head :title="$t('messages.orders_management')" />

    <VendorLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.orders_management') }}
                </h2>
            </div>
        </template>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.total }}</p>
                        <p class="text-xs text-gray-500 uppercase font-bold">{{ $t('messages.total_orders') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.pending }}</p>
                        <p class="text-xs text-gray-500 uppercase font-bold">{{ $t('messages.pending') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <i class="fas fa-spinner text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.processing }}</p>
                        <p class="text-xs text-gray-500 uppercase font-bold">{{ $t('messages.processing') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.completed }}</p>
                        <p class="text-xs text-gray-500 uppercase font-bold">{{ $t('messages.completed') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.cancelled }}</p>
                        <p class="text-xs text-gray-500 uppercase font-bold">{{ $t('messages.cancelled') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.search') }}
                    </label>
                    <input v-model="localFilters.search" type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        :placeholder="$t('messages.search_orders')">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.filter_by_status') }}
                    </label>
                    <select v-model="localFilters.status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">{{ $t('messages.all_statuses') }}</option>
                        <option value="pending">{{ $t('messages.pending') }}</option>
                        <option value="processing">{{ $t('messages.processing') }}</option>
                        <option value="completed">{{ $t('messages.completed') }}</option>
                        <option value="cancelled">{{ $t('messages.cancelled') }}</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="applyFilters"
                        class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition active:scale-95">
                        {{ $t('messages.apply_filters') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-xs border dark:border-gray-700">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3 text-start">{{ $t('messages.order_id') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.customer_name') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.items') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.total') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.payment_method') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.status') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.date') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="order in orders.data" :key="order.id"
                            class="text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <span class="font-mono font-bold text-purple-600">#{{ order.id }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ order.user?.name || 'Guest' }}</span>
                                    <span class="text-xs text-gray-400">{{ order.user?.email }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs font-bold">
                                    {{ order.items?.length || 0 }} {{ $t('messages.items') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-bold">
                                {{ formatCurrency(order.total) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="capitalize">{{ order.payment_method }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span :class="getStatusClass(order.status)"
                                    class="px-3 py-1 font-bold rounded-full uppercase">
                                    {{ $t('messages.' + order.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <Link :href="route('vendor.orders.show', { order: order.id, store_id: storeId })"
                                        class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg transition"
                                        :title="$t('messages.view')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                    <p>{{ $t('messages.no_orders') }}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <Pagination :links="orders.links" />
            </div>
        </div>

    </VendorLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    orders: Object,
    stats: Object,
    storeId: [Number, String],
    filters: Object
});

const localFilters = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || ''
});

const applyFilters = () => {
    router.get(route('vendor.orders.index', {
        store_id: props.storeId,
        ...localFilters.value
    }), {}, {
        preserveState: true,
        preserveScroll: true
    });
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
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
