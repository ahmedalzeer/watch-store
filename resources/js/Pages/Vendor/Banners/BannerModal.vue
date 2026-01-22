<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold border-b pb-4 dark:border-gray-700 flex justify-between items-center">
                <span>{{ isEdit ? $t('messages.edit_banner') : $t('messages.create_banner') }}</span>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
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
                        <label class="label-dark">{{ $t('messages.title') }} (AR)</label>
                        <input v-model="form.title.ar" type="text" class="form-input" required />
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.title') }} (EN)</label>
                        <input v-model="form.title.en" type="text" class="form-input" required />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.description') }} (AR)</label>
                        <textarea v-model="form.description.ar" class="form-input" rows="2"></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.description') }} (EN)</label>
                        <textarea v-model="form.description.en" class="form-input" rows="2"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.banner_type') }}</label>
                        <select v-model="form.type" class="form-input">
                            <option value="promotion">Promotion</option>
                            <option value="sale">Sale</option>
                            <option value="featured">Featured</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.link') }}</label>
                        <input v-model="form.link" type="url" class="form-input" placeholder="https://..." />
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="checkbox" v-model="form.active" class="checkbox-purple">
                        <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">{{ $t('messages.active')
                        }}</span>
                    </label>
                </div>

                <div class="space-y-2">
                    <label class="label-dark">{{ $t('messages.banner_image') }}</label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-700/30">
                        <Dropzone :url="route('vendor.media.upload.temp')"
                            :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }"
                            @success="(r) => form.image_path = r.response.path" />
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3 rtl:space-x-reverse border-t pt-6 dark:border-gray-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                        {{ $t('messages.cancel') }}
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="px-8 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold shadow-md transition active:scale-95">
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

const props = defineProps({
    show: Boolean,
    editBanner: Object,
    storeId: [Number, String]
});
const emit = defineEmits(['close', 'refresh']);
const isEdit = computed(() => !!props.editBanner);

const form = useForm({
    store_id: props.storeId,
    title: { ar: '', en: '' },
    description: { ar: '', en: '' },
    type: 'promotion',
    link: '',
    active: true,
    image_path: '',
});

watch(() => props.show, (val) => {
    if (val && props.editBanner) {
        form.title = { ...props.editBanner.title };
        form.description = { ...props.editBanner.description };
        form.type = props.editBanner.type;
        form.link = props.editBanner.link;
        form.active = props.editBanner.active;
        form.store_id = props.storeId;
        form.image_path = '';
    } else if (val) {
        form.reset();
        form.store_id = props.storeId;
    }
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('vendor.banners.update', props.editBanner.id), {
            onSuccess: () => emit('refresh', 'updated'),
        });
    } else {
        form.post(route('vendor.banners.store'), {
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

.checkbox-purple {
    @apply w-5 h-5 text-purple-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-purple-500;
}
</style>