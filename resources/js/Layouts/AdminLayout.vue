<template>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
        <aside
            class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0 border-r dark:border-gray-700">
            <SidebarContent />
        </aside>

        <transition enter-active-class="transition ease-in-out duration-150" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition ease-in-out duration-150"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="isSideMenuOpen" @click="isSideMenuOpen = false"
                class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center">
            </div>
        </transition>

        <transition enter-active-class="transition ease-in-out duration-150"
            :enter-from-class="$locale() === 'ar' ? 'transform translate-x-full' : 'transform -translate-x-full'"
            enter-to-class="transform translate-x-0" leave-active-class="transition ease-in-out duration-150"
            leave-from-class="transform translate-x-0"
            :leave-to-class="$locale() === 'ar' ? 'transform translate-x-full' : 'transform -translate-x-full'">
            <aside v-if="isSideMenuOpen"
                class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden"
                :class="$locale() === 'ar' ? 'right-0' : 'left-0'">
                <SidebarContent />
            </aside>
        </transition>

        <div class="flex flex-col flex-1 w-full overflow-hidden">
            <header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
                <div
                    class="container flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300">
                    <button class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none"
                        @click="isSideMenuOpen = !isSideMenuOpen">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z">
                            </path>
                        </svg>
                    </button>

                    <SearchInput />

                    <ul class="flex items-center flex-shrink-0 space-x-6">
                        <ThemeToggle />
                        <LanguageSwitcher />
                        <NotificationsMenu />
                        <ProfileMenu />
                    </ul>
                </div>
            </header>

            <main class="h-full overflow-y-auto p-6">
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import SidebarContent from '@/Components/Admin/SidebarContent.vue';
import SearchInput from '@/Components/Header/SearchInput.vue';
import ThemeToggle from '@/Components/Header/ThemeToggle.vue';
import LanguageSwitcher from '@/Components/Header/LanguageSwitcher.vue';
import NotificationsMenu from '@/Components/Header/NotificationsMenu.vue';
import ProfileMenu from '@/Components/Header/ProfileMenu.vue';

const isSideMenuOpen = ref(false);
</script>
