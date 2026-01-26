<template>
    <Modal :show="show" @close="$emit('update:show', false)" max-width="4xl">
        <div class="p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ isEdit ? $t('messages.edit_variant') : $t('messages.add_variant') }}
                </h2>
                <button @click="$emit('update:show', false)" 
                    class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Product Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('messages.product') }}
                        </label>
                        <select v-model="form.product_id" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">{{ $t('messages.select_product') }}</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name?.[$page.props.locale] || product.name?.en }}
                            </option>
                        </select>
                        <InputError :message="form.errors.product_id" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('messages.sku') }}
                        </label>
                        <input v-model="form.sku" type="text" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            :placeholder="$t('messages.sku_placeholder')">
                        <InputError :message="form.errors.sku" />
                    </div>
                </div>

                <!-- Price and Stock -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('messages.price') }}
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input v-model="form.price" type="number" step="0.01" required
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        <InputError :message="form.errors.price" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('messages.discount_price') }}
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input v-model="form.discount_price" type="number" step="0.01"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        <InputError :message="form.errors.discount_price" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('messages.stock') }}
                        </label>
                        <input v-model="form.stock" type="number" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <InputError :message="form.errors.stock" />
                    </div>
                </div>

                <!-- Attributes Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.attributes') }}
                    </label>
                    <div class="space-y-3">
                        <div v-for="attribute in availableAttributes" :key="attribute.id" 
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" 
                                    :id="`attr-${attribute.id}`"
                                    v-model="selectedAttributes[attribute.id]"
                                    @change="updateAttributeValues(attribute.id)"
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <label :for="`attr-${attribute.id}`" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ attribute.name?.[$page.props.locale] || attribute.name?.en }}
                                </label>
                            </div>
                            <select v-if="selectedAttributes[attribute.id]" 
                                v-model="selectedAttributeValues[attribute.id]"
                                @change="syncAttributeValues"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-600 dark:text-white">
                                <option value="">{{ $t('messages.select_value') }}</option>
                                <option v-for="value in attribute.values" :key="value.id" :value="value.id">
                                    {{ value.value?.[$page.props.locale] || value.value?.en }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Primary Variant -->
                <div class="flex items-center">
                    <input v-model="form.is_primary" type="checkbox" id="is_primary"
                        class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="is_primary" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $t('messages.set_as_primary') }}
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="$emit('update:show', false)"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        {{ $t('messages.cancel') }}
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50">
                        {{ isEdit ? $t('messages.update') : $t('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import InputError from '@/Components/InputError.vue'
import axios from 'axios'

const props = defineProps({
    show: Boolean,
    variant: Object,
    products: Array,
    storeId: [Number, String]
})

const emit = defineEmits(['update:show', 'refresh'])

const isEdit = computed(() => !!props.variant)
const availableAttributes = ref([])
const selectedAttributes = ref({})
const selectedAttributeValues = ref({})

const form = useForm({
    product_id: '',
    sku: '',
    price: '',
    discount_price: '',
    stock: '',
    is_primary: false,
    attribute_value_ids: []
})

// Fetch available attributes
const fetchAttributes = async () => {
    try {
        const response = await axios.get(route('vendor.attributes.index', { store_id: props.storeId }))
        availableAttributes.value = response.data.attributes
    } catch (error) {
        console.error('Failed to fetch attributes:', error)
    }
}

const updateAttributeValues = (attributeId) => {
    if (!selectedAttributes.value[attributeId]) {
        delete selectedAttributeValues.value[attributeId]
    }
    syncAttributeValues()
}

const syncAttributeValues = () => {
    const values = Object.values(selectedAttributeValues.value).filter(v => v)
    form.attribute_value_ids = values
}

const submit = () => {
    const action = isEdit.value 
        ? route('vendor.variants.update', { variant: props.variant.id, store_id: props.storeId })
        : route('vendor.variants.store', { store_id: props.storeId })

    if (isEdit.value) {
        form.transform(data => ({ ...data, _method: 'PUT' }))
            .post(action, {
                onSuccess: () => {
                    emit('refresh')
                    form.reset()
                }
            })
    } else {
        form.post(action, {
            onSuccess: () => {
                emit('refresh')
                form.reset()
            }
        })
    }
}

// Initialize form when variant changes
watch(() => props.variant, (variant) => {
    if (variant) {
        form.product_id = variant.product_id
        form.sku = variant.sku
        form.price = variant.price
        form.discount_price = variant.discount_price
        form.stock = variant.stock
        form.is_primary = variant.is_primary
        form.attribute_value_ids = variant.attribute_values?.map(av => av.id) || []

        // Set selected attributes and values
        selectedAttributes.value = {}
        selectedAttributeValues.value = {}
        variant.attribute_values?.forEach(av => {
            selectedAttributes.value[av.attribute_id] = true
            selectedAttributeValues.value[av.attribute_id] = av.id
        })
    } else {
        form.reset()
        selectedAttributes.value = {}
        selectedAttributeValues.value = {}
    }
}, { immediate: true })

// Reset form when modal closes
watch(() => props.show, (show) => {
    if (!show) {
        form.reset()
        selectedAttributes.value = {}
        selectedAttributeValues.value = {}
    }
})

onMounted(() => {
    fetchAttributes()
})
</script>
