<template>
    <Modal :show="show" @close="$emit('close')" max-width="2xl">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-xl">
            <h2 class="text-lg font-bold border-b pb-4 dark:border-gray-700 flex items-center gap-2">
                <span class="text-purple-600">⚙️</span>
                {{ $t('messages.manage_specifications') }}: {{ product?.name?.[$page.props.locale] }}
            </h2>

            <div class="mt-6 space-y-4 max-h-[65vh] overflow-y-auto pr-2 custom-scrollbar">
                <div v-if="dynamicSpecs.length > 0" class="grid grid-cols-12 gap-4 px-2 mb-2">
                    <div class="col-span-5 text-[10px] font-bold uppercase text-gray-400">{{
                        $t('messages.specification_name') }}</div>
                    <div class="col-span-6 text-[10px] font-bold uppercase text-gray-400">{{
                        $t('messages.specification_value') }}</div>
                </div>

                <div v-for="(spec, index) in dynamicSpecs" :key="index"
                    class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-200 dark:border-gray-700 relative group animate-fadeIn">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3 border-r dark:border-gray-700 pr-4">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-[10px] bg-purple-100 text-purple-600 px-1.5 py-0.5 rounded font-bold">AR</span>
                                <input v-model="spec.key_ar" type="text" placeholder="مثلاً: المادة"
                                    class="form-input text-xs" />
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-[10px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded font-bold">EN</span>
                                <input v-model="spec.key_en" type="text" placeholder="e.g. Material"
                                    class="form-input text-xs" />
                            </div>
                        </div>

                        <div class="space-y-3 relative">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-[10px] bg-purple-100 text-purple-600 px-1.5 py-0.5 rounded font-bold">AR</span>
                                <input v-model="spec.value_ar" type="text" placeholder="مثلاً: فولاذ مقاوم للصدأ"
                                    class="form-input text-xs" />
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-[10px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded font-bold">EN</span>
                                <input v-model="spec.value_en" type="text" placeholder="e.g. Stainless Steel"
                                    class="form-input text-xs" />
                            </div>

                            <button @click="removeSpec(index)"
                                class="absolute -right-2 top-1/2 -translate-y-1/2 text-red-400 hover:text-red-600 p-2 transition opacity-0 group-hover:opacity-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button @click="addSpec"
                    class="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-gray-500 hover:border-purple-500 hover:text-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-all text-sm font-bold">
                    + {{ $t('messages.add_translated_spec') }}
                </button>
            </div>

            <div class="mt-8 flex justify-end space-x-3 rtl:space-x-reverse border-t pt-4 dark:border-gray-700">
                <button @click="$emit('close')" class="px-4 py-2 text-gray-500">{{ $t('messages.cancel') }}</button>
                <button @click="saveSpecs" :disabled="form.processing"
                    class="px-8 py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 shadow-lg flex items-center gap-2 transition-all">
                    <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    {{ form.processing ? $t('messages.saving') : $t('messages.save_changes') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({ show: Boolean, product: Object, storeId: [Number, String], });
const emit = defineEmits(['close', 'refresh']);

const dynamicSpecs = ref([]);
const form = useForm({ specifications: [], store_id: props.storeId, });

watch(() => props.show, (val) => {
    if (val && props.product) {

        const specs = props.product.specifications || [];


        dynamicSpecs.value = Array.isArray(specs) ? specs.map(s => ({
            key_ar: s.key?.ar || '',
            key_en: s.key?.en || '',
            value_ar: s.value?.ar || '',
            value_en: s.value?.en || '',
        })) : [];
    }
});

const addSpec = () => dynamicSpecs.value.push({ key_ar: '', key_en: '', value_ar: '', value_en: '' });
const removeSpec = (index) => dynamicSpecs.value.splice(index, 1);

const saveSpecs = () => {

    form.specifications = dynamicSpecs.value.map(s => ({
        key: { ar: s.key_ar, en: s.key_en },
        value: { ar: s.value_ar, en: s.value_en }
    }));

    form.transform(data => ({
        ...data,
        _method: 'PUT',
        update_specs_only: true
    })).post(route('vendor.products.update', props.product.id), {
        onSuccess: () => {
            emit('refresh', 'updated');
            emit('close');
        }
    });
};
</script>

<style scoped>
.form-input {
    @apply w-full mt-1 block text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 transition;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 0px;
    background: transparent;
}

.custom-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
</style>
