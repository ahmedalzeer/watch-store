<template>
    <Modal :show="show" @close="$emit('close')" max-width="md">
        <div class="p-6 dark:bg-gray-800 text-start">
            <div class="flex items-center justify-between border-b pb-3 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $t('messages.user_details') }}
                </h2>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="mt-6 flex flex-col items-center">
                <div class="relative w-24 h-24 mb-4">
                    <img :src="user?.profile_photo_url"
                        class="w-24 h-24 rounded-full object-cover border-4 border-purple-100 dark:border-purple-900"
                        alt="Avatar" />
                </div>

                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ user?.name }}</h3>
                <span
                    class="px-3 py-1 mt-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full dark:bg-purple-700 dark:text-purple-100">
                    {{ user?.role }}
                </span>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <span class="w-24 font-bold">{{ $t('messages.email') }}:</span>
                    <span>{{ user?.email }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <span class="w-24 font-bold">{{ $t('messages.phone') }}:</span>
                    <span>{{ user?.phone || '---' }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <span class="w-24 font-bold">{{ $t('messages.created_at') }}:</span>
                    <span>{{ user?.created_at_human }}</span>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="$emit('close')"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition">
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
    user: Object
});

defineEmits(['close']);
</script>
