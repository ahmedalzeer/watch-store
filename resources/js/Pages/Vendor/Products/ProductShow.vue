<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-start rounded-lg shadow-xl border-t-8 border-purple-600">
            <div class="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $t('messages.product_details') }}</h2>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="mt-6 flex flex-col items-center">
                <div
                    class="relative w-32 h-32 mb-4 bg-gray-50 dark:bg-gray-700 rounded-2xl flex items-center justify-center border-2 border-purple-100 overflow-hidden shadow-inner">
                    <img :src="product?.image_url" class="w-full h-full object-cover" />
                </div>
                <h3 class="text-xl font-bold text-center">{{ product?.name?.[$page.props.locale] || product?.name?.ar }}
                </h3>
                <p class="text-sm text-gray-400 font-mono">{{ product?.sku }}</p>
            </div>

            <div class="mt-8 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <span class="block text-xs text-gray-400 uppercase font-bold">{{ $t('messages.price') }}</span>
                        <span class="text-lg font-bold text-purple-600">{{ product?.price }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <span class="block text-xs text-gray-400 uppercase font-bold">{{ $t('messages.stock') }}</span>
                        <span class="text-lg font-bold">{{ product?.stock }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center p-3 border-b dark:border-gray-700">
                    <span class="text-sm font-bold">{{ $t('messages.condition') }}</span>
                    <span class="text-sm uppercase font-mono bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{
                        product?.condition }}</span>
                </div>

                <div class="grid grid-cols-3 gap-2 py-4">
                    <div class="text-center">
                        <div :class="product?.is_active ? 'bg-green-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full mx-auto mb-1"></div>
                        <span class="text-[10px] uppercase font-bold text-gray-400">{{ $t('messages.active') }}</span>
                    </div>
                    <div class="text-center">
                        <div :class="product?.main_menu ? 'bg-purple-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full mx-auto mb-1"></div>
                        <span class="text-[10px] uppercase font-bold text-gray-400">{{ $t('messages.menu') }}</span>
                    </div>
                    <div class="text-center">
                        <div :class="product?.main_store ? 'bg-blue-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full mx-auto mb-1"></div>
                        <span class="text-[10px] uppercase font-bold text-gray-400">{{ $t('messages.store') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="$emit('close')"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg dark:bg-gray-700 dark:text-gray-300 font-semibold">{{
                        $t('messages.close') }}</button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
defineProps({ show: Boolean, product: Object });
defineEmits(['close']);
</script>
