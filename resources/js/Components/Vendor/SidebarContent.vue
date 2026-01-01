<template>
    <div class="py-4 text-gray-500 dark:text-gray-400">
        <Link class="px-6 text-lg font-bold text-gray-800 dark:text-gray-200" :href="route('vendor.dashboard')">
            {{ $t('messages.vendor_panel') }}
        </Link>

        <div class="px-6 mt-4">
            <label class="text-xs uppercase font-bold text-gray-400">{{ $t('messages.current_store') }}</label>
            <select v-model="selectedStoreId"
                class="w-full mt-1 text-sm dark:bg-gray-800 dark:text-gray-300 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-purple-500">
                <option v-for="store in $page.props.auth.user.stores" :key="store.id" :value="store.id">
                    {{ store.name?.[$page.props.locale] || store.name?.ar }}
                </option>
            </select>
        </div>

        <ul class="mt-6">
            <li class="relative px-6 py-3">
                <span v-if="route().current('vendor.dashboard')"
                    class="absolute inset-y-0 w-1 bg-purple-600 rounded-tl-lg rounded-bl-lg"
                    :class="$locale() === 'ar' ? 'right-0' : 'left-0'" aria-hidden="true"></span>
                <Link
                    class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                    :class="route().current('vendor.dashboard') ? 'text-gray-800 dark:text-gray-100' : ''"
                    :href="route('vendor.dashboard')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.dashboard') }}</span>
                </Link>
            </li>

            <template v-if="selectedStoreId">

                <li class="relative px-6 py-3">
                    <Link
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        :href="route('vendor.categories.index', { store_id: selectedStoreId })">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.categories') }}</span>
                    </Link>
                </li>

                <li class="relative px-6 py-3">
                    <span v-if="route().current('vendor.brands.*')"
                        class="absolute inset-y-0 w-1 bg-purple-600 rounded-tl-lg rounded-bl-lg"
                        :class="$locale() === 'ar' ? 'right-0' : 'left-0'" aria-hidden="true"></span>

                    <Link
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        :class="route().current('vendor.brands.*') ? 'text-gray-800 dark:text-gray-100' : ''"
                        :href="route('vendor.brands.index', { store_id: selectedStoreId })">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>

                        <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.brands') }}</span>
                    </Link>
                </li>

                <li class="relative px-6 py-3">
                    <Link
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        :href="route('vendor.products.index', { store_id: selectedStoreId })">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.products') }}</span>
                    </Link>
                </li>

                <li class="relative px-6 py-3">
                    <button
                        class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        @click="isOrdersMenuOpen = !isOrdersMenuOpen">
                        <span class="inline-flex items-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.orders_management')
                                }}</span>
                        </span>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <transition enter-active-class="transition-all ease-in-out duration-300"
                        enter-from-class="opacity-25 max-h-0" enter-to-class="opacity-100 max-h-xl">
                        <ul v-if="isOrdersMenuOpen"
                            class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900">
                            <li
                                class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                                <Link
                                    :href="route('vendor.orders.index', { store_id: selectedStoreId, status: 'new' })">
                                    {{ $t('messages.new_orders') }}</Link>
                            </li>
                            <li
                                class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                                <Link :href="route('vendor.orders.index', { store_id: selectedStoreId })">{{
                                    $t('messages.orders_history') }}</Link>
                            </li>
                        </ul>
                    </transition>
                </li>
            </template>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';

const isOrdersMenuOpen = ref(false);
const selectedStoreId = ref(null);

watch(selectedStoreId, (newId) => {
    if (!newId) return;

    const currentRoute = route().current();

    router.get(route(currentRoute), { store_id: newId }, {
        preserveState: true,
        replace: true
    });
});

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const storeIdFromUrl = urlParams.get('store_id');

    const stores = usePage().props.auth.user.stores;

    if (storeIdFromUrl) {
        selectedStoreId.value = parseInt(storeIdFromUrl);
    } else if (stores && stores.length > 0) {
        selectedStoreId.value = stores[0].id;
    }
});
</script>
