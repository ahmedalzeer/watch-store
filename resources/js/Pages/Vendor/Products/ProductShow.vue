<template>
    <Modal :show="show" @close="$emit('close')" max-width="2xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div
                class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="bg-purple-100 text-purple-700 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>
                    {{ $t('messages.product_details') }}
                </h2>
                <button @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
                <!-- Top Section: Image & Key Info -->
                <div class="flex flex-col md:flex-row gap-6 mb-8">
                    <!-- Gallery Section -->
                    <div class="w-full md:w-1/3 flex flex-col gap-3">
                        <div
                            class="aspect-square rounded-xl overflow-hidden border-2 border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 group relative">
                            <img :src="activeImage || product?.image_url"
                                class="w-full h-full object-contain p-2 transition duration-300 group-hover:scale-105" />
                        </div>
                        <!-- Thumbnails -->
                        <div v-if="product?.media?.length > 1" class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar">
                            <div v-for="media in product.media" :key="media.id"
                                @click="activeImage = media.original_url"
                                :class="{ 'ring-2 ring-purple-500': activeImage === media.original_url }"
                                class="w-16 h-16 flex-shrink-0 rounded-lg border dark:border-gray-700 bg-gray-50 dark:bg-gray-800 cursor-pointer overflow-hidden hover:opacity-80 transition">
                                <img :src="media.original_url" class="w-full h-full object-cover" />
                            </div>
                        </div>
                    </div>

                    <!-- Info Section -->
                    <div class="w-full md:w-2/3 space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="px-2 py-0.5 text-xs font-bold rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    {{ product?.brand?.name?.[$page.props.locale] || $t('messages.brand') }}
                                </span>
                                <span v-if="product?.is_active"
                                    class="px-2 py-0.5 text-xs font-bold rounded bg-green-100 text-green-700 uppercase tracking-wider">
                                    {{ $t('messages.active') }}
                                </span>
                                <span v-else
                                    class="px-2 py-0.5 text-xs font-bold rounded bg-red-100 text-red-700 uppercase tracking-wider">
                                    {{ $t('messages.inactive') }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-tight">
                                {{ product?.name?.[$page.props.locale] || product?.name?.ar }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono mt-1 flex items-center gap-2">
                                <span
                                    class="w-4 h-4 inline-flex items-center justify-center bg-gray-100 rounded text-[10px] mobile-hidden">SKU</span>
                                {{ product?.sku }}
                            </p>
                        </div>

                        <!-- Price & Stock Card -->
                        <div
                            class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-700/20 p-4 rounded-xl border dark:border-gray-700/50">
                            <div>
                                <div class="flex flex-col">
                                    <span
                                        :class="{ 'text-red-500 line-through text-sm': product?.discount_price !== null }"
                                        class="text-gray-400 uppercase block mb-1">
                                        {{ $t('messages.price') }}
                                    </span>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{
                                            product?.price }}</span>
                                    </div>
                                </div>
                                <div v-if="product?.discount_price !== null" class="mt-2">
                                    <span class="text-xs font-bold text-green-600 uppercase block mb-1">
                                        {{ $t('messages.discount_price') }}
                                    </span>
                                    <span class="text-2xl font-bold text-green-600">{{ product?.discount_price }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase block mb-1">{{
                                    $t('messages.stock') }}</span>
                                <div class="flex items-center gap-2">
                                    <span
                                        :class="product?.stock > 0 ? 'text-gray-800 dark:text-gray-200' : 'text-red-500'"
                                        class="text-2xl font-bold">
                                        {{ product?.stock }}
                                    </span>
                                    <span v-if="product?.stock <= 5 && product?.stock > 0"
                                        class="text-xs text-orange-500 font-medium">({{ $t('messages.low_stock')
                                        }})</span>
                                </div>
                            </div>
                        </div>

                        <!-- Categories & Attributes -->
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div
                                class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-800 border dark:border-gray-700">
                                <span class="text-gray-400 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg></span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{
                                    product?.category?.name?.[$page.props.locale] || '---' }}</span>
                            </div>

                            <div
                                class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-800 border dark:border-gray-700">
                                <span class="text-gray-400 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg></span>
                                <span class="font-medium text-gray-700 dark:text-gray-300 uppercase">{{
                                    product?.condition || $t('messages.new') }}</span>
                            </div>

                            <div v-if="product?.is_featured"
                                class="flex items-center gap-2 p-2 rounded-lg bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-900/30">
                                <span class="text-yellow-500">⭐</span>
                                <span class="font-medium text-yellow-700 dark:text-yellow-500">{{
                                    $t('messages.featured') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs: Description & Specs -->
                <div class="mt-6">
                    <div class="border-b dark:border-gray-700 mb-4 flex gap-6">
                        <button @click="activeTab = 'desc'"
                            :class="activeTab === 'desc' ? 'border-purple-600 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                            class="pb-2 font-bold text-sm border-b-2 transition">
                            {{ $t('messages.description') }}
                        </button>
                        <button @click="activeTab = 'specs'"
                            :class="activeTab === 'specs' ? 'border-purple-600 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                            class="pb-2 font-bold text-sm border-b-2 transition">
                            {{ $t('messages.specifications') }}
                        </button>
                        <button @click="activeTab = 'meta'"
                            :class="activeTab === 'meta' ? 'border-purple-600 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                            class="pb-2 font-bold text-sm border-b-2 transition">
                            {{ $t('messages.metadata') }}
                        </button>
                    </div>

                    <div v-if="activeTab === 'desc'" class="space-y-4 animate-fadeIn">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/20 border dark:border-gray-700/50">
                                <span class="text-xs font-bold text-gray-400 block mb-2 uppercase">Arabic</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                                    {{ product?.description?.ar || $t('messages.no_description') }}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/20 border dark:border-gray-700/50">
                                <span class="text-xs font-bold text-gray-400 block mb-2 uppercase">English</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                                    {{ product?.description?.en || $t('messages.no_description') }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'specs'" class="animate-fadeIn">
                        <div v-if="product?.specifications && Object.keys(product.specifications).length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div v-for="(value, key) in product.specifications" :key="key"
                                class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/20 rounded-lg border dark:border-gray-700/50">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">{{
                                    key.replace('_', ' ') }}</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ value }}</span>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400">
                            {{ $t('messages.no_specifications') }}
                        </div>
                    </div>

                    <div v-if="activeTab === 'meta'" class="animate-fadeIn">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/20 rounded-lg text-center">
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">{{
                                    $t('messages.main_menu') }}</span>
                                <span :class="product?.main_menu ? 'text-green-600' : 'text-gray-400'"
                                    class="font-bold text-lg">{{ product?.main_menu ? $t('messages.yes') :
                                        $t('messages.no') }}</span>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/20 rounded-lg text-center">
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">{{
                                    $t('messages.main_store') }}</span>
                                <span :class="product?.main_store ? 'text-green-600' : 'text-gray-400'"
                                    class="font-bold text-lg">{{ product?.main_store ? $t('messages.yes') :
                                        $t('messages.no') }}</span>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/20 rounded-lg text-center">
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">Created At</span>
                                <span class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ new
                                    Date(product?.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/20 rounded-lg text-center">
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">Slug</span>
                                <span class="text-xs font-mono text-purple-500 truncate block w-full">{{ product?.slug
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t dark:border-gray-700 flex justify-end bg-gray-50 dark:bg-gray-800">
                <button @click="$emit('close')"
                    class="px-6 py-2 bg-white border border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition font-medium shadow-sm">
                    {{ $t('messages.close') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({ show: Boolean, product: Object });
defineEmits(['close']);

const activeTab = ref('desc');
const activeImage = ref(null);

watch(() => props.show, (val) => {
    if (!val) {
        activeImage.value = null;
        activeTab.value = 'desc';
    }
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-gray-300 dark:bg-gray-600 rounded-full;
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
