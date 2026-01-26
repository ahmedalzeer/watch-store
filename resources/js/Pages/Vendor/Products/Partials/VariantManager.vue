<template>
    <div class="mt-10 space-y-10 animate-in fade-in duration-700">
        <!-- Dashboard Header -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
            <div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-purple-600 rounded-full shadow-lg shadow-purple-500/20"></span>
                    {{ $t('messages.product_variants') }}
                </h3>
                <p class="text-xs text-gray-400 mt-2 uppercase tracking-widest font-black ltr:ml-5 rtl:mr-5">
                    {{ $t('messages.manage_options_and_inventory') }}
                </p>
            </div>

            <div v-if="variants.length > 0" class="flex items-center gap-3">
                <div class="hidden lg:flex flex-col items-end ltr:mr-4 rtl:ml-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $t('messages.status')
                        }}</p>
                    <p class="text-xs font-bold"
                        :class="isStockExceeded ? 'text-red-500' : (remainingStock === 0 ? 'text-green-500' : 'text-amber-500')">
                        {{ isStockExceeded ? $t('messages.allocation_warning') : (remainingStock === 0 ?
                            $t('messages.ready_to_save') : $t('messages.distributed')) }}
                    </p>
                </div>
                <button type="button" @click="addManualVariant"
                    class="group flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:border-purple-500 hover:text-purple-600 transition-all shadow-sm">
                    <i class="fa-solid fa-plus group-hover:rotate-90 transition-transform"></i>
                    {{ $t('messages.add_custom_row') }}
                </button>
            </div>
        </div>

        <!-- Inventory Intelligence Dashboard -->
        <div v-if="variants.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Overall Progress Card -->
            <div
                class="md:col-span-2 bg-white dark:bg-gray-900/40 p-6 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl flex items-center justify-between group overflow-hidden relative">
                <div class="absolute top-0 ltr:left-0 rtl:right-0 w-1.5 h-full"
                    :class="isStockExceeded ? 'bg-red-500' : (remainingStock === 0 ? 'bg-green-500' : 'bg-amber-500')">
                </div>

                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-3xl flex items-center justify-center text-purple-600 transition-transform duration-500"
                        :class="isStockExceeded ? 'bg-red-50 dark:bg-red-900/10 text-red-500' : (remainingStock === 0 ? 'bg-green-50 dark:bg-green-900/10 text-green-500' : 'bg-purple-50 dark:bg-purple-900/20 text-purple-600')">
                        <i class="fa-solid fa-boxes-packing text-2xl"></i>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-12">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{
                                $t('messages.stock') }}</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ productStock ||
                                0 }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{
                                $t('messages.distributed') }}</p>
                            <p class="text-2xl font-black tabular-nums"
                                :class="isStockExceeded ? 'text-red-500' : 'text-gray-900 dark:text-white'">{{
                                consumedStock }}</p>
                        </div>
                        <div class="hidden lg:block">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{
                                $t('messages.remaining_stock') }}</p>
                            <p class="text-2xl font-black tabular-nums"
                                :class="remainingStock < 0 ? 'text-red-500' : (remainingStock === 0 ? 'text-green-500' : 'text-amber-500')">
                                {{ remainingStock }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-3 min-w-[120px]">
                    <div
                        class="w-full h-2.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden shadow-inner flex">
                        <div class="h-full transition-all duration-1000 cubic-bezier(0.4, 0, 0.2, 1)"
                            :class="isStockExceeded ? 'bg-red-500' : 'bg-green-500'"
                            :style="{ width: `${Math.min((consumedStock / (parseInt(productStock) || 1)) * 100, 100)}%` }">
                        </div>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-tighter"
                        :class="isStockExceeded ? 'text-red-500' : 'text-gray-400'">
                        {{ Math.min(((consumedStock / (parseInt(productStock) || 1)) * 100), 100).toFixed(0) }}% {{
                            $t('messages.allocated') }}
                    </p>
                </div>
            </div>

            <!-- Total Variations Card -->
            <div
                class="bg-white dark:bg-gray-900/40 p-6 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl flex items-center gap-5">
                <div
                    class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-layer-group text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{
                        $t('messages.total_variants') }}</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums">{{ variants.length }}</p>
                </div>
            </div>
        </div>

        <!-- Shopify-Style Options Manager -->
        <div
            class="bg-white dark:bg-gray-900/20 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 space-y-8 shadow-2xl shadow-purple-500/[0.02]">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">{{
                        $t('messages.options') }}</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">{{
                        $t('messages.define_product_variations') }}</p>
                </div>
                <button type="button" @click="addOption"
                    class="text-[10px] font-black text-purple-600 hover:text-purple-700 uppercase tracking-widest flex items-center gap-2 px-5 py-2.5 bg-purple-50 dark:bg-purple-900/10 rounded-2xl transition-all hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-plus-circle"></i>
                    {{ $t('messages.add_another_option') }}
                </button>
            </div>

            <div class="space-y-6">
                <div v-for="(option, optIdx) in options" :key="optIdx"
                    class="group relative bg-gray-50/50 dark:bg-gray-800/30 p-8 rounded-[2rem] border-2 border-transparent hover:border-purple-200 dark:hover:border-purple-900/30 transition-all animate-in slide-in-from-left duration-500"
                    :style="{ animationDelay: `${optIdx * 100}ms` }">

                    <button @click="removeOption(optIdx)"
                        class="absolute -top-3 -right-3 w-10 h-10 bg-white dark:bg-gray-800 text-red-500 border border-red-50 dark:border-gray-700 rounded-2xl flex items-center justify-center shadow-xl opacity-0 group-hover:opacity-100 transition-all hover:scale-110 hover:bg-red-500 hover:text-white">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        <!-- Option Label/Search -->
                        <div class="lg:col-span-4 space-y-3">
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest ltr:ml-2 rtl:mr-2">{{
                                $t('messages.option_name') }}</label>
                            <div class="relative group/sel">
                                <select v-model="option.attributeId" @change="onAttributeChange(optIdx)"
                                    class="w-full bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-5 text-sm font-bold focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all appearance-none cursor-pointer">
                                    <option :value="null">{{ $t('messages.select_option_type') }}</option>
                                    <option v-for="attr in allAttributes" :key="attr.id" :value="attr.id">
                                        {{ attr.name?.[$page.props.locale] || attr.name?.en }}
                                    </option>
                                    <option value="custom" class="text-purple-600 font-bold">+ {{
                                        $t('messages.custom_property') }}</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 ltr:right-5 rtl:left-5 flex items-center pointer-events-none text-gray-300 group-hover/sel:text-purple-500 transition-colors">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Values Tag Input -->
                        <div class="lg:col-span-8 space-y-3">
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest ltr:ml-2 rtl:mr-2">{{
                                    $t('messages.option_values') }}</label>
                            <div
                                class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl p-2.5 min-h-[56px] flex flex-wrap gap-2.5 focus-within:ring-4 focus-within:ring-purple-500/10 focus-within:border-purple-500 transition-all">
                                <div v-for="(val, valIdx) in option.values" :key="valIdx"
                                    class="flex items-center gap-2.5 bg-purple-50 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 px-4 py-2 rounded-xl text-xs font-black uppercase border border-purple-100/50 dark:border-purple-800/50 animate-in zoom-in duration-300 group/tag">
                                    {{ val.name }}
                                    <button @click="removeValue(optIdx, valIdx)"
                                        class="hover:text-red-500 transition-colors">
                                        <i class="fa-solid fa-circle-xmark opacity-50 group-hover/tag:opacity-100"></i>
                                    </button>
                                </div>
                                <input type="text" :placeholder="$t('messages.type_and_hit_enter')"
                                    class="flex-grow bg-transparent border-none focus:ring-0 text-sm py-2 px-3 font-bold min-w-[200px]"
                                    @keydown.enter.prevent="addValueFromInput($event, optIdx)"
                                    @keydown.backspace="onBackspace(optIdx, $event)" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Intelligence Bar -->
        <div v-if="variants.length > 0"
            class="sticky top-6 z-20 bg-gray-900/95 backdrop-blur-md text-white p-5 rounded-[2rem] shadow-2xl flex flex-wrap items-center justify-between gap-6 border border-white/10 animate-in slide-in-from-bottom duration-500">
            <div class="flex items-center gap-5">
                <div
                    class="w-12 h-12 rounded-2xl bg-purple-600 flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/40">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-purple-300 tracking-widest">{{
                        $t('messages.bulk_editor') }}</p>
                    <p class="text-xs font-bold">{{ $t('messages.apply_to_all_variants') }}</p>
                </div>
            </div>

            <div class="flex items-center flex-wrap gap-4">
                <div
                    class="flex items-center bg-white/5 rounded-2xl px-4 py-1.5 border border-white/10 group focus-within:border-purple-500/50 transition-all">
                    <span class="text-[11px] font-black text-gray-500 uppercase mr-3">$</span>
                    <input v-model="bulkPrice" type="number" step="0.01" placeholder="Price"
                        class="w-24 bg-transparent border-none focus:ring-0 text-xs font-bold text-white p-1" />
                    <button @click="applyBulk('price')"
                        class="ml-3 px-4 py-1.5 bg-white text-gray-900 rounded-xl text-[10px] font-black uppercase hover:bg-purple-100 transition-all active:scale-95">{{
                            $t('messages.apply') }}</button>
                </div>
                <div
                    class="flex items-center bg-white/5 rounded-2xl px-4 py-1.5 border border-white/10 group focus-within:border-purple-500/50 transition-all">
                    <i class="fa-solid fa-box text-[10px] text-gray-500 mr-3"></i>
                    <input v-model="bulkStock" type="number" placeholder="Stock"
                        class="w-20 bg-transparent border-none focus:ring-0 text-xs font-bold text-white p-1" />
                    <button @click="applyBulk('stock')"
                        class="ml-3 px-4 py-1.5 bg-white text-gray-900 rounded-xl text-[10px] font-black uppercase hover:bg-purple-100 transition-all active:scale-95">{{
                            $t('messages.apply') }}</button>
                </div>
                <div class="h-8 w-px bg-white/10 mx-2 hidden sm:block"></div>
                <!-- Precision Distribution Tool -->
                <button @click="autoDistribute"
                    class="px-5 py-2.5 bg-purple-600 text-white rounded-2xl text-[10px] font-black uppercase hover:bg-purple-500 transition-all active:scale-95 flex items-center gap-2 shadow-lg shadow-purple-900/20">
                    <i class="fa-solid fa-calculator"></i>
                    {{ $t('messages.auto_distribute') }}
                </button>
            </div>
        </div>

        <!-- Variants Master Table -->
        <div v-if="variants.length > 0"
            class="group relative bg-white dark:bg-gray-900/40 border-2 border-gray-100 dark:border-gray-800 rounded-[3rem] shadow-2xl overflow-hidden transition-all duration-500">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-xs text-start border-separate border-spacing-0">
                    <thead>
                        <tr
                            class="bg-gray-50/50 dark:bg-gray-950/50 text-gray-400 uppercase font-black tracking-widest text-[9px]">
                            <th class="px-10 py-6 text-start">{{ $t('messages.variant') }}</th>
                            <th class="px-10 py-6 text-start">{{ $t('messages.sku') }}</th>
                            <th class="px-10 py-6 text-start">{{ $t('messages.price') }}</th>
                            <th class="px-10 py-6 text-start">{{ $t('messages.inventory') }}</th>
                            <th class="px-10 py-6 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        <tr v-for="(v, idx) in variants" :key="v.key || idx"
                            class="group/row hover:bg-purple-50/20 dark:hover:bg-purple-900/5 transition-all animate-in fade-in slide-in-from-bottom duration-500"
                            :style="{ animationDelay: `${Math.min(idx * 40, 400)}ms` }">

                            <td class="px-10 py-7">
                                <div class="flex flex-wrap gap-2.5">
                                    <span v-for="vName in v.valueNames" :key="vName"
                                        class="px-3.5 py-1.5 rounded-xl bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 font-black text-[10px] uppercase shadow-sm group-hover/row:border-purple-300 group-hover/row:text-purple-600 transition-all">
                                        {{ vName }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-10 py-7">
                                <input v-model="v.sku" type="text"
                                    class="w-44 bg-gray-50 dark:bg-gray-950 border-transparent rounded-2xl px-5 py-3 text-[10px] font-bold focus:ring-4 focus:ring-purple-500/10 focus:bg-white dark:focus:bg-gray-900 transition-all"
                                    placeholder="SKU-XYZ" />
                            </td>

                            <td class="px-10 py-7">
                                <div class="relative w-36 group/price hide-arrows">
                                    <span
                                        class="absolute ltr:left-5 rtl:right-5 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 font-black">$</span>
                                    <input v-model="v.price" type="number" step="0.01"
                                        class="w-full ltr:pl-10 rtl:pr-10 bg-gray-50 dark:bg-gray-950 border-transparent rounded-2xl px-5 py-3 text-[11px] font-black focus:ring-4 focus:ring-purple-500/10 focus:bg-white dark:focus:bg-gray-900 transition-all ltr:text-left rtl:text-right" />
                                </div>
                            </td>

                            <td class="px-10 py-7">
                                <div class="flex items-center gap-5">
                                    <div class="relative w-28">
                                        <input v-model.number="v.stock" type="number"
                                            class="w-full border-2 rounded-2xl px-5 py-3 text-[12px] font-black transition-all text-center"
                                            :class="isIndividualStockInvalid(v.stock) ? 'border-red-200 bg-red-50 text-red-600 focus:ring-red-500/20' : 'bg-gray-50 dark:bg-gray-950 border-transparent focus:ring-purple-500/10 focus:bg-white dark:focus:bg-gray-900'" />
                                    </div>
                                    <div class="h-10 w-[1.5px] bg-gray-100 dark:bg-gray-800"></div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black uppercase tracking-wider"
                                            :class="getStockLevelClass(v.stock)">{{ getStockLevelLabel(v.stock)
                                            }}</span>
                                        <span
                                            class="text-[8px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-0.5">{{
                                            $t('messages.units') }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-10 py-7 text-center">
                                <button @click="removeVariant(idx)"
                                    class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-950/20 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm mx-auto group/del">
                                    <i class="fa-solid fa-trash-can group-hover/del:scale-110 transition-transform"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Advanced Property Creation Modal -->
        <div v-if="showCustomAttrModal"
            class="fixed inset-0 z-[60] flex items-center justify-center p-6 bg-gray-900/70 backdrop-blur-md animate-in fade-in duration-300">
            <div
                class="bg-white dark:bg-gray-900 rounded-[3rem] w-full max-w-lg p-10 shadow-full border border-white/10 space-y-8 animate-in zoom-in-95 duration-300">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600">
                        <i class="fa-solid fa-vial-circle-check text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">New
                            Custom Property</h4>
                        <p class="text-xs text-gray-400 font-bold uppercase mt-1">Create a unique attribute for your
                            products</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2 tracking-widest">Property Name
                            (English)</label>
                        <input v-model="newAttr.name.en" type="text" placeholder="e.g. Dial Finish"
                            class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent focus:border-purple-500 focus:bg-white dark:focus:bg-gray-900 px-5 py-4 rounded-2xl text-sm font-bold transition-all" />
                    </div>
                    <div class="space-y-2 text-end">
                        <label class="text-[10px] font-black text-gray-400 uppercase mr-2 tracking-widest">الاسم
                            (بالعربي)</label>
                        <input v-model="newAttr.name.ar" dir="rtl" type="text" placeholder="مثال: نوع المينا"
                            class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent focus:border-purple-500 focus:bg-white dark:focus:bg-gray-900 px-5 py-4 rounded-2xl text-sm font-bold text-end transition-all" />
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button @click="showCustomAttrModal = false"
                        class="flex-grow py-4 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-gray-700 transition-colors">Cancel</button>
                    <button @click="confirmCustomAttribute" :disabled="!newAttr.name.en || !newAttr.name.ar"
                        class="flex-grow py-4 bg-purple-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-purple-500/30 hover:bg-purple-700 active:scale-95 transition-all disabled:opacity-50">Create
                        Property</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: Array,
    storeId: [Number, String],
    initialAttributes: Array,
    basePrice: [Number, String],
    baseSku: String,
    productStock: [Number, String],
});

const emit = defineEmits(['update:modelValue']);

// Data State
const allAttributes = ref([]);
const options = ref([]);
const variants = ref(props.modelValue || []);
const bulkPrice = ref(null);
const bulkStock = ref(null);

// UI State
const showCustomAttrModal = ref(false);
const newAttr = ref({ name: { en: '', ar: '' } });
const currentCustomOptIdx = ref(null);

// Computed Intelligence
const consumedStock = computed(() => {
    return variants.value.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
});

const remainingStock = computed(() => {
    const total = parseInt(props.productStock) || 0;
    return total - consumedStock.value;
});

const isStockExceeded = computed(() => {
    return remainingStock.value < 0;
});

// Reactivity: Auto-Generation with Data Preservation
const generateVariantsFromOptions = () => {
    const activeOptions = options.value.filter(opt => opt.attributeId && opt.values.length > 0);

    if (activeOptions.length === 0) {
        // If they cleared all options but had variants, we might want to keep manual ones
        // but typically Shopify clears. Let's keep manual ones (without attribute ids)
        variants.value = variants.value.filter(v => v.attribute_value_ids.length === 0);
        return;
    }

    const selectedData = activeOptions.map(opt => {
        return opt.values.map(val => ({
            id: val.id,
            name: val.name,
            optId: opt.attributeId
        }));
    });

    const combinations = cartesianProduct(selectedData);
    const newVariants = combinations.map((combo, index) => {
        const valueNames = combo.map(c => c.name);
        const attribute_value_ids = combo.map(c => c.id).filter(id => id !== null);

        // Key for stable data matching
        const key = valueNames.sort().join('|');

        // Preserve data if existing
        const existing = variants.value.find(v => {
            const existingKey = v.valueNames ? [...v.valueNames].sort().join('|') : '';
            return existingKey === key;
        });

        if (existing) return { ...existing, key };

        return {
            key,
            attribute_value_ids,
            valueNames,
            sku: `${props.baseSku || 'SKU'}-${variants.value.length + index + 1}`,
            price: props.basePrice || 0,
            stock: 0,
            is_new: true
        };
    });

    // Merge manual variants (those with no attribute ids) with auto-generated ones
    const manualVariants = variants.value.filter(v => v.attribute_value_ids.length === 0);
    variants.value = [...newVariants, ...manualVariants];
};

// Advanced: Equal Distribution Logic
const autoDistribute = () => {
    const total = parseInt(props.productStock) || 0;
    const count = variants.value.length;
    if (count === 0 || total === 0) return;

    const equalBase = Math.floor(total / count);
    const remainder = total % count;

    variants.value.forEach((v, idx) => {
        v.stock = equalBase + (idx < remainder ? 1 : 0);
    });
};

// Option Management
const addOption = () => {
    if (options.value.length >= 3) {
        alert("Maximum 3 levels of variants supported for best performance.");
        return;
    }
    options.value.push({ attributeId: null, values: [] });
};

const removeOption = (idx) => {
    options.value.splice(idx, 1);
    generateVariantsFromOptions();
};

const onAttributeChange = (idx) => {
    if (options.value[idx].attributeId === 'custom') {
        currentCustomOptIdx.value = idx;
        newAttr.value = { name: { en: '', ar: '' } };
        showCustomAttrModal.value = true;
    } else {
        options.value[idx].values = [];
        generateVariantsFromOptions();
    }
};

const confirmCustomAttribute = async () => {
    try {
        const response = await axios.post(route('vendor.attributes.store', { store_id: props.storeId }), newAttr.value);
        allAttributes.value.push(response.data);
        options.value[currentCustomOptIdx.value].attributeId = response.data.id;
        showCustomAttrModal.value = false;
    } catch (e) { console.error("Attr Creation Failed"); }
};

const addValueFromInput = async (e, optIdx) => {
    const input = e.target;
    const value = input.value.trim();
    if (!value) return;

    const opt = options.value[optIdx];
    if (!opt.attributeId) return;

    try {
        const response = await axios.post(route('vendor.attribute-values.store'), {
            attribute_id: opt.attributeId,
            value: { en: value, ar: value }
        });
        opt.values.push({ id: response.data.id, name: value });
        input.value = '';
        generateVariantsFromOptions();
    } catch (err) { console.error("Value Sync Failed"); }
};

const removeValue = (optIdx, valIdx) => {
    options.value[optIdx].values.splice(valIdx, 1);
    generateVariantsFromOptions();
};

const onBackspace = (optIdx, e) => {
    if (e.target.value === '' && options.value[optIdx].values.length > 0) {
        options.value[optIdx].values.pop();
        generateVariantsFromOptions();
    }
};

// Variant Management
const addManualVariant = () => {
    variants.value.push({
        attribute_value_ids: [],
        valueNames: ['Custom'],
        sku: `${props.baseSku || 'SKU'}-${variants.value.length + 1}`,
        price: props.basePrice || 0,
        stock: 0,
        key: `manual-${Date.now()}`
    });
};

const removeVariant = (idx) => {
    variants.value.splice(idx, 1);
};

// Bulk Actions
const applyBulk = (field) => {
    const val = field === 'price' ? bulkPrice.value : bulkStock.value;
    if (val === null || val === undefined) return;

    variants.value.forEach(v => { v[field] = val; });

    if (field === 'price') bulkPrice.value = null;
    if (field === 'stock') bulkStock.value = null;
};

// Visual Helpers
const isIndividualStockInvalid = (stock) => {
    return isStockExceeded.value && stock > 0;
};

const getStockLevelClass = (stock) => {
    if (stock <= 2) return 'text-red-600 bg-red-50 px-2 py-0.5 rounded-md';
    if (stock <= 10) return 'text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md';
    return 'text-green-600 bg-green-50 px-2 py-0.5 rounded-md';
};

const getStockLevelLabel = (stock) => {
    if (stock <= 2) return 'Critical';
    if (stock <= 10) return 'Limited';
    return 'Stable';
};

const cartesianProduct = (arrays) => {
    if (arrays.length === 0) return [];
    return arrays.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
};

const fetchAttributes = async () => {
    try {
        const response = await axios.get(route('vendor.attributes.index', { store_id: props.storeId }));
        allAttributes.value = response.data.attributes;
        reconstructOptions();
    } catch (error) { console.error('Failed to fetch attributes'); }
};

const reconstructOptions = () => {
    if (variants.value.length === 0 || allAttributes.value.length === 0 || options.value.length > 0) return;

    const valIds = [...new Set(variants.value.flatMap(v => v.attribute_value_ids))];
    const newOptions = [];

    allAttributes.value.forEach(attr => {
        const activeVals = attr.values.filter(v => valIds.includes(v.id));
        if (activeVals.length > 0) {
            newOptions.push({
                attributeId: attr.id,
                values: activeVals.map(v => ({
                    id: v.id,
                    name: v.value[usePage().props.locale] || v.value.en
                }))
            });
        }
    });

    if (newOptions.length > 0) options.value = newOptions;
};

// Lifecycle & Sync
onMounted(() => {
    fetchAttributes();
});

watch(variants, (newVal) => {
    emit('update:modelValue', newVal);
}, { deep: true });

watch(() => props.modelValue, (newVal) => {
    if (newVal && newVal.length > 0 && variants.value.length === 0) {
        variants.value = JSON.parse(JSON.stringify(newVal));
        reconstructOptions();
    }
}, { immediate: true, deep: true });

</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-gray-200 dark:bg-gray-800 rounded-full border-4 border-white dark:border-gray-900 hover:bg-purple-200 transition-colors;
}

.hide-arrows input::-webkit-outer-spin-button,
.hide-arrows input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.hide-arrows input[type=number] {
    -moz-appearance: textfield;
}

.shadow-full {
    shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.cubic-bezier {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
