<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold border-b pb-4 dark:border-gray-700 flex justify-between items-center">
                <span>{{ isEdit ? $t('messages.edit_brand') : $t('messages.create_brand') }}</span>
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
                    <input v-model="form.slug" type="text" class="form-input" placeholder="rolex-luxury" />
                    <div v-if="form.errors.slug" class="error-msg">{{ form.errors.slug }}</div>
                </div>

                <div class="space-y-1">
                    <label class="label-dark">{{ $t('messages.website') }} ({{ $t('messages.optional') }})</label>
                    <input v-model="form.website" type="url" class="form-input" placeholder="https://www.brand.com" />
                    <div v-if="form.errors.website" class="error-msg">{{ form.errors.website }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.is_featured" class="checkbox-purple">
                            <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">{{
                                $t('messages.featured_brand') }}</span>
                        </label>
                    </div>
                    <div class="space-y-1">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.is_active" class="checkbox-purple">
                            <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">{{
                                $t('messages.active') }}</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.main_menu" class="checkbox-purple">
                            <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">{{
                                $t('messages.main_menu') }}</span>
                        </label>
                    </div>
                    <div class="space-y-1">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.main_store" class="checkbox-purple">
                            <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">{{
                                $t('messages.main_store') }}</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="label-dark">{{ $t('messages.brand_logo') }}</label>
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
    editBrand: Object,
    storeId: [Number, String]
});
const emit = defineEmits(['close', 'refresh']);
const isEdit = computed(() => !!props.editBrand);

const form = useForm({
    store_id: props.storeId,
    name: { ar: '', en: '' },
    slug: '',
    website: '',
    is_featured: false,
    is_active: true,
    main_menu: false,
    main_store: false,
    image_path: '',
});

watch(() => props.show, (val) => {
    if (val && props.editBrand) {
        form.name = { ar: props.editBrand.name.ar, en: props.editBrand.name.en };
        form.slug = props.editBrand.slug;
        form.website = props.editBrand.website || '';
        form.is_featured = !!props.editBrand.is_featured;
        form.is_active = !!props.editBrand.is_active;
        form.main_menu = !!props.editBrand.main_menu;
        form.main_store = !!props.editBrand.main_store;
        form.store_id = props.storeId;
        form.image_path = '';
    } else if (val) {
        form.reset();
        form.store_id = props.storeId;
    }
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('vendor.brands.update', props.editBrand.id), {
            onSuccess: () => emit('refresh', 'updated'),
        });
    } else {
        form.post(route('vendor.brands.store'), {
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
