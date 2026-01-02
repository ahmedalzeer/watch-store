<template>
    <div class="flex justify-center flex-1">
        <div class="relative w-full max-w-xl focus-within:text-purple-500 text-gray-500">
            <input v-model="search"
                class="w-full text-sm text-gray-700 placeholder-gray-600 bg-gray-100 border-0 rounded-md dark:placeholder-gray-500 dark:bg-gray-700 dark:text-gray-200 focus:outline-none form-input focus:ring-1 focus:ring-purple-500"
                :class="$locale() === 'ar' ? 'pr-10 pl-2 text-right' : 'pl-10 pr-2 text-left'" type="text"
                :placeholder="$t('messages.search_placeholder')" />
            <div class="absolute inset-y-0 flex items-center pointer-events-none"
                :class="$locale() === 'ar' ? 'right-0 pr-3' : 'left-0 pl-3'">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z">
                    </path>
                </svg>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const search = ref(new URLSearchParams(window.location.search).get('search') || '');

watch(search, debounce((newValue) => {
    const urlParams = new URLSearchParams(window.location.search);
    const currentSearch = urlParams.get('search') || '';
    const storeId = urlParams.get('store_id');

    if (newValue === currentSearch) {
        return;
    }

    const queryParams = {
        search: newValue
    };

    if (storeId) {
        queryParams.store_id = storeId;
    }

    router.get(window.location.pathname, queryParams, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 500));
</script>
