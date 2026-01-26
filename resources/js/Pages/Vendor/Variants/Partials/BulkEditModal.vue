<template>
    <Modal :show="show" @close="$emit('update:show', false)" max-width="2xl">
        <div class="p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $t('messages.bulk_edit_variants') }}
                </h2>
                <button @click="$emit('update:show', false)" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('messages.selected_variants_count', { count: selectedVariants.length }) }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Bulk Price Update -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.update_price') }}
                    </label>
                    <div class="flex items-center gap-3">
                        <input v-model="bulkForm.price" type="number" step="0.01"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            :placeholder="$t('messages.new_price')">
                        <select v-model="bulkForm.price_operation"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="set">{{ $t('messages.set_to') }}</option>
                            <option value="increase">{{ $t('messages.increase_by') }}</option>
                            <option value="decrease">{{ $t('messages.decrease_by') }}</option>
                            <option value="percentage">{{ $t('messages.percentage') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Bulk Stock Update -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.update_stock') }}
                    </label>
                    <div class="flex items-center gap-3">
                        <input v-model="bulkForm.stock" type="number"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            :placeholder="$t('messages.new_stock')">
                        <select v-model="bulkForm.stock_operation"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="set">{{ $t('messages.set_to') }}</option>
                            <option value="add">{{ $t('messages.add') }}</option>
                            <option value="subtract">{{ $t('messages.subtract') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Bulk Status Update -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.update_status') }}
                    </label>
                    <select v-model="bulkForm.status"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">{{ $t('messages.no_change') }}</option>
                        <option value="active">{{ $t('messages.active') }}</option>
                        <option value="inactive">{{ $t('messages.inactive') }}</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="$emit('update:show', false)"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        {{ $t('messages.cancel') }}
                    </button>
                    <button type="submit" :disabled="bulkForm.processing"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50">
                        {{ $t('messages.apply_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    show: Boolean,
    selectedVariants: Array,
    storeId: [Number, String]
})

const emit = defineEmits(['update:show', 'refresh'])

const bulkForm = useForm({
    variants: {},
    price: '',
    price_operation: 'set',
    stock: '',
    stock_operation: 'set',
    status: ''
})

const submit = () => {
    // Prepare variants data
    const variantsData = {}
    props.selectedVariants.forEach(variantId => {
        variantsData[variantId] = {}
    })

    // Add price updates if specified
    if (bulkForm.price) {
        props.selectedVariants.forEach(variantId => {
            variantsData[variantId].price = bulkForm.price
            variantsData[variantId].price_operation = bulkForm.price_operation
        })
    }

    // Add stock updates if specified
    if (bulkForm.stock) {
        props.selectedVariants.forEach(variantId => {
            variantsData[variantId].stock = bulkForm.stock
            variantsData[variantId].stock_operation = bulkForm.stock_operation
        })
    }

    // Add status updates if specified
    if (bulkForm.status) {
        props.selectedVariants.forEach(variantId => {
            variantsData[variantId].status = bulkForm.status
        })
    }

    bulkForm.variants = variantsData

    bulkForm.post(route('vendor.variants.bulk-update', { store_id: props.storeId }), {
        onSuccess: () => {
            emit('refresh')
            bulkForm.reset()
        }
    })
}

// Reset form when modal closes
watch(() => props.show, (show) => {
    if (!show) {
        bulkForm.reset()
    }
})
</script>
