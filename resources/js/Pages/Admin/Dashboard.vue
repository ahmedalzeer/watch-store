<template>
    <Head :title="$t('messages.admin_dashboard')" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        🔐 {{ $t('messages.super_admin_dashboard') }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        {{ $t('messages.manage_all_system') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('messages.logged_as') }}: <strong>{{ auth.user.name }}</strong></p>
                </div>
            </div>
        </template>

        <!-- Impersonation Status Banner -->
        <div v-if="isImpersonating" class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 rounded">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⚠️</span>
                    <div>
                        <p class="font-bold text-yellow-800 dark:text-yellow-100">
                            {{ $t('messages.impersonating_user') }}: {{ impersonatingUser?.name }}
                        </p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-200">
                            {{ $t('messages.impersonation_active_since') }}: {{ formatTime(impersonationTime) }}
                        </p>
                    </div>
                </div>
                <button @click="stopImpersonating"
                    class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                    {{ $t('messages.exit_impersonation') }}
                </button>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $t('messages.total_users') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statistics.total_users }}</p>
                    </div>
                    <div class="text-4xl">👥</div>
                </div>
            </div>

            <!-- Active Stores -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $t('messages.active_stores') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statistics.total_stores }}</p>
                    </div>
                    <div class="text-4xl">🏪</div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $t('messages.total_products') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statistics.total_products }}</p>
                    </div>
                    <div class="text-4xl">📦</div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $t('messages.pending_orders') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statistics.pending_orders }}</p>
                    </div>
                    <div class="text-4xl">📋</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Users by Role -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900 dark:to-purple-900">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">📊 {{ $t('messages.users_by_role') }}</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-for="(count, role) in statistics.roles_breakdown" :key="role" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white capitalize">{{ role }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ getRoleDescription(role) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ count }}</p>
                                <p class="text-xs text-gray-500">{{ getPercentage(count) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900 dark:to-emerald-900">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">⚙️ {{ $t('messages.system_actions') }}</h2>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <Link :href="route('admin.permissions.index')"
                            class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition">
                            <p class="font-bold text-blue-900 dark:text-blue-100">🔑 {{ $t('messages.manage_permissions') }}</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">{{ $t('messages.roles_and_permissions') }}</p>
                        </Link>
                        <Link :href="route('admin.users.index')"
                            class="p-4 bg-purple-50 dark:bg-purple-900 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition">
                            <p class="font-bold text-purple-900 dark:text-purple-100">👥 {{ $t('messages.manage_users') }}</p>
                            <p class="text-sm text-purple-700 dark:text-purple-300">{{ $t('messages.create_edit_delete') }}</p>
                        </Link>
                        <Link :href="route('admin.stores.index')"
                            class="p-4 bg-green-50 dark:bg-green-900 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition">
                            <p class="font-bold text-green-900 dark:text-green-100">🏪 {{ $t('messages.manage_stores') }}</p>
                            <p class="text-sm text-green-700 dark:text-green-300">{{ $t('messages.stores_management') }}</p>
                        </Link>
                        <Link :href="route('admin.impersonate.list')"
                            class="p-4 bg-orange-50 dark:bg-orange-900 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-800 transition">
                            <p class="font-bold text-orange-900 dark:text-orange-100">🔄 {{ $t('messages.impersonate_user') }}</p>
                            <p class="text-sm text-orange-700 dark:text-orange-300">{{ $t('messages.test_as_user') }}</p>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">📈 {{ $t('messages.quick_stats') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ $t('messages.total_roles') }}</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ statistics.total_roles }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ $t('messages.total_permissions') }}</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ statistics.total_permissions }}</span>
                        </div>
                    </div>
                </div>

                <!-- Admin Info -->
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900 dark:to-blue-900 rounded-lg shadow p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">ℹ️ {{ $t('messages.admin_info') }}</h3>
                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><strong>{{ $t('messages.your_role') }}:</strong> Super Admin</p>
                        <p><strong>{{ $t('messages.permissions_count') }}:</strong> {{ statistics.total_permissions }}</p>
                        <p><strong>{{ $t('messages.last_activity') }}:</strong> {{ formatTime(now) }}</p>
                    </div>
                </div>

                <!-- Helpful Links -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">🔗 {{ $t('messages.helpful_links') }}</h3>
                    <div class="space-y-2">
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">{{ $t('messages.system_logs') }}</a>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline text-sm block">{{ $t('messages.api_documentation') }}</a>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline text-sm block">{{ $t('messages.security_settings') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';
import { useAlert } from '@/useAlert';

const { success, error } = useAlert();

const props = defineProps({
    auth: Object,
    statistics: Object,
});

const isImpersonating = ref(false);
const impersonatingUser = ref(null);
const impersonationTime = ref(null);
const now = ref(new Date());

onMounted(() => {
    checkImpersonationStatus();
    // Update time every second
    setInterval(() => {
        now.value = new Date();
    }, 1000);
});

const checkImpersonationStatus = async () => {
    try {
        const response = await fetch(route('admin.impersonate.status'));
        const data = await response.json();

        if (data.impersonating) {
            isImpersonating.value = true;
            impersonatingUser.value = data.impersonating_by;
            impersonationTime.value = data.start_time;
        }
    } catch (e) {
        console.error('Error checking impersonation status:', e);
    }
};

const stopImpersonating = async () => {
    try {
        const response = await fetch(route('admin.impersonate.stop'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        if (response.ok) {
            window.location.reload();
        }
    } catch (e) {
        error('خطأ في إيقاف الاستعراض');
    }
};

const getRoleDescription = (role) => {
    const descriptions = {
        'super_admin': 'صلاحيات كاملة للنظام',
        'admin': 'إدارة شاملة',
        'moderator': 'إشراف على المحتوى',
        'vendor': 'بائعو منتجات',
        'customer': 'عملاء المتجر'
    };
    return descriptions[role] || role;
};

const getPercentage = (count) => {
    const total = props.statistics.total_users;
    return total > 0 ? Math.round((count / total) * 100) : 0;
};

const formatTime = (date) => {
    if (!date) return 'غير معروف';
    return new Date(date).toLocaleString('ar-SA');
};
</script>
