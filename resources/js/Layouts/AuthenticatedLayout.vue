<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ProfileMenu from '@/Components/Header/ProfileMenu.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const isDarkMode = ref(false);
</script>

<template>
    <div :class="{ 'dark': isDarkMode }" :dir="$locale() === 'ar' ? 'rtl' : 'ltr'">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <ApplicationLogo />
                        <div class="hidden md:flex space-x-4">
                            <NavLink :href="route('dashboard')">{{ $t('messages.dashboard') }}</NavLink>
                            <NavLink :href="route('profile.edit')">{{ $t('messages.profile') }}</NavLink>
                        </div>
                        <ProfileMenu />
                    </div>
                </div>
            </nav>

            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <slot />
            </div>
        </div>
    </div>
</template>
