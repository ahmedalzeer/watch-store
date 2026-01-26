<template>
    <VendorLayout>

        <Head :title="$t('messages.variants')" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.variants') }}
                </h2>

                <div class="flex items-center gap-3">
                    <button @click="showBulkModal = true" :disabled="selectedVariants.length === 0"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95">
                        {{ $t('messages.bulk_edit') }}
                    </button>
                    <button @click="showCreateModal = true"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 shadow-md transition-all active:scale-95">
                        + {{ $t('messages.add_variant') }}
                    </button>
                </div>
            </div>
        </template>

        <!-- Filters Section -->
        <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.search') }}
                    </label>
                    <input v-model="filters.search" type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        :placeholder="$t('messages.search_variants')">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('messages.product') }}
                    </label>
                    <select v-model="filters.product"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">{{ $t('messages.all_products') }}</option>
                        <option v-for="product in products" :key="product.id" :value="product.id">
                            {{ product.name?.[$page.props.locale] || product.name?.en }}
                        </option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="applyFilters"
                        class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        {{ $t('messages.apply_filters') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Selection Bar -->
        <div v-if="selectedVariants.length > 0"
            class="mb-6 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <input type="checkbox" :checked="selectedVariants.length === variants.data.length"
                    @change="toggleSelectAll"
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <span class="text-sm font-medium text-purple-700 dark:text-purple-300">
                    {{ selectedVariants.length }} {{ $t('messages.selected') }}
                </span>
            </div>
            <button @click="clearSelection" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                {{ $t('messages.clear_selection') }}
            </button>
        </div>

        <!-- Variants Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-xs border dark:border-gray-700">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3 text-start">
                                <input type="checkbox" :checked="selectedVariants.length === variants.data.length"
                                    @change="toggleSelectAll"
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.product') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.sku') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.attributes') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.price') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.stock') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.status') }}</th>
                            <th class="px-4 py-3 text-start">{{ $t('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="variant in variants.data" :key="variant.id"
                            class="text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" :checked="selectedVariants.includes(variant.id)"
                                    @change="toggleVariantSelection(variant.id)"
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center font-semibold">
                                    <div class="flex flex-col">
                                        <span>{{ variant.product?.name?.[$page.props.locale] ||
                                            variant.product?.name?.en
                                            }}</span>
                                        <span class="text-xs text-gray-400">{{ variant.product?.sku }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-mono">
                                    {{ variant.sku }}
                                </div>
                                <div v-if="variant.is_primary" class="text-xs text-purple-600 font-medium">
                                    {{ $t('messages.primary') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="attr in variant.attribute_values" :key="attr.id"
                                        class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                                        {{ attr.value?.[$page.props.locale] || attr.value?.en }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    ${{ variant.price }}
                                </div>
                                <div v-if="variant.discount_price" class="text-xs text-red-600 line-through">
                                    ${{ variant.discount_price }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">
                                        {{ variant.stock }}
                                    </span>
                                    <span :class="getStockClass(variant.stock)" class="px-2 py-1 text-xs rounded-full">
                                        {{ getStockLabel(variant.stock) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <button @click="toggleVariantStatus(variant)"
                                    :class="variant.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    class="px-2 py-1 text-xs rounded-full font-medium">
                                    {{ variant.stock > 0 ? $t('messages.active') : $t('messages.out_of_stock') }}
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button @click="editVariant(variant)" class="text-purple-600 hover:text-purple-700">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteVariant(variant)" class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                <Pagination :links="variants.links" />
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <VariantModal v-model:show="showCreateModal" :variant="editingVariant" :products="products" :store-id="storeId"
            @refresh="refreshVariants" />

        <!-- Bulk Edit Modal -->
        <BulkEditModal v-model:show="showBulkModal" :selected-variants="selectedVariants" :products="products"
            :store-id="storeId" @refresh="refreshVariants" />
    </VendorLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import VendorLayout from '@/Layouts/VendorLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import VariantModal from './Partials/VariantModal.vue'
import BulkEditModal from './Partials/BulkEditModal.vue'

const props = defineProps({
    variants: Object,
    products: Array,
    storeId: [Number, String],
    filters: Object
})

const showCreateModal = ref(false)
const showBulkModal = ref(false)
const editingVariant = ref(null)
const selectedVariants = ref([])

const filters = ref({
    search: props.filters?.search || '',
    product: props.filters?.product || ''
})

// Methods
const applyFilters = () => {
    router.get(route('vendor.variants.index', {
        store_id: props.storeId,
        ...filters.value
    }, {
        preserveState: true,
        preserveScroll: true
    }))
}

const toggleSelectAll = () => {
    if (selectedVariants.value.length === props.variants.data.length) {
        selectedVariants.value = []
    } else {
        selectedVariants.value = props.variants.data.map(v => v.id)
    }
}

const toggleVariantSelection = (variantId) => {
    const index = selectedVariants.value.indexOf(variantId)
    if (index > -1) {
        selectedVariants.value.splice(index, 1)
    } else {
        selectedVariants.value.push(variantId)
    }
}

const clearSelection = () => {
    selectedVariants.value = []
}

const editVariant = (variant) => {
    editingVariant.value = variant
    showCreateModal.value = true
}

const deleteVariant = (variant) => {
    if (confirm(`Are you sure you want to delete variant ${variant.sku}?`)) {
        router.delete(route('vendor.variants.destroy', {
            variant: variant.id,
            store_id: props.storeId
        }), {
            onSuccess: () => refreshVariants()
        })
    }
}

const toggleVariantStatus = (variant) => {
    router.post(route('vendor.variants.toggle-status', {
        variant: variant.id,
        store_id: props.storeId
    }), {}, {
        onSuccess: () => refreshVariants()
    })
}

const refreshVariants = () => {
    router.reload({ only: ['variants'] })
    showCreateModal.value = false
    showBulkModal.value = false
    editingVariant.value = null
    selectedVariants.value = []
}

const page = usePage()

const getStockClass = (stock) => {
    if (stock <= 2) return 'bg-red-100 text-red-800'
    if (stock <= 10) return 'bg-yellow-100 text-yellow-800'
    return 'bg-green-100 text-green-800'
}

const getStockLabel = (stock) => {
    if (stock <= 2) return page.props.locale === 'ar' ? 'منخفض' : 'Low'
    if (stock <= 10) return page.props.locale === 'ar' ? 'محدود' : 'Limited'
    return page.props.locale === 'ar' ? 'متوفر' : 'Available'
}

// Watch for filter changes
watch(filters, () => {
    applyFilters()
}, { deep: true })
</script>
