<template>
    <div class="py-4 text-gray-500 dark:text-gray-400">
        <Link class="px-6 text-lg font-bold text-gray-800 dark:text-gray-200" :href="route('vendor.dashboard')">
            {{ $t('messages.vendor_panel') }}
        </Link>
        <ul class="mt-6">
            <li class="relative px-6 py-3">
                <span v-if="route().current('admin.dashboard')"
                    class="absolute inset-y-0 w-1 bg-purple-600 rounded-tl-lg rounded-bl-lg"
                    :class="$locale() === 'ar' ? 'right-0' : 'left-0'" aria-hidden="true"></span>
                <Link
                    class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                    :class="route().current('admin.dashboard') ? 'text-gray-800 dark:text-gray-100' : ''"
                    :href="route('vendor.dashboard')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.dashboard') }}</span>
                </Link>
            </li>

            <li class="relative px-6 py-3">
                <Link
                    class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                    href="/admin/users">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span :class="$locale() === 'ar' ? 'mr-4' : 'ml-4'">{{ $t('messages.categories') }}</span>
                </Link>
            </li>

            <li class="relative px-6 py-3">
                <Link
                    class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                    href="/admin/stores">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
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
                    enter-from-class="opacity-25 max-h-0" enter-to-class="opacity-100 max-h-xl"
                    leave-active-class="transition-all ease-in-out duration-300" leave-from-class="opacity-100 max-h-xl"
                    leave-to-class="opacity-0 max-h-0">
                    <ul v-if="isOrdersMenuOpen"
                        class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900">
                        <li
                            class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                            <Link class="w-full" href="#">{{ $t('messages.new_orders') }}</Link>
                        </li>
                        <li
                            class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                            <Link class="w-full" href="#">{{ $t('messages.orders_history') }}</Link>
                        </li>
                    </ul>
                </transition>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const isOrdersMenuOpen = ref(false);
</script>
