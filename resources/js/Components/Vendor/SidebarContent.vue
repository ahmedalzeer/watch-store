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
                    <span v-if="route().current('vendor.variants.*')"
                        class="absolute inset-y-0 w-1 bg-purple-600 rounded-tl-lg rounded-bl-lg"
                        :class="$locale() === 'ar' ? 'right-0' : 'left-0'" aria-hidden="true"></span>

                    <Link
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        :class="route().current('vendor.variants.*') ? 'text-gray-800 dark:text-gray-100' : ''"
                        :href="route('vendor.variants.index', { store_id: selectedStoreId })">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>

                        <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.variants') }}</span>
                    </Link>
                </li>

                <li class="relative px-6 py-3">
                    <span v-if="route().current('vendor.banners.*')"
                        class="absolute inset-y-0 w-1 bg-purple-600 rounded-tl-lg rounded-bl-lg"
                        :class="$locale() === 'ar' ? 'right-0' : 'left-0'" aria-hidden="true"></span>

                    <Link
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        :class="route().current('vendor.banners.*') ? 'text-gray-800 dark:text-gray-100' : ''"
                        :href="route('vendor.banners.index', { store_id: selectedStoreId })">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>

                        <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.banners') }}</span>
                    </Link>
                </li>

                <li class="relative px-6 py-3">
                    <span v-if="route().current('vendor.orders.*')"
                        class="absolute inset-y-0 w-1 bg-purple-600 rounded-tl-lg rounded-bl-lg"
                        :class="$locale() === 'ar' ? 'right-0' : 'left-0'" aria-hidden="true"></span>
                    <Link
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        :class="route().current('vendor.orders.*') ? 'text-gray-800 dark:text-gray-100' : ''"
                        :href="route('vendor.orders.index', { store_id: selectedStoreId })">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.orders') }}</span>
                    </Link>
                </li>

                <li class="relative px-6 py-3">
                    <span v-if="route().current('vendor.settings.*')"
                        class="absolute inset-y-0 w-1 bg-purple-600 rounded-tl-lg rounded-bl-lg"
                        :class="$locale() === 'ar' ? 'right-0' : 'left-0'" aria-hidden="true"></span>
                    <Link
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        :class="route().current('vendor.settings.*') ? 'text-gray-800 dark:text-gray-100' : ''"
                        :href="route('vendor.settings.edit', { store_id: selectedStoreId })">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.settings') }}</span>
                    </Link>
                </li>
            </template>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';

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
