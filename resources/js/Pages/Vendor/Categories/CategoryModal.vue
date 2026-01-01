<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold border-b pb-4 dark:border-gray-700 flex justify-between items-center">
                <span>{{ isEdit ? $t('messages.edit_category') : $t('messages.create_category') }}</span>
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
                        <label class="label-dark">{{ $t('messages.parent_category') }} ({{ $t('messages.optional')
                            }})</label>
                        <select v-model="form.parent_id" class="form-input">
                            <option :value="null">{{ $t('messages.none') }}</option>
                            <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">
                                {{ cat.name?.[$page.props.locale] || cat.name?.ar }}
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center md:pt-6">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.is_active" class="checkbox-purple">
                            <span
                                class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">
                                {{ $t('messages.active') }}
                            </span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.name') }} (AR)</label>
                        <input v-model="form.name.ar" type="text" class="form-input" required />
                        <div v-if="form.errors['name.ar']" class="error-msg">{{ form.errors['name.ar'] }}</div>
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.name') }} (EN)</label>
                        <input v-model="form.name.en" type="text" class="form-input" required />
                        <div v-if="form.errors['name.en']" class="error-msg">{{ form.errors['name.en'] }}</div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="label-dark">{{ $t('messages.slug') }}</label>
                    <input v-model="form.slug" type="text" class="form-input" placeholder="electronics-gadgets" />
                    <div v-if="form.errors.slug" class="error-msg">{{ form.errors.slug }}</div>
                </div>

                <div class="space-y-2">
                    <label class="label-dark">{{ $t('messages.category_icon') }}</label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-700/30">
                        <Dropzone :url="route('vendor.media.upload.temp')"
                            :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }"
                            @success="(r) => form.image_path = r.response.path" />
                    </div>
                    <div v-if="form.errors.image_path" class="error-msg">{{ form.errors.image_path }}</div>
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
    parentCategories: Array,
    editCategory: Object,
    storeId: [Number, String]
});
const emit = defineEmits(['close', 'refresh']);
const isEdit = computed(() => !!props.editCategory);

const form = useForm({
    store_id: props.storeId,
    parent_id: null,
    name: { ar: '', en: '' },
    slug: '',
    image_path: '', // تأكد أن الاسم يطابق الـ Request والـ Controller
    is_active: true,
});

watch(() => props.show, (val) => {
    if (val && props.editCategory) {
        form.parent_id = props.editCategory.parent_id;
        form.name = { ar: props.editCategory.name.ar, en: props.editCategory.name.en };
        form.slug = props.editCategory.slug;
        form.is_active = !!props.editCategory.is_active;
        form.store_id = props.storeId;
        form.image_path = ''; // ريست لمسار الصورة عند التعديل
    } else if (val) {
        form.reset();
        form.store_id = props.storeId;
    }
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('vendor.categories.update', props.editCategory.id), {
            onSuccess: () => emit('refresh', 'updated'),
        });
    } else {
        form.post(route('vendor.categories.store'), {
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
