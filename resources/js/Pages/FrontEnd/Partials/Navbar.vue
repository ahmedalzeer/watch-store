<template>
    <header :class="$page.props.locale === 'ar' ? 'font-arabic' : 'font-poppins'">
        <div class="w-100 bg-light py-1 text-center small border-bottom">
            {{ $t('store.header.top_bar_message') }}
        </div>

        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-md-4 col-6">
                    <Link :href="route('welcome')" class="navbar-brand">
                        <img src="https://i.ibb.co/dHx2ZR3/velstore.png" width="80" alt="Logo" />
                    </Link>
                </div>

                <div class="col-md-8 col-6">
                    <form @submit.prevent="handleSearch" class="d-flex justify-content-end">
                        <div class="input-group search-input-width">
                            <input type="text" v-model="searchQuery" class="form-control"
                                :placeholder="$t('store.header.search_placeholder')">
                            <button class="btn btn-outline-secondary"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container border-top pt-3 pb-2">
            <div class="d-flex justify-content-between align-items-center">
                <nav class="d-none d-md-block">
                    <ul class="nav gap-3">
                        <li v-for="item in menuItems" :key="item.id">
                            <Link :href="item.url" class="text-dark text-decoration-none fw-medium">
                                {{ item.title[$page.props.locale] }}
                            </Link>
                        </li>
                    </ul>
                </nav>

                <div class="d-flex align-items-center gap-4">
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            {{ $page.props.locale.toUpperCase() }}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <Link :href="route('language.switch', 'ar')" class="dropdown-item">العربية</Link>
                            </li>
                            <li>
                                <Link :href="route('language.switch', 'en')" class="dropdown-item">English</Link>
                            </li>
                        </ul>
                    </div>

                    <Link :href="route('customer.wishlist.index')" class="text-dark position-relative">
                        <i class="fa-regular fa-heart fs-5"></i>
                        <span v-if="wishlistCount > 0"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{
                                wishlistCount }}</span>
                    </Link>

                    <Link :href="route('cart.view')" class="text-dark position-relative">
                        <i class="fa fa-shopping-bag fs-5"></i>
                        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">{{
                            cartCount }}</span>
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
const searchQuery = ref('');
</script>
