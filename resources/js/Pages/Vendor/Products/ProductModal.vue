```vue
<template>
    <Modal :show="show" @close="$emit('close')" max-width="2xl">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold border-b pb-4 dark:border-gray-700 flex justify-between items-center">
                <span>{{ isEdit ? $t('messages.edit_product') : $t('messages.create_product') }}</span>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </h2>

            <form @submit.prevent="submit"
                class="mt-6 space-y-5 text-start overflow-y-auto max-h-[75vh] pr-2 custom-scrollbar">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.name') }} (AR)</label>
                        <input v-model="form.name.ar" type="text" class="form-input" required />
                        <InputError :message="form.errors['name.ar']" />
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.name') }} (EN)</label>
                        <input v-model="form.name.en" type="text" class="form-input" required />
                        <InputError :message="form.errors['name.en']" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.slug') }}</label>
                        <input v-model="form.slug" type="text" class="form-input" required />
                        <InputError :message="form.errors.slug" />
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <label class="label-dark">{{ $t('messages.sku') }}</label>
                            <button type="button" @click="form.sku = generateSKU(form.name.en)"
                                class="text-[10px] text-purple-600 font-bold hover:underline uppercase">
                                {{ $t('messages.regenerate') }}
                            </button>
                        </div>
                        <div class="relative">
                            <input v-model="form.sku" type="text" class="form-input pr-10"
                                :placeholder="$t('messages.sku_placeholder')" required />
                            <div
                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                        </div>
                        <InputError :message="form.errors.sku" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.description') }} (AR)</label>
                        <textarea v-model="form.description.ar" type="text" class="form-input" required></textarea>
                        <InputError :message="form.errors['description.ar']" />
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.description') }} (EN)</label>
                        <textarea v-model="form.description.en" type="text" class="form-input" required></textarea>
                        <InputError :message="form.errors['description.en']" />
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.category') }}</label>
                        <select v-model="form.category_id" class="form-input" required>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name?.[$page.props.locale] }}
                            </option>
                        </select>
                        <InputError :message="form.errors.category_id" />
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.brand') }}</label>
                        <select v-model="form.brand_id" class="form-input">
                            <option :value="null">---</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                {{ brand.name?.[$page.props.locale] }}
                            </option>
                        </select>
                        <InputError :message="form.errors.brand_id" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.price') }}</label>
                        <input v-model="form.price" type="number" step="0.01" class="form-input" required />
                        <InputError :message="form.errors.price" />
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.discount_price') }}</label>
                        <input v-model="form.discount_price" type="number" step="0.01" class="form-input" />
                        <InputError :message="form.errors.discount_price" />
                    </div>
                    <div class="space-y-1">
                        <label class="label-dark">{{ $t('messages.stock') }}</label>
                        <input v-model="form.stock" type="number" class="form-input" required />
                        <InputError :message="form.errors.stock" />
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="checkbox-purple">
                        <span class="ml-2 text-xs uppercase font-bold">{{ $t('messages.active') }}</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" v-model="form.main_menu" class="checkbox-purple">
                        <span class="ml-2 text-xs uppercase font-bold">{{ $t('messages.menu') }}</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" v-model="form.main_store" class="checkbox-purple">
                        <span class="ml-2 text-xs uppercase font-bold">{{ $t('messages.store') }}</span>
                    </label>
                    <div class="flex flex-col">
                        <select v-model="form.condition" class="text-xs border-gray-300 dark:bg-gray-800 rounded">
                            <option value="new">New</option>
                            <option value="used">Used</option>
                            <option value="refurbished">Refurbished</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="label-dark">{{ $t('messages.product_images') }}</label>
                    <Dropzone :url="route('vendor.media.upload.temp')"
                        :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }" @success="handleUploadSuccess"
                        class="dropzone-minimal" />

                    <div v-if="form.image_paths.length" class="mt-6">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-widest">
                            {{ $t('messages.select_main_image') }}
                        </p>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="(path, index) in form.image_paths" :key="index"
                                class="relative aspect-square rounded-xl border-2 transition" :class="form.main_image_path === path
                                    ? 'border-purple-600'
                                    : 'border-gray-200 dark:border-gray-700'">

                                <img :src="'/storage/' + path" class="w-full h-full object-cover rounded-xl" />

                                <button type="button" @click="form.main_image_path = path" style="margin-left:29px;"
                                    class="absolute top-2 left-2 z-[999]
                                               w-9 h-9 rounded-full
                                               flex items-center justify-center
                                               bg-white text-gray-400
                                               shadow-xl hover:text-purple-600" :class="form.main_image_path === path
                                                ? 'bg-purple-600 text-white'
                                                : ''">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>

                                <button type="button" @click="removeImage(index)"
                                    style="font-size: 13px;width: 19px;height: 19px;" class="absolute top-2 right-2 z-[999]
                                               w-8 h-8 rounded-full
                                               bg-red-600 text-white
                                               flex items-center justify-center
                                               shadow-xl hover:bg-red-700">
                                    ✕
                                </button>

                                <div v-if="form.main_image_path === path" class="absolute bottom-2 left-1/2 -translate-x-1/2
                                            bg-purple-600 text-white text-[10px]
                                            px-2 py-0.5 rounded-full font-bold">
                                    {{ $t('messages.main') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <VariantManager v-model="form.variants" :store-id="storeId" :base-price="form.price"
                    :base-sku="form.sku" :product-stock="form.stock" />

                <div class="mt-8 flex justify-end space-x-3 rtl:space-x-reverse border-t pt-6 dark:border-gray-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                        {{ $t('messages.cancel') }}
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="px-8 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold shadow-md transition">
                        {{ isEdit ? $t('messages.update') : $t('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import Dropzone from '@/Components/Dropzone.vue'
import InputError from '@/Components/InputError.vue'
import VariantManager from './Partials/VariantManager.vue'

const props = defineProps({
    show: Boolean,
    editProduct: Object,
    categories: Array,
    brands: Array,
    storeId: [Number, String],
})

const emit = defineEmits(['close', 'refresh'])
const isEdit = computed(() => !!props.editProduct)

const form = useForm({
    store_id: props.storeId,
    category_id: '',
    brand_id: '',
    name: { ar: '', en: '' },
    description: { ar: '', en: '' },
    slug: '',
    sku: '',
    price: '',
    discount_price: null,
    stock: 0,
    condition: 'new',
    is_active: true,
    main_menu: true,
    main_store: true,
    image_paths: [],
    main_image_path: null,
    variants: []
})

const handleUploadSuccess = (r) => {
    form.image_paths.push(r.response.path)
}

const removeImage = (index) => {
    const p = form.image_paths[index]
    form.image_paths.splice(index, 1)
    if (form.main_image_path === p) form.main_image_path = null
}


watch(() => props.show, (val) => {
    if (val && props.editProduct) {
        Object.assign(form, props.editProduct)
        form.name = { ...props.editProduct.name }
        form.description = { ...props.editProduct.description }

        if (props.editProduct.variants) {
            form.variants = props.editProduct.variants.map(v => ({
                id: v.id,
                sku: v.sku,
                price: v.price,
                stock: v.stock,
                attribute_value_ids: v.attribute_values.map(av => av.id),
                valueNames: v.attribute_values.map(av => av.value[usePage().props.locale] || av.value.en)
            }))
        }
    } else if (val) {
        form.reset()
        form.store_id = props.storeId
        form.variants = []
    }
})

const generateSKU = (name) => {
    if (!name) return '';

    const prefix = name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 3);

    const randomStr = Math.random().toString(36).substring(2, 7).toUpperCase();

    return `${prefix}-${props.storeId}-${randomStr}`;
};

watch(() => form.name.en, (newName) => {
    if (!isEdit.value && (!form.sku || form.sku === '')) {
        form.sku = generateSKU(newName);
    }
});

const submit = () => {
    if (form.image_paths.length && !form.main_image_path) return alert('Select main image')
    const action = isEdit.value
        ? route('vendor.products.update', props.editProduct.id)
        : route('vendor.products.store')

    if (isEdit.value) {
        form.transform(d => ({ ...d, _method: 'PUT' }))
            .post(action, { onSuccess: () => emit('refresh') })
    } else {
        form.post(action, { onSuccess: () => emit('refresh') })
    }
}
</script>

<style scoped>
.label-dark {
    @apply block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide;
}

.form-input {
    @apply w-full mt-1 block text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 transition;
}

.checkbox-purple {
    @apply w-5 h-5 text-purple-600 border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded focus:ring-purple-500 transition cursor-pointer;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-gray-300 dark:bg-gray-600 rounded-full;
}

:deep(.dz-preview) {
    display: none !important;
}
</style>
```
