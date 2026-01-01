<template>
    <div class="relative">
        <button class="flex items-center gap-1 relative align-middle rounded-md focus:outline-none"
            @click="isOpen = !isOpen">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                </path>
            </svg>
            <span class="text-xs font-bold uppercase">{{ $locale() }}</span>
        </button>
        <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <ul v-if="isOpen"
                class="absolute right-0 w-32 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700">
                <li class="flex"><a
                        class="inline-flex items-center w-full px-2 py-1 text-sm transition-colors duration-150 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                        :href="route('language.switch', 'ar')">🇪🇬 العربية</a></li>
                <li class="flex"><a
                        class="inline-flex items-center w-full px-2 py-1 text-sm transition-colors duration-150 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                        :href="route('language.switch', 'en')">🇺🇸 English</a></li>
            </ul>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue';
const isOpen = ref(false);
</script>
