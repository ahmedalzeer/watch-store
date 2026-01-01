<template>

    <Head :title="$t('messages.stores_management')" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.stores_management') }}
                </h2>

                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                    <button @click="openModal()"
                        class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none shadow-md active:scale-95 transition-all">
                        + {{ $t('messages.create_store') }}
                    </button>
                </div>
            </div>
        </template>

        <div class="w-full overflow-hidden rounded-lg shadow-xs border dark:border-gray-700">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3 text-start">{{ $t('messages.name') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.owner') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.subdomain') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.status') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="store in stores.data" :key="store.id"
                            class="text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center font-semibold">
                                    <div class="relative w-8 h-8 rounded-full ltr:mr-3 rtl:ml-3">
                                        <img class="object-cover w-full h-full rounded-full border dark:border-gray-600"
                                            :src="store.logo_url"
                                            @error="(e) => e.target.src = 'https://ui-avatars.com/api/?name=Store'" />
                                    </div>
                                    <span class="font-medium">{{ store.name?.[$page.props.locale] || store.name?.ar
                                        }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-semibold">{{ store.user?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ store.user?.email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-purple-400 font-mono">{{ store.subdomain }}</td>
                            <td class="px-6 py-4">
                                <span
                                    :class="store.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                                    class="px-3 py-1 text-xs font-bold rounded-full">
                                    {{ store.is_active ? $t('messages.active') : $t('messages.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <button @click="showStore(store)"
                                        class="text-blue-500 hover:text-blue-400 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="editStore(store)"
                                        class="text-purple-600 hover:scale-110 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button @click="deleteStore(store.id)"
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

            <div class="px-4 py-3 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex flex-wrap justify-center space-x-1 rtl:space-x-reverse">
                    <template v-for="(link, key) in stores.links" :key="key">
                        <Link v-if="link.url" :href="link.url"
                            class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded transition-colors"
                            :class="link.active ? 'bg-purple-600 text-white' : 'bg-white dark:bg-gray-700 dark:text-gray-300 hover:bg-purple-50'"
                            v-html="link.label" preserve-scroll />
                        <span v-else
                            class="px-3 py-1 text-sm text-gray-400 border border-gray-300 dark:border-gray-600 rounded cursor-not-allowed"
                            v-html="link.label"></span>
                    </template>
                </div>
            </div>
        </div>

        <StoreModal :show="showingModal" :users="users" :edit-store="selectedStore" @close="showingModal = false"
            @refresh="onRefresh" />
        <StoreShow :show="showingShowModal" :store="selectedStoreToShow" @close="showingShowModal = false" />
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StoreModal from './StoreModal.vue';
import { debounce } from 'lodash';
import StoreShow from './StoreShow.vue';
import { useAlert } from '@/useAlert';

const props = defineProps({ stores: Object, users: Array });
const { success, confirmDelete } = useAlert();

const showingShowModal = ref(false);
const selectedStoreToShow = ref(null);
const showingModal = ref(false);
const selectedStore = ref(null);
const search = ref('');

watch(search, debounce((q) => {
    router.get(route('admin.stores.index'), { search: q }, { preserveState: true, replace: true });
}, 300));

const openModal = () => {
    selectedStore.value = null;
    showingModal.value = true;
};

const editStore = (store) => {
    selectedStore.value = store;
    showingModal.value = true;
};

const showStore = (store) => {
    selectedStoreToShow.value = store;
    showingShowModal.value = true;
};

const onRefresh = (type) => {
    showingModal.value = false;
    success(type === 'updated' ? 'messages.store_updated' : 'messages.store_created');
};

const deleteStore = (id) => {
    confirmDelete(() => {
        router.delete(route('admin.stores.destroy', id), {
            onSuccess: () => success('messages.store_deleted'),
            preserveScroll: true
        });
    });
};
</script>
