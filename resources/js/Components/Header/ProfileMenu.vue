<template>
    <div class="relative">
        <button class="align-middle rounded-full focus:outline-none" @click="isOpen = !isOpen">
            <img class="object-cover w-8 h-8 rounded-full border dark:border-gray-600"
                :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
        </button>

        <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <ul v-if="isOpen"
                class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700">

                <li class="px-2 py-2 text-xs font-bold text-purple-600 border-b dark:border-gray-600">
                    {{ $page.props.auth.user.name }}
                </li>

                <li class="flex">
                    <Link
                        class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                        :href="route('logout')" method="post" as="button">
                        <svg class="w-4 h-4 mr-3 rtl:ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>{{ $t('messages.logout') }}</span>
                    </Link>
                </li>
            </ul>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const isOpen = ref(false);
</script>
