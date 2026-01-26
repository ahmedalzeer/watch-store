<script setup>
import { Link, Head, router } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    store: {
        type: Object,
        required: true
    }
});

const page = usePage();
const auth = page.props.auth;

// Navigation items for vendor dashboard
const navigationItems = [
    {
        label: 'Dashboard',
        icon: '📊',
        href: route('vendor.dashboard.analytics', { store: props.store.id }),
        active: false
    },
    {
        label: 'Products',
        icon: '📦',
        href: route('vendor.products.index', { store: props.store.id }),
        active: false
    },
    {
        label: 'Orders',
        icon: '🛒',
        href: route('vendor.orders.index', { store: props.store.id }),
        active: false
    },
    {
        label: 'Categories',
        icon: '📂',
        href: route('vendor.categories.index', { store: props.store.id }),
        active: false
    },
    {
        label: 'Banners',
        icon: '🎨',
        href: route('vendor.banners.index', { store: props.store.id }),
        active: false
    },
    {
        label: 'Settings',
        icon: '⚙️',
        href: route('vendor.settings.edit', { store: props.store.id }),
        active: false
    }
];

const handleStoreSwitch = () => {
    router.get(route('vendor.dashboard'));
};
</script>

<template>
    <Head :title="`${store.name.en} - Vendor Dashboard`" />

    <VendorLayout>
        <!-- Store Header with Switch Option -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ store.name.en }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">{{ store.subdomain }}</p>
                </div>
                <button
                    @click="handleStoreSwitch"
                    class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors"
                >
                    Switch Store
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Quick Navigation -->
                <div class="mb-8">
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                        <Link
                            v-for="item in navigationItems"
                            :key="item.label"
                            :href="item.href"
                            class="p-4 bg-white rounded-lg shadow-sm hover:shadow-md hover:border-indigo-500 border border-gray-200 transition-all duration-200 text-center"
                        >
                            <div class="text-3xl mb-2">{{ item.icon }}</div>
                            <p class="text-sm font-medium text-gray-900">{{ item.label }}</p>
                        </Link>
                    </div>
                </div>

                <!-- Store Info Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-8">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Products</p>
                        <p class="text-3xl font-bold text-gray-900">{{ store.products_count || 0 }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-900">{{ store.orders_count || 0 }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Revenue</p>
                        <p class="text-3xl font-bold text-gray-900">{{ store.currency?.symbol || '$' }}{{ store.revenue || '0' }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-gray-600 text-sm font-medium mb-2">Status</p>
                        <p v-if="store.is_active" class="text-lg font-bold text-green-600">Active</p>
                        <p v-else class="text-lg font-bold text-gray-600">Inactive</p>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Welcome to your store dashboard</h3>
                    <p class="text-gray-600 mb-6">Manage your products, orders, and store settings from here.</p>
                    <Link
                        :href="route('vendor.dashboard.analytics', { store: store.id })"
                        class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors"
                    >
                        View Analytics
                    </Link>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>
