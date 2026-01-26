<template>
    <Head :title="$t('messages.manage_permissions')" />

    <AdminLayout>
        <template #header>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">🔑 {{ $t('messages.permissions_management') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $t('messages.manage_roles_and_permissions') }}</p>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Roles Section -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">👥 {{ $t('messages.roles') }}</h2>
                        <button @click="showNewRoleForm = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            + {{ $t('messages.new_role') }}
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div v-for="role in roles" :key="role.id" class="border dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ role.name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ role.permissions.length }} {{ $t('messages.permissions_count') }}
                                    </p>
                                </div>
                                <button v-if="!isSystemRole(role.name)" @click="deleteRole(role)"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                    {{ $t('messages.delete') }}
                                </button>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ $t('messages.permissions') }}</p>
                                <form @submit.prevent="updateRolePermissions(role)">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        <label v-for="permission in allPermissions" :key="permission.id" class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox"
                                                :value="permission.name"
                                                v-model="roleForms[role.id].permissions"
                                                class="w-4 h-4 rounded">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ permission.name }}</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                        {{ $t('messages.save') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Permissions List -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900 dark:to-emerald-900">
                        <h3 class="font-bold text-gray-900 dark:text-white">📋 {{ $t('messages.all_permissions') }}</h3>
                    </div>
                    <div class="p-6 space-y-2 max-h-96 overflow-y-auto">
                        <div v-for="permission in allPermissions" :key="permission.id" class="p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ permission.name }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900 dark:to-pink-900 rounded-lg shadow p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3">📊 {{ $t('messages.stats') }}</h3>
                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><strong>{{ $t('messages.total_roles') }}:</strong> {{ roles.length }}</p>
                        <p><strong>{{ $t('messages.total_permissions') }}:</strong> {{ allPermissions.length }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Role Form Modal -->
        <div v-if="showNewRoleForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ $t('messages.create_new_role') }}</h2>
                <form @submit.prevent="createNewRole">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('messages.role_name') }}</label>
                        <input v-model="newRoleForm.name" type="text" class="form-input w-full" required>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ $t('messages.create') }}
                        </button>
                        <button type="button" @click="showNewRoleForm = false" class="flex-1 px-4 py-2 bg-gray-300 text-gray-900 rounded hover:bg-gray-400">
                            {{ $t('messages.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, reactive } from 'vue';
import { useAlert } from '@/useAlert';

const { success, error } = useAlert();

const props = defineProps({
    roles: Array,
    permissions: Object,
});

const allPermissions = ref([]);
const showNewRoleForm = ref(false);
const newRoleForm = reactive({ name: '' });
const roleForms = reactive({});

// Initialize role forms
props.roles.forEach(role => {
    roleForms[role.id] = {
        permissions: role.permissions.map(p => p.name)
    };
});

// Flatten permissions
Object.values(props.permissions).forEach(group => {
    if (Array.isArray(group)) {
        allPermissions.value.push(...group);
    }
});

const isSystemRole = (roleName) => {
    return ['super_admin', 'admin', 'moderator', 'vendor', 'customer'].includes(roleName);
};

const updateRolePermissions = async (role) => {
    try {
        // Send request to update
        // Will be implemented with Laravel fetch
        success(`تم تحديث صلاحيات الدور "${role.name}"`);
    } catch (e) {
        error('خطأ في تحديث الصلاحيات');
    }
};

const deleteRole = async (role) => {
    if (confirm(`هل أنت متأكد من حذف الدور "${role.name}"؟`)) {
        try {
            // Send request to delete
            success(`تم حذف الدور "${role.name}"`);
        } catch (e) {
            error('خطأ في حذف الدور');
        }
    }
};

const createNewRole = async () => {
    if (!newRoleForm.name) {
        error('يجب إدخال اسم الدور');
        return;
    }

    try {
        // Send request to create
        success(`تم إنشاء الدور "${newRoleForm.name}"`);
        showNewRoleForm.value = false;
        newRoleForm.name = '';
    } catch (e) {
        error('خطأ في إنشاء الدور');
    }
};
</script>

<style scoped>
.form-input {
    @apply px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white;
}
</style>
