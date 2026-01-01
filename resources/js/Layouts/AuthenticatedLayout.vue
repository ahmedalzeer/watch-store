<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div :class="{ 'dark': isDarkMode }" :dir="$locale() === 'ar' ? 'rtl' : 'ltr'">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            </nav>

            <div class="flex">
                <aside class="w-64 bg-white dark:bg-gray-800 h-screen">
                    <Link :href="route('admin.users')">{{ $t('messages.users_management') }}</Link>
                </aside>

                <main class="p-6 w-full">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
