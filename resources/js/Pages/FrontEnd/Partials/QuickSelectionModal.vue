<template>
    <div v-if="store.isOpen" class="fixed inset-0 z-[100] overflow-y-auto" @click.self="store.close()">
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-4xl bg-white rounded-sm shadow-2xl overflow-hidden transform transition-all flex flex-col md:flex-row"
                :dir="$page.props.locale === 'ar' ? 'rtl' : 'ltr'">

                <!-- Close Button -->
                <button @click="store.close()"
                    class="absolute top-4 right-4 z-20 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-black hover:text-white transition-all">
                    <i class="fa fa-times"></i>
                </button>

                <!-- Loading State -->
                <div v-if="store.loading" class="w-full h-[500px] flex items-center justify-center bg-white">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-black"></div>
                </div>

                <template v-else-if="store.product">
                    <!-- Images Gallery -->
                    <div class="w-full md:w-1/2 p-8 bg-gray-50 flex flex-col gap-4">
                        <div class="aspect-square bg-white border border-gray-100 p-8 flex items-center justify-center">
                            <img :src="activeImage" :alt="store.product.name"
                                class="max-w-full max-h-full object-contain transition-all duration-500">
                        </div>
                        <div v-if="store.product.media?.length > 1" class="grid grid-cols-4 gap-2">
                            <button v-for="media in store.product.media" :key="media.id"
                                @click="activeImage = media.original_url"
                                class="aspect-square bg-white border p-2 flex items-center justify-center hover:border-black transition-all"
                                :class="activeImage === media.original_url ? 'border-black' : 'border-gray-200'">
                                <img :src="media.original_url" class="object-contain max-h-full">
                            </button>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="w-full md:w-1/2 p-8 flex flex-col">
                        <div class="mb-6">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 block">
                                {{ store.product.brand?.name }}
                            </span>
                            <h2 class="text-2xl font-black text-gray-900 leading-tight mb-2">
                                {{ store.product.name }}
                            </h2>
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-black text-blue-600">
                                    {{ formatPrice(currentPrice) }}
                                </span>
                                <span v-if="store.product.discount_price && !matchedVariant"
                                    class="text-sm text-gray-400 line-through">
                                    {{ formatPrice(store.product.price) }}
                                </span>
                            </div>
                        </div>

                        <!-- Variants Selection -->
                        <div v-if="attributes?.length > 0" class="space-y-6 mb-8 flex-grow">
                            <div v-for="attr in attributes" :key="attr.id" class="space-y-3">
                                <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-900">
                                    {{ attr.name }}
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <button v-for="val in attr.values" :key="val.id"
                                        @click="selectValue(attr.id, val.id)"
                                        class="px-4 py-2 text-xs font-bold transition-all border-2" :class="selectedValues[attr.id] === val.id
                                            ? 'bg-black text-white border-black'
                                            : 'bg-white text-gray-900 border-gray-100 hover:border-black'">
                                        {{ val.value }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-6">
                            <span v-if="outOfStock" class="text-xs font-bold text-red-500 uppercase tracking-widest">
                                {{ $t('messages.out_of_stock') }}
                            </span>
                            <span v-else class="text-xs font-bold text-green-600 uppercase tracking-widest">
                                {{ $t('messages.in_stock') }} ({{ currentStock }})
                            </span>
                        </div>

                        <!-- Quantity and CTA -->
                        <div class="mt-auto flex gap-4">
                            <div class="flex items-center border-2 border-gray-100">
                                <button @click="quantity > 1 ? quantity-- : null"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-gray-50">-</button>
                                <input type="number" v-model="quantity" class="w-12 text-center border-none font-bold"
                                    min="1">
                                <button @click="quantity < currentStock ? quantity++ : null"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-gray-50">+</button>
                            </div>
                            <button @click="addToCart" :disabled="outOfStock || store.adding"
                                class="flex-grow bg-black text-white px-8 py-3 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                                <i v-if="store.adding" class="fa fa-spinner fa-spin"></i>
                                {{ $t('messages.add_to_cart') }}
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { quickSelectionStore as store } from '@/Stores/quickSelectionStore.js';
import Swal from 'sweetalert2';

const activeImage = ref('');
const quantity = ref(1);
const selectedValues = ref({});

const attributes = computed(() => {
    if (!store.product || !store.product.variants) return [];

    const attrs = {};
    store.product.variants.forEach(variant => {
        variant.attribute_values.forEach(av => {
            if (!attrs[av.attribute_id]) {
                attrs[av.attribute_id] = {
                    id: av.attribute_id,
                    name: av.attribute.name,
                    values: []
                };
            }
            if (!attrs[av.attribute_id].values.find(v => v.id === av.id)) {
                attrs[av.attribute_id].values.push({
                    id: av.id,
                    value: av.value
                });
            }
        });
    });
    return Object.values(attrs);
});

const matchedVariant = computed(() => {
    if (!store.product || !store.product.variants) return null;
    if (Object.keys(selectedValues.value).length === 0) return null;

    return store.product.variants.find(variant => {
        return variant.attribute_values.every(av =>
            selectedValues.value[av.attribute_id] === av.id
        );
    });
});

const currentPrice = computed(() => {
    if (matchedVariant.value) return matchedVariant.value.price;
    return store.product?.discount_price || store.product?.price || 0;
});

const currentStock = computed(() => {
    if (matchedVariant.value) return matchedVariant.value.stock;
    return store.product?.stock || 0;
});

const outOfStock = computed(() => currentStock.value <= 0);

const selectValue = (attrId, valId) => {
    selectedValues.value[attrId] = valId;
};

const formatPrice = (price) => {
    return `${price} ${usePage().props.currentCurrency || 'EGP'}`;
};

watch(() => store.product, (newVal) => {
    if (newVal) {
        activeImage.value = newVal.image_url;
        quantity.value = 1;
        selectedValues.value = {};

        // Auto-select primary variant or first values
        if (newVal.variants?.length > 0) {
            const primary = newVal.variants.find(v => v.is_primary) || newVal.variants[0];
            primary.attribute_values.forEach(av => {
                selectedValues.value[av.attribute_id] = av.id;
            });
            if (primary.price) {
                // matchedVariant will catch it
            }
        }
    }
}, { immediate: true });

const addToCart = () => {
    if (outOfStock.value) return;

    store.adding = true;
    router.post(route('cart.add'), {
        product_id: store.product.id,
        variant_id: matchedVariant.value?.id,
        quantity: quantity.value
    }, {
        onSuccess: () => {
            store.close();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Added to cart',
                showConfirmButton: false,
                timer: 1500
            });
        },
        onFinish: () => {
            store.adding = false;
        }
    });
};
</script>
