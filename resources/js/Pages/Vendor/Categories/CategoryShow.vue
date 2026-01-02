<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-start rounded-lg shadow-xl border-t-8 border-purple-600">

            <div class="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $t('messages.category_details') }}
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
                <div class="relative w-24 h-24 mb-4">
                    <img :src="category?.icon_url"
                        @error="(e) => e.target.src = `https://ui-avatars.com/api/?name=${category?.name?.en || 'Cat'}&background=7e3af2&color=fff`"
                        class="w-24 h-24 rounded-2xl object-cover border-4 border-purple-100 dark:border-purple-900/30 shadow-sm"
                        alt="Category Icon" />
                </div>

                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 text-center">
                    {{ category?.name?.[$page.props.locale] || category?.name?.ar }}
                </h3>

                <div class="mt-3 flex flex-wrap gap-2 justify-center">
                    <span :class="category?.is_active
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                        : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                        class="px-4 py-1 text-xs font-bold rounded-full uppercase">
                        {{ category?.is_active ? $t('messages.active') : $t('messages.inactive') }}
                    </span>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <div
                    class="flex items-center text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg">
                    <span class="w-32 font-bold flex-shrink-0">{{ $t('messages.parent_category') }}:</span>
                    <span class="font-medium text-gray-900 dark:text-gray-200">
                        {{ category?.parent?.name?.[$page.props.locale] || $t('messages.none') }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 px-3 py-2 border-y dark:border-gray-700">
                    <div class="flex flex-col space-y-1">
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.main_menu') }}</span>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <div :class="category?.main_menu ? 'bg-purple-500' : 'bg-gray-300'"
                                class="w-3 h-3 rounded-full shadow-sm"></div>
                            <span class="text-sm font-semibold">{{ category?.main_menu ? $t('messages.yes') :
                                $t('messages.no') }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-1">
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ $t('messages.main_store') }}</span>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <div :class="category?.main_store ? 'bg-blue-500' : 'bg-gray-300'"
                                class="w-3 h-3 rounded-full shadow-sm"></div>
                            <span class="text-sm font-semibold">{{ category?.main_store ? $t('messages.yes') :
                                $t('messages.no') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 px-3">
                    <span class="w-32 font-bold flex-shrink-0">{{ $t('messages.slug') }}:</span>
                    <span class="text-purple-600 dark:text-purple-400 font-mono font-bold">{{ category?.slug }}</span>
                </div>

                <div class="grid grid-cols-1 gap-3 px-3">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <span class="w-32 font-bold flex-shrink-0">{{ $t('messages.name') }} (AR):</span>
                        <span class="dark:text-gray-300">{{ category?.name?.ar }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <span class="w-32 font-bold flex-shrink-0">{{ $t('messages.name') }} (EN):</span>
                        <span class="dark:text-gray-300">{{ category?.name?.en }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="$emit('close')"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 transition font-semibold">
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
    category: Object
});

defineEmits(['close']);
</script>
