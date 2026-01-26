<script setup>
import { Link, Head, router } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { computed } from 'vue';

defineProps({
    stores: {
        type: Array,
        required: true
    }
});

const handleStoreSelect = (storeId) => {
    router.get(route('vendor.store.select', { store: storeId }));
};
</script>

<template>
    <Head title="Select Store" />

    <VendorLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Stores
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- No Stores State -->
                <div v-if="!stores || stores.length === 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-900">
                        <p class="mb-4 text-lg font-medium">No stores found</p>
                        <p class="text-gray-600 mb-4">You don't have any stores yet.</p>
                        <Link href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">
                            Create a store
                        </Link>
                    </div>
                </div>

                <!-- Stores Grid -->
                <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="store in stores"
                        :key="store.id"
                        @click="handleStoreSelect(store.id)"
                        class="cursor-pointer overflow-hidden bg-white shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-200"
                    >
                        <!-- Store Header -->
                        <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                        <!-- Store Logo/Icon -->
                        <div class="relative px-6 pb-6">
                            <div class="flex items-center justify-between -mt-12 mb-4">
                                <div class="w-16 h-16 rounded-lg bg-white border-4 border-gray-100 shadow-md flex items-center justify-center">
                                    <div v-if="store.logo" class="w-full h-full overflow-hidden rounded">
                                        <img :src="store.logo" :alt="store.name.en" class="w-full h-full object-cover">
                                    </div>
                                    <div v-else class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                        <span class="text-lg font-bold text-white">
                                            {{ store.name.en.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span v-if="store.is_active" class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Active</span>
                                    <span v-else class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded">Inactive</span>
                                </div>
                            </div>

                            <!-- Store Info -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ store.name.en }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ store.subdomain }}</p>

                            <!-- Store Stats -->
                            <div class="grid grid-cols-3 gap-2 mb-4 pt-4 border-t border-gray-100">
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-gray-900">{{ store.products_count || 0 }}</p>
                                    <p class="text-xs text-gray-600">Products</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-gray-900">{{ store.orders_count || 0 }}</p>
                                    <p class="text-xs text-gray-600">Orders</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-gray-900">{{ store.revenue || '$0' }}</p>
                                    <p class="text-xs text-gray-600">Revenue</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button
                                    @click.prevent.stop="handleStoreSelect(store.id)"
                                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded transition-colors"
                                >
                                    Enter Store
                                </button>
                                <Link
                                    :href="route('vendor.settings.edit', { store: store.id })"
                                    class="px-4 py-2 text-indigo-600 hover:text-indigo-700 border border-indigo-600 rounded hover:border-indigo-700 transition-colors font-medium"
                                >
                                    Settings
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Store Button -->
                <div v-if="stores && stores.length > 0" class="mt-6 text-center">
                    <Link href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                        <span class="mr-2">+</span>
                        Create New Store
                    </Link>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>
