<template>

    <Head :title="$t('messages.brands_management')" />

    <VendorLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.brands_management') }}
                </h2>

                <button @click="openModal()"
                    class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 shadow-md transition-all">
                    + {{ $t('messages.create_brand') }}
                </button>
            </div>
        </template>

        <div class="w-full overflow-hidden rounded-lg shadow-xs border dark:border-gray-700">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3 text-start">{{ $t('messages.brand') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.website') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.featured') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="brand in brands.data" :key="brand.id"
                            class="text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center font-semibold">
                                    <div
                                        class="relative w-10 h-10 rounded-lg ltr:mr-3 rtl:ml-3 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border">
                                        <img v-if="brand.logo_url" :src="brand.logo_url"
                                            class="w-full h-full object-contain p-1" />
                                        <span v-else class="text-purple-600 font-bold uppercase">{{
                                            brand.name?.[$page.props.locale]?.charAt(0) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span>{{ brand.name?.[$page.props.locale] || brand.name?.ar }}</span>
                                        <span class="text-xs text-gray-400">{{ brand.slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm italic">
                                <a v-if="brand.website" :href="brand.website" target="_blank"
                                    class="text-blue-500 hover:underline">{{ brand.website }}</a>
                                <span v-else>---</span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span
                                    :class="brand.is_featured ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-700'"
                                    class="px-3 py-1 font-bold rounded-full">
                                    {{ brand.is_featured ? $t('messages.featured') : $t('messages.normal') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <button @click="showBrand(brand)"
                                        class="text-blue-500 hover:scale-110 transition"><svg class="w-5 h-5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button @click="editBrand(brand)"
                                        class="text-purple-600 hover:scale-110 transition"><svg class="w-5 h-5"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                            </path>
                                        </svg></button>
                                    <button @click="deleteBrand(brand.id)"
                                        class="text-red-500 hover:scale-110 transition"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex flex-wrap justify-center space-x-1 rtl:space-x-reverse">
                    <template v-for="(link, key) in brands.links" :key="key">
                        <Link v-if="link.url" :href="link.url"
                            class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded transition-colors"
                            :class="link.active ? 'bg-purple-600 text-white' : 'bg-white dark:bg-gray-700 dark:text-gray-300 hover:bg-purple-50'"
                            v-html="link.label" preserve-scroll preserve-state /> <span v-else
                            class="px-3 py-1 text-sm text-gray-400 border border-gray-300 dark:border-gray-600 rounded cursor-not-allowed"
                            v-html="link.label">
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <BrandModal :show="showingModal" :edit-brand="selectedBrand" :store-id="storeId" @close="showingModal = false"
            @refresh="onRefresh" />
        <BrandShow :show="showingShowModal" :brand="selectedBrandToShow" @close="showingShowModal = false" />
    </VendorLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import BrandModal from './BrandModal.vue';
import BrandShow from './BrandShow.vue';
import { useAlert } from '@/useAlert';

const props = defineProps({ brands: Object, storeId: [Number, String] });
const { success, confirmDelete } = useAlert();
const showingModal = ref(false);
const selectedBrand = ref(null);
const showingShowModal = ref(false);
const selectedBrandToShow = ref(null);

const showBrand = (brand) => { selectedBrandToShow.value = brand; showingShowModal.value = true; };
const openModal = () => { selectedBrand.value = null; showingModal.value = true; };
const editBrand = (brand) => { selectedBrand.value = brand; showingModal.value = true; };
const onRefresh = (type) => { showingModal.value = false; success(type === 'updated' ? 'messages.brand_updated' : 'messages.brand_created'); };

const deleteBrand = (id) => {
    confirmDelete(() => {
        router.delete(route('vendor.brands.destroy', id), { preserveScroll: true });
    });
};
</script>
