<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 dark:bg-gray-800">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-3 dark:border-gray-700">
                {{ editMode ? $t('messages.edit_user') : $t('messages.add_user') }}
            </h2>

            <form @submit.prevent="submit" class="mt-6 space-y-4 text-start">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-400">{{ $t('messages.user_name')
                            }}</label>
                        <input v-model="form.name" type="text" class="form-input" />
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-400">{{ $t('messages.email') }}</label>
                        <input v-model="form.email" type="email" class="form-input" />
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-400">{{ $t('messages.role') }}</label>
                        <select v-model="form.role" class="form-input">
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-400">{{ $t('messages.phone') }}</label>
                        <input v-model="form.phone" type="tel" class="form-input" />
                    </div>
                </div>

                <div class="mt-4 border-t pt-4 dark:border-gray-700">
                    <label class="block text-sm font-medium dark:text-gray-400 mb-2">{{ $t('messages.profile_picture')
                        }}</label>
                    <Dropzone :url="route('admin.media.upload.temp')" :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }" @success="handleUploadSuccess" />
                    <p v-if="form.avatar_path" class="text-xs text-green-500 mt-2">✓ {{ $t('messages.image_attached') }}
                    </p>
                </div>

                <div v-if="!editMode || (editMode && changePassword)">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium dark:text-gray-400">{{ $t('messages.password')
                            }}</label>
                        <button v-if="editMode" type="button" @click="changePassword = false"
                            class="text-xs text-red-500 underline">Cancel</button>
                    </div>
                    <input v-model="form.password" type="password" class="form-input" />
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                </div>
                <button v-if="editMode && !changePassword" type="button" @click="changePassword = true"
                    class="text-sm text-purple-500 underline">
                    Change Password?
                </button>

                <div class="mt-6 flex justify-end space-x-3 rtl:space-x-reverse">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-gray-500">{{
                        $t('messages.cancel') }}</button>
                    <button type="submit" :disabled="form.processing"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50">
                        {{ editMode ? $t('messages.update') : $t('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import Dropzone from '@/Components/Dropzone.vue';

const props = defineProps({ show: Boolean, user: Object, editMode: Boolean });
const emit = defineEmits(['close', 'refresh']);
const changePassword = ref(false);

const form = useForm({
    name: '', email: '', role: 'customer', phone: '', password: '', avatar_path: '',
});

watch(() => props.show, (isVisible) => {
    if (isVisible && props.user) {
        form.name = props.user.name;
        form.email = props.user.email;
        form.role = props.user.role;
        form.phone = props.user.phone;
        form.password = '';
        form.avatar_path = '';
        changePassword.value = false;
    } else if (!isVisible) {
        form.reset();
        form.clearErrors();
    }
});

const handleUploadSuccess = (result) => {
    form.avatar_path = result.response.path;
};

const submit = () => {
    if (props.editMode) {
        form.put(route('admin.users.update', props.user.id), {
            onSuccess: () => emit('refresh', 'updated'),
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => emit('refresh', 'added'),
        });
    }
};
</script>
