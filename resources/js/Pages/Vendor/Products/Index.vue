<template>

    <Head :title="$t('messages.products_management')" />

    <VendorLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.products_management') }}
                </h2>

                <button @click="openModal()"
                    class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 shadow-md transition-all active:scale-95">
                    + {{ $t('messages.create_product') }}
                </button>
            </div>
        </template>

        <div class="w-full overflow-hidden rounded-lg shadow-xs border dark:border-gray-700">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3 text-start">{{ $t('messages.product') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.price') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.stock') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.status') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="product in products.data" :key="product.id"
                            class="text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center font-semibold">
                                    <div
                                        class="relative w-12 h-12 rounded-lg ltr:mr-3 rtl:ml-3 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border dark:border-gray-600">
                                        <img v-if="product.image_url" :src="product.image_url"
                                            class="w-full h-full object-cover" />
                                        <span v-else class="text-purple-600 font-bold uppercase">
                                            {{ product.name?.[$page.props.locale]?.charAt(0) }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold">
                                            {{ product.name?.[$page.props.locale] || product.name?.ar }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ product.sku || 'No SKU' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex flex-col">
                                    <span
                                        :class="{ 'text-red-500 line-through text-xs': product.discount_price !== null }">
                                        {{ product.price }}
                                    </span>
                                    <span v-if="product.discount_price !== null" class="text-green-600 font-bold">
                                        {{ product.discount_price }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span
                                    :class="product.stock > 5 ? 'text-gray-600 dark:text-gray-400' : 'text-red-500 font-bold'">
                                    {{ product.stock }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span
                                    :class="product.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700'"
                                    class="px-3 py-1 font-bold rounded-full uppercase">
                                    {{ product.is_active ? $t('messages.active') : $t('messages.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <button @click="showProduct(product)"
                                        class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg transition"
                                        :title="$t('messages.view')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button @click="openSpecsModal(product)"
                                        class="p-2 text-amber-500 hover:bg-amber-50 dark:hover:bg-gray-700 rounded-lg transition"
                                        :title="$t('messages.specifications')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>

                                    <button @click="editProduct(product)"
                                        class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-gray-700 rounded-lg transition"
                                        :title="$t('messages.edit')">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>

                                    <button @click="deleteProduct(product.id)"
                                        class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-gray-700 rounded-lg transition"
                                        :title="$t('messages.delete')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.data.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                {{ $t('messages.no_data_found') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <Pagination :links="products.links" />
            </div>
        </div>

        <ProductModal :show="showingModal" :edit-product="selectedProduct" :categories="categories" :brands="brands"
            :store-id="storeId" @close="showingModal = false" @refresh="onRefresh" />

        <ProductShow :show="showingShowModal" :product="selectedProductToShow" @close="showingShowModal = false" />

        <ProductSpecsModal :show="showingSpecsModal" :product="selectedProductForSpecs" :store-id="storeId"
            @close="showingSpecsModal = false" @refresh="onRefresh('updated')" />

    </VendorLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import ProductModal from './ProductModal.vue';
import ProductShow from './ProductShow.vue';
import ProductSpecsModal from './ProductSpecsModal.vue';
import Pagination from '@/Components/Pagination.vue';
import { useAlert } from '@/useAlert';

const props = defineProps({
    products: Object,
    categories: Array,
    brands: Array,
    storeId: [Number, String]
});

const { success, confirmDelete } = useAlert();

const showingModal = ref(false);
const selectedProduct = ref(null);

const openModal = () => {
    selectedProduct.value = null;
    showingModal.value = true;
};

const editProduct = (product) => {
    selectedProduct.value = product;
    showingModal.value = true;
};

const showingShowModal = ref(false);
const selectedProductToShow = ref(null);

const showProduct = (p) => {
    selectedProductToShow.value = p;
    showingShowModal.value = true;
};

const showingSpecsModal = ref(false);
const selectedProductForSpecs = ref(null);

const openSpecsModal = (product) => {
    selectedProductForSpecs.value = product;
    showingSpecsModal.value = true;
};

const onRefresh = (type) => {
    showingModal.value = false;
    showingSpecsModal.value = false;
    success(type === 'updated' ? 'messages.product_updated' : 'messages.product_created');
};

const deleteProduct = (id) => {
    confirmDelete(() => {
        router.delete(route('vendor.products.destroy', id), {
            preserveScroll: true,
            onSuccess: () => success('messages.product_deleted')
        });
    });
};
</script>
