<template>

    <Head :title="$t('messages.categories_management')" />

    <VendorLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.categories_management') }}
                </h2>

                <button @click="openModal()"
                    class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 shadow-md active:scale-95 transition-all">
                    + {{ $t('messages.create_category') }}
                </button>
            </div>
        </template>

        <div class="w-full overflow-hidden rounded-lg shadow-xs border dark:border-gray-700">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3 text-start">{{ $t('messages.name') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.parent_category') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.slug') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.status') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="category in categories.data" :key="category.id"
                            class="text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center font-semibold">
                                    <div
                                        class="relative w-8 h-8 rounded-lg ltr:mr-3 rtl:ml-3 bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                        <span class="text-purple-600 font-bold uppercase">{{
                                            category.name?.[$page.props.locale]?.charAt(0) }}</span>
                                    </div>
                                    <span>{{ category.name?.[$page.props.locale] || category.name?.ar }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm italic text-gray-500">
                                {{ category.parent?.name?.[$page.props.locale] || '---' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-purple-400">{{ category.slug }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span
                                    :class="category.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                                    class="px-3 py-1 font-bold rounded-full">
                                    {{ category.is_active ? $t('messages.active') : $t('messages.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <button @click="showCategory(category)"
                                        class="text-blue-500 hover:text-blue-400 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="editCategory(category)"
                                        class="text-purple-600 hover:scale-110 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button @click="deleteCategory(category.id)"
                                        class="text-red-500 hover:text-red-400 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <CategoryModal :show="showingModal" :parent-categories="parentCategories" :edit-category="selectedCategory"
            :store-id="storeId" @close="showingModal = false" @refresh="onRefresh" />
        <CategoryShow :show="showingShowModal" :category="selectedCategoryToShow" @close="showingShowModal = false" />
    </VendorLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue'; // تأكد من استخدام Layout التاجر
import CategoryModal from './CategoryModal.vue';
import { useAlert } from '@/useAlert';

const props = defineProps({
    categories: Object,
    parentCategories: Array,
    storeId: [Number, String]
});

const { success, confirmDelete } = useAlert();
const showingModal = ref(false);
const selectedCategory = ref(null);
const showingShowModal = ref(false);
const selectedCategoryToShow = ref(null);

const showCategory = (category) => {
    selectedCategoryToShow.value = category;
    showingShowModal.value = true;
};

const openModal = () => {
    selectedCategory.value = null;
    showingModal.value = true;
};

const editCategory = (category) => {
    selectedCategory.value = category;
    showingModal.value = true;
};

const onRefresh = (type) => {
    showingModal.value = false;
    success(type === 'updated' ? 'messages.category_updated' : 'messages.category_created');
};

const deleteCategory = (id) => {
    confirmDelete(() => {
        router.delete(route('vendor.categories.destroy', id), {
            preserveScroll: true
        });
    });
};
</script>
