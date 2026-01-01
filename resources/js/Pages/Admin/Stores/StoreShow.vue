<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-start rounded-lg shadow-xl border-t-8"
            :style="{ borderTopColor: store?.theme_color || '#7e3af2' }">

            <div class="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $t('messages.store_details') }}
                </h2>
                <button @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="mt-6 flex flex-col items-center">
                <div class="relative w-28 h-28 mb-4">
                    <img :src="store?.logo_url"
                        @error="(e) => e.target.src = `https://ui-avatars.com/api/?name=${store?.name?.en || 'Store'}&background=7e3af2&color=fff`"
                        class="w-28 h-28 rounded-2xl object-cover border-4 shadow-sm"
                        :style="{ borderColor: (store?.theme_color || '#7e3af2') + '20' }" alt="Store Logo" />
                </div>

                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 text-center">
                    {{ store?.name?.[$page.props.locale] || store?.name?.ar }}
                </h3>

                <div class="flex space-x-2 rtl:space-x-reverse mt-2">
                    <span :class="store?.is_active
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                        : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                        class="px-3 py-1 text-xs font-bold rounded-full uppercase">
                        {{ store?.is_active ? $t('messages.active') : $t('messages.inactive') }}
                    </span>
                    <span
                        class="px-3 py-1 text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">
                        {{ store?.currency }}
                    </span>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <div
                    class="flex items-start text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg">
                    <span class="w-28 font-bold flex-shrink-0">{{ $t('messages.owner') }}:</span>
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900 dark:text-gray-200">{{ store?.user?.name }}</span>
                        <span class="text-xs">{{ store?.user?.email }}</span>
                    </div>
                </div>

                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 px-3">
                    <span class="w-28 font-bold flex-shrink-0">{{ $t('messages.subdomain') }}:</span>
                    <span class="text-purple-600 dark:text-purple-400 font-mono font-bold">{{ store?.subdomain }}</span>
                </div>

                <div class="grid grid-cols-1 gap-3 px-3">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <span class="w-28 font-bold flex-shrink-0">{{ $t('messages.email') }}:</span>
                        <span>{{ store?.contact_email || '---' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <span class="w-28 font-bold flex-shrink-0">{{ $t('messages.phone') }}:</span>
                        <span dir="ltr">{{ store?.phone || '---' }}</span>
                    </div>
                </div>

                <div class="flex items-start text-sm text-gray-600 dark:text-gray-400 px-3">
                    <span class="w-28 font-bold flex-shrink-0">{{ $t('messages.description') }}:</span>
                    <p class="italic text-gray-500 dark:text-gray-500">{{ store?.description?.[$page.props.locale] ||
                        store?.description?.ar || '---' }}</p>
                </div>

                <div v-if="store?.social_links"
                    class="flex items-center text-sm text-gray-600 dark:text-gray-400 px-3 border-t pt-4 dark:border-gray-700">
                    <span class="w-28 font-bold flex-shrink-0">{{ $t('messages.social_media') }}:</span>
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <a v-if="store.social_links.facebook" :href="store.social_links.facebook" target="_blank"
                            class="text-blue-600 hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a v-if="store.social_links.instagram" :href="store.social_links.instagram" target="_blank"
                            class="text-pink-600 hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a v-if="store.social_links.twitter" :href="store.social_links.twitter" target="_blank"
                            class="text-blue-400 hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="flex items-center text-xs text-gray-400 dark:text-gray-500 px-3 justify-between">
                    <span>{{ $t('messages.created_at') }}: {{ store?.created_at_human }}</span>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="$emit('close')"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition font-semibold">
                    {{ $t('messages.close') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';

defineProps({
    show: Boolean,
    store: Object
});

defineEmits(['close']);
</script>
