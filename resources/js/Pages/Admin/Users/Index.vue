<template>

    <Head :title="$t('messages.users_management')" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.users_management') }}
                </h2>

                <button @click="openModal()"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none shadow-md active:scale-95 transition-all">
                    + {{ $t('messages.add_user') }}
                </button>
            </div>
        </template>

        <div class="w-full overflow-hidden rounded-lg shadow-xs border dark:border-gray-700">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3 text-start">{{ $t('messages.user_name') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.email') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.role') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="user in users.data" :key="user.id"
                            class="text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center font-semibold">
                                    <div class="relative w-8 h-8 rounded-full ltr:mr-3 rtl:ml-3">
                                        <img class="object-cover w-full h-full rounded-full border dark:border-gray-600"
                                            :src="user.profile_photo_url" loading="lazy" />
                                    </div>
                                    <span>{{ user.name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ user.email }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="px-2 py-1 font-semibold leading-tight text-purple-700 bg-purple-100 rounded-full dark:bg-purple-700 dark:text-purple-100 uppercase text-xs">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <button @click="showDetails(user)" class="text-blue-600 hover:scale-110 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="openModal(user)" class="text-purple-600 hover:scale-110 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button @click="handleDelete(user.id)"
                                        class="text-red-600 hover:scale-110 transition">
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
                    <template v-for="(link, key) in users.links" :key="key">
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

        <UserModal :show="showingUserModal" :user="selectedUser" :edit-mode="editMode" @close="showingUserModal = false"
            @refresh="onModalSuccess" />
        <UserShow :show="showingDetails" :user="selectedUser" @close="showingDetails = false" />
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import UserModal from './UserModal.vue';
import UserShow from './UserShow.vue';
import { useAlert } from '@/useAlert';

const props = defineProps({
    users: Object,
    filters: Object
});

const { success, confirmDelete } = useAlert();

const showingUserModal = ref(false);
const showingDetails = ref(false);
const editMode = ref(false);
const selectedUser = ref(null);

const openModal = (user = null) => {
    selectedUser.value = user;
    editMode.value = !!user;
    showingUserModal.value = true;
};

const showDetails = (user) => {
    selectedUser.value = user;
    showingDetails.value = true;
};

const onModalSuccess = (type) => {
    showingUserModal.value = false;
    success(type === 'updated' ? 'messages.user_updated' : 'messages.user_added');
};

const handleDelete = (id) => {
    confirmDelete(() => {
        router.delete(route('users.destroy', id), {
            onSuccess: () => success('messages.user_deleted')
        });
    });
};
</script>
