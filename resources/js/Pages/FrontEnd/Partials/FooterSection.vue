<template>
    <footer class="bg-light pt-5 border-top mt-5">
        <div class="container">
            <div class="row text-center text-md-start">
                <div class="col-md-3 mb-4">
                    <img src="https://i.ibb.co/dHx2ZR3/velstore.png" width="100" alt="Logo">
                    <p class="small text-muted mt-2">{{ $t('messages.footer_description') }}</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">{{ $t('store.footer.account') }}</h6>
                    <ul class="list-unstyled">
                        <li>
                            <Link href="#" class="text-muted small text-decoration-none">{{
                                $t('store.footer.my_account') }}</Link>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">{{ $t('store.footer.pages') }}</h6>
                    <ul class="list-unstyled">
                        <li>
                            <Link href="#" class="text-muted small text-decoration-none">{{
                                $t('store.footer.privacy_policy') }}</Link>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold">{{ $t('store.footer.follow_us') }}</h6>
                    <div class="d-flex gap-3 justify-content-center justify-content-md-start">
                        <a href="#" class="text-dark"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-dark"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-black text-white py-3 mt-4 text-center small">
            {{ $t('store.footer.copyright') }}
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
</script>
