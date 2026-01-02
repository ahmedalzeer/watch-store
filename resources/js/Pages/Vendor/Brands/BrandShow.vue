<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-start rounded-lg shadow-xl border-t-8 border-purple-600">
            <div class="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $t('messages.brand_details') }}</h2>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="mt-6 flex flex-col items-center">
                <div
                    class="relative w-24 h-24 mb-4 bg-gray-50 dark:bg-gray-700 rounded-2xl flex items-center justify-center border-2 border-purple-100 dark:border-purple-900/30">
                    <img :src="brand?.logo_url"
                        @error="(e) => e.target.src = `https://ui-avatars.com/api/?name=${brand?.name?.en}&background=7e3af2&color=fff`"
                        class="max-w-full max-h-full object-contain p-2" />
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                    {{ brand?.name?.[$page.props.locale] || brand?.name?.ar }}
                </h3>

                <div class="mt-3 flex flex-wrap gap-2 justify-center">
                    <span v-if="brand?.is_featured"
                        class="bg-yellow-100 text-yellow-700 px-3 py-1 text-xs font-bold rounded-full uppercase">
                        ⭐ {{ $t('messages.featured') }}
                    </span>
                    <span :class="brand?.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                        class="px-3 py-1 text-xs font-bold rounded-full uppercase">
                        {{ brand?.is_active ? $t('messages.active') : $t('messages.inactive') }}
                    </span>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex items-center text-sm p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <span class="w-32 font-bold text-gray-600 dark:text-gray-400">{{ $t('messages.website') }}:</span>
                    <a v-if="brand?.website" :href="brand.website" target="_blank"
                        class="text-purple-600 hover:underline break-all font-medium">{{ brand.website }}</a>
                    <span v-else class="text-gray-400">---</span>
                </div>

                <div class="grid grid-cols-2 gap-4 px-3">
                    <div class="flex flex-col space-y-1">
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.main_menu') }}</span>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <div :class="brand?.main_menu ? 'bg-purple-500' : 'bg-gray-300'"
                                class="w-3 h-3 rounded-full"></div>
                            <span class="text-sm font-semibold">{{ brand?.main_menu ? $t('messages.yes') :
                                $t('messages.no') }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-1">
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.main_store') }}</span>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <div :class="brand?.main_store ? 'bg-blue-500' : 'bg-gray-300'"
                                class="w-3 h-3 rounded-full"></div>
                            <span class="text-sm font-semibold">{{ brand?.main_store ? $t('messages.yes') :
                                $t('messages.no') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center text-sm px-3 pt-2 border-t dark:border-gray-700">
                    <span class="w-32 font-bold text-gray-600 dark:text-gray-400">{{ $t('messages.slug') }}:</span>
                    <span class="font-mono text-purple-500 font-bold">{{ brand?.slug }}</span>
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
defineProps({ show: Boolean, brand: Object });
defineEmits(['close']);
</script>
