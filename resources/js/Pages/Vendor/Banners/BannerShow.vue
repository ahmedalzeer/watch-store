<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-start rounded-lg shadow-xl border-t-8 border-purple-600">
            <div class="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $t('messages.banner_details') }}</h2>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="mt-6 flex flex-col items-center">
                <div
                    class="relative w-full h-40 mb-4 bg-gray-50 dark:bg-gray-700 rounded-2xl flex items-center justify-center border-2 border-purple-100 dark:border-purple-900/30 overflow-hidden">
                    <img :src="banner?.image_url" class="w-full h-full object-cover" />
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-center">
                    {{ banner?.title?.[$page.props.locale] || banner?.title?.ar }}
                </h3>

                <div class="mt-3 flex flex-wrap gap-2 justify-center">
                    <span class="bg-purple-100 text-purple-700 px-3 py-1 text-xs font-bold rounded-full uppercase">
                        {{ banner?.type }}
                    </span>
                    <span :class="banner?.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                        class="px-3 py-1 text-xs font-bold rounded-full uppercase">
                        {{ banner?.active ? $t('messages.active') : $t('messages.inactive') }}
                    </span>
                </div>
            </div>

            <div class="mt-8 space-y-4 text-sm">
                <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <p class="font-bold text-gray-600 dark:text-gray-400 mb-1">{{ $t('messages.description') }}:</p>
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ banner?.description?.[$page.props.locale] || banner?.description?.ar || '---' }}
                    </p>
                </div>

                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <span class="w-32 font-bold text-gray-600 dark:text-gray-400">{{ $t('messages.link') }}:</span>
                    <a v-if="banner?.link" :href="banner.link" target="_blank"
                        class="text-purple-600 hover:underline break-all font-medium">{{ banner.link }}</a>
                    <span v-else class="text-gray-400">---</span>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="$emit('close')"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold">
                    {{ $t('messages.close') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
defineProps({ show: Boolean, banner: Object });
defineEmits(['close']);
</script>