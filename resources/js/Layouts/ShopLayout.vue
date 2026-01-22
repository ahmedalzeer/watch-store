<template>
    <div :dir="$page.props.locale === 'ar' ? 'rtl' : 'ltr'"
        class="bg-white min-h-screen font-sans text-gray-800 flex flex-col">

        <!-- Row 1: Top Bar -->
        <div class="bg-gray-50 border-b border-gray-100 py-2">
            <div
                class="container mx-auto px-4 text-center text-[10px] md:text-xs text-gray-500 uppercase tracking-widest">
                {{ $t('app.header.free_shipping') }}
            </div>
        </div>

        <!-- Row 2: Logo & Search -->
        <div class="border-b border-gray-50 py-8">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between gap-12">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <Link :href="route('welcome')" class="block w-24 md:w-32">
                            <img src="https://i.ibb.co/dHx2ZR3/velstore.png" alt="Velstore" class="w-full h-auto">
                        </Link>
                    </div>

                    <!-- Search Bar (Large & Middle) -->
                    <div class="flex-grow hidden lg:block">
                        <form @submit.prevent="handleSearch" class="relative w-full max-w-3xl mx-auto">
                            <input type="text" v-model="searchQuery"
                                class="w-full border-b border-gray-200 py-3 px-4 text-sm focus:border-black outline-none transition-colors ltr:pr-10 rtl:pl-10"
                                :placeholder="$t('app.header.search_placeholder')" />
                            <button type="submit"
                                class="absolute ltr:right-0 rtl:left-0 top-1/2 -translate-y-1/2 h-full px-4 text-gray-400 hover:text-black transition-colors">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Actions Icons (Right) -->
                    <div class="flex items-center gap-6">
                        <Link :href="route('cart.view')"
                            class="relative text-gray-700 hover:text-black transition-colors group">
                            <span class="sr-only">Cart</span>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span
                                    class="absolute -top-1 -right-1 h-4 w-4 bg-black text-white text-[10px] flex items-center justify-center rounded-full group-hover:scale-110 transition-transform">0</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Navigation (Links Right, Extras Left) -->
        <div class="border-b border-gray-50 py-4 bg-white sticky top-0 z-40">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between">
                    <!-- Extras Left (Lang, Currency, Small Icons) -->
                    <div class="flex items-center gap-6">
                        <!-- Selectors -->
                        <div
                            class="hidden md:flex items-center gap-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                            <div class="relative group">
                                <button class="hover:text-black transition-colors flex items-center gap-1 uppercase">
                                    {{ $page.props.currentCurrency || 'USD' }}
                                    <i class="fas fa-chevron-down text-[8px]"></i>
                                </button>
                                <div
                                    class="absolute left-0 top-full mt-2 w-24 bg-white shadow-xl border border-gray-50 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                                    <button @click="changeCurrency('USD')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-50 hover:text-black">USD</button>
                                    <button @click="changeCurrency('EUR')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-50 hover:text-black">EUR</button>
                                    <button @click="changeCurrency('SAR')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-50 hover:text-black">SAR</button>
                                </div>
                            </div>
                            <span class="h-3 w-[1px] bg-gray-200"></span>
                            <div class="relative group">
                                <button class="hover:text-black transition-colors flex items-center gap-1 uppercase">
                                    {{ $page.props.locale }}
                                    <i class="fas fa-chevron-down text-[8px]"></i>
                                </button>
                                <div
                                    class="absolute left-0 top-full mt-2 w-24 bg-white shadow-xl border border-gray-50 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                                    <button @click="changeLanguage('en')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-50 hover:text-black">EN</button>
                                    <button @click="changeLanguage('ar')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-50 hover:text-black">AR</button>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Icons -->
                        <div class="flex items-center gap-4 text-gray-400">
                            <Link :href="route('customer.wishlist.index')" class="hover:text-black transition-colors">
                                <i class="fa-regular fa-heart"></i>
                            </Link>
                            <div class="relative group">
                                <button class="hover:text-black transition-colors">
                                    <i class="fa-regular fa-user"></i>
                                </button>
                                <div
                                    class="absolute ltr:left-0 rtl:right-0 top-full mt-4 w-48 bg-white shadow-2xl rounded-sm py-4 border border-gray-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                    <template v-if="!$page.props.auth.user">
                                        <Link :href="route('login')"
                                            class="block px-6 py-2 text-xs font-bold uppercase tracking-widest text-gray-800 hover:text-blue-600">
                                            Login</Link>
                                        <Link :href="route('register')"
                                            class="block px-6 py-2 text-xs font-bold uppercase tracking-widest text-gray-800 hover:text-blue-600">
                                            Register</Link>
                                    </template>
                                    <template v-else>
                                        <Link :href="route('dashboard')"
                                            class="block px-6 py-2 text-xs font-bold uppercase tracking-widest text-gray-800 hover:text-blue-600">
                                            Dashboard</Link>
                                        <Link :href="route('logout')" method="post" as="button"
                                            class="block w-full text-left px-6 py-2 text-xs font-bold uppercase tracking-widest text-gray-800 hover:text-blue-600">
                                            Logout</Link>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Links Right -->
                    <nav class="hidden md:block">
                        <ul
                            class="flex items-center gap-10 text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">
                            <li>
                                <Link :href="route('welcome')" class="hover:text-black transition-colors"
                                    :class="{ 'text-black': $page.component === 'FrontEnd/Welcome' }">{{
                                    $t('app.nav.home') }}</Link>
                            </li>
                            <li>
                                <Link :href="route('shop.index')" class="hover:text-black transition-colors">{{
                                    $t('app.nav.shop') }}</Link>
                            </li>
                            <li>
                                <Link href="#" class="hover:text-black transition-colors">{{ $t('app.nav.about') }}
                                </Link>
                            </li>
                            <li>
                                <Link href="#" class="hover:text-black transition-colors">{{ $t('app.nav.services') }}
                                </Link>
                            </li>
                            <li>
                                <Link href="#" class="hover:text-black transition-colors text-purple-600">{{
                                    $t('app.nav.vendors') }}</Link>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Footer from '@/Pages/FrontEnd/Partials/Footer.vue';

const searchQuery = ref('');

const changeLanguage = (lang) => {
    router.get(route('language.switch', { locale: lang }));
};

const changeCurrency = (code) => {
    router.post(route('change.currency'), { currency_code: code });
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('shop.index'), { search: searchQuery.value });
    }
};
</script>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');

body {
    font-family: 'Outfit', sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* RTL Helpers */
[dir="rtl"] .fa-chevron-right {
    transform: rotate(180deg);
}

[dir="rtl"] .fa-chevron-left {
    transform: rotate(180deg);
}
</style>
