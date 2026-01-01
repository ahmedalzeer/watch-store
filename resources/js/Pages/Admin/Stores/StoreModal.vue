<template>
    <Modal :show="show" @close="$emit('close')" max-width="2xl">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold border-b pb-4 dark:border-gray-700 flex justify-between items-center">
                <span>{{ isEdit ? $t('messages.edit_store') : $t('messages.create_new_store') }}</span>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </h2>

            <form @submit.prevent="submit"
                class="mt-6 space-y-5 text-start overflow-y-auto max-h-[75vh] pr-2 custom-scrollbar">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.select_vendor') }}</label>
                        <select v-model="form.user_id" class="form-input" :disabled="isEdit">
                            <option value="">{{ $t('messages.choose_vendor') }}</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <div v-if="form.errors.user_id" class="error-msg">{{ form.errors.user_id }}</div>
                    </div>
                    <div class="flex items-center md:pt-8">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.is_active" class="checkbox-purple">
                            <span
                                class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">{{
                                    $t('messages.active') }}</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.name') }} (AR)</label>
                        <input v-model="form.name.ar" type="text" class="form-input" />
                        <div v-if="form.errors['name.ar']" class="error-msg">{{ form.errors['name.ar'] }}</div>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.name') }} (EN)</label>
                        <input v-model="form.name.en" type="text" class="form-input" />
                        <div v-if="form.errors['name.en']" class="error-msg">{{ form.errors['name.en'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.description') }} (AR)</label>
                        <textarea v-model="form.description.ar" class="form-input h-20 resize-none"></textarea>
                        <div v-if="form.errors['description.ar']" class="error-msg">{{ form.errors['description.ar'] }}
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.description') }} (EN)</label>
                        <textarea v-model="form.description.en" class="form-input h-20 resize-none"></textarea>
                        <div v-if="form.errors['description.en']" class="error-msg">{{ form.errors['description.en'] }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.subdomain') }}</label>
                        <input v-model="form.subdomain" type="text" class="form-input" />
                        <div v-if="form.errors.subdomain" class="error-msg">{{ form.errors.subdomain }}</div>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.email') }}</label>
                        <input v-model="form.contact_email" type="email" class="form-input" />
                        <div v-if="form.errors.contact_email" class="error-msg">{{ form.errors.contact_email }}</div>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.phone') }}</label>
                        <input v-model="form.phone" type="text" class="form-input" />
                        <div v-if="form.errors.phone" class="error-msg">{{ form.errors.phone }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.currency') }}</label>
                        <select v-model="form.currency_id" class="form-input">
                            <option value="">{{ $t('messages.choose_currency') }}</option>
                            <option v-for="curr in currencies" :key="curr.id" :value="curr.id">
                                {{ curr.name?.[$page.props.locale] || curr.name?.ar }} ({{ curr.symbol }})
                            </option>
                        </select>
                        <div v-if="form.errors.currency_id" class="error-msg">{{ form.errors.currency_id }}</div>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.theme_color') }}</label>
                        <input v-model="form.theme_color" type="color"
                            class="h-10 w-full bg-transparent border-none cursor-pointer" />
                        <div v-if="form.errors.theme_color" class="error-msg">{{ form.errors.theme_color }}</div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700/20 rounded-lg space-y-3">
                    <h3 class="text-sm font-bold text-purple-600">{{ $t('messages.social_links') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="space-y-1">
                            <input v-model="form.social_links.facebook" placeholder="Facebook URL"
                                class="form-input text-xs" />
                            <div v-if="form.errors['social_links.facebook']" class="error-msg">{{
                                form.errors['social_links.facebook'] }}</div>
                        </div>
                        <div class="space-y-1">
                            <input v-model="form.social_links.instagram" placeholder="Instagram URL"
                                class="form-input text-xs" />
                            <div v-if="form.errors['social_links.instagram']" class="error-msg">{{
                                form.errors['social_links.instagram'] }}</div>
                        </div>
                        <div class="space-y-1">
                            <input v-model="form.social_links.twitter" placeholder="Twitter URL"
                                class="form-input text-xs" />
                            <div v-if="form.errors['social_links.twitter']" class="error-msg">{{
                                form.errors['social_links.twitter'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="label-dark">{{ $t('messages.store_logo') }}</label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-700/30">
                        <Dropzone :url="route('admin.media.upload.temp')"
                            :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }"
                            @success="(r) => form.logo_path = r.response.path" />
                    </div>
                    <div v-if="form.errors.logo_path" class="error-msg">{{ form.errors.logo_path }}</div>
                </div>

                <div class="mt-8 flex justify-end space-x-3 rtl:space-x-reverse border-t pt-6 dark:border-gray-700">
                    <button type="button" @click="$emit('close')"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ $t('messages.cancel') }}
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="px-8 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition active:scale-95 disabled:opacity-50 font-bold shadow-md">
                        {{ isEdit ? $t('messages.update') : $t('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import Dropzone from '@/Components/Dropzone.vue';

const props = defineProps({ show: Boolean, users: Array, currencies: Array, editStore: Object });
const emit = defineEmits(['close', 'refresh']);
const isEdit = computed(() => !!props.editStore);

const form = useForm({
    user_id: '',
    name: { ar: '', en: '' },
    description: { ar: '', en: '' },
    subdomain: '',
    contact_email: '',
    phone: '',
    currency_id: '',
    theme_color: '#7e3af2',
    social_links: { facebook: '', instagram: '', twitter: '' },
    logo_path: '',
    is_active: true,
});

watch(() => props.show, (val) => {
    if (val && props.editStore) {
        form.user_id = props.editStore.user_id;
        form.name = { ar: props.editStore.name.ar, en: props.editStore.name.en };
        form.description = { ar: props.editStore.description?.ar || '', en: props.editStore.description?.en || '' };
        form.subdomain = props.editStore.subdomain;
        form.contact_email = props.editStore.contact_email || '';
        form.phone = props.editStore.phone || '';
        form.currency_id = props.editStore.currency_id || '';
        form.theme_color = props.editStore.theme_color || '#7e3af2';
        form.social_links = props.editStore.social_links || { facebook: '', instagram: '', twitter: '' };
        form.is_active = !!props.editStore.is_active;
    } else if (val) {
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    if (props.editStore) {
        form.put(route('admin.stores.update', props.editStore.id), {
            onSuccess: () => emit('refresh', 'updated'),
        });
    } else {
        form.post(route('admin.stores.store'), {
            onSuccess: () => emit('refresh', 'added'),
        });
    }
};
</script>

<style scoped>
.label-dark {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-400;
}

.form-input {
    @apply w-full mt-1 block text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 transition;
}

.error-msg {
    @apply text-red-500 text-xs mt-1;
}

.checkbox-purple {
    @apply w-5 h-5 text-purple-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-purple-500;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-gray-300 dark:bg-gray-600 rounded-full;
}
</style>
