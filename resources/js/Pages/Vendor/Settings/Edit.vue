<template>

    <Head :title="$t('messages.settings')" />

    <VendorLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $t('messages.settings') }}
                </h2>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Settings Form -->
            <div class="lg:col-span-2 space-y-6">
                <form @submit.prevent="submit">
                    <!-- Store Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm mb-6">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $t('messages.store_details') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Store Name -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.store_name') }} (AR)</label>
                                    <input v-model="form.name.ar" type="text" class="form-input" required />
                                    <InputError :message="form.errors['name.ar']" />
                                </div>
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.store_name') }} (EN)</label>
                                    <input v-model="form.name.en" type="text" class="form-input" required />
                                    <InputError :message="form.errors['name.en']" />
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.description') }} (AR)</label>
                                    <textarea v-model="form.description.ar" rows="3" class="form-input"></textarea>
                                    <InputError :message="form.errors['description.ar']" />
                                </div>
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.description') }} (EN)</label>
                                    <textarea v-model="form.description.en" rows="3" class="form-input"></textarea>
                                    <InputError :message="form.errors['description.en']" />
                                </div>
                            </div>

                            <!-- Subdomain & Currency -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.subdomain') }}</label>
                                    <div class="flex items-center">
                                        <input v-model="form.subdomain" type="text" class="form-input rounded-r-none" required />
                                        <span class="px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-500">
                                            .watchstore.com
                                        </span>
                                    </div>
                                    <InputError :message="form.errors.subdomain" />
                                </div>
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.currency') }}</label>
                                    <select v-model="form.currency_id" class="form-input" required>
                                        <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                                            {{ currency.name?.[$page.props.locale] || currency.name?.en }} ({{ currency.symbol }})
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.currency_id" />
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.contact_email') }}</label>
                                    <input v-model="form.contact_email" type="email" class="form-input" />
                                    <InputError :message="form.errors.contact_email" />
                                </div>
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.phone') }}</label>
                                    <input v-model="form.contact_phone" type="tel" class="form-input" />
                                    <InputError :message="form.errors.contact_phone" />
                                </div>
                            </div>

                            <!-- Theme Color -->
                            <div class="space-y-2">
                                <label class="label-dark">{{ $t('messages.theme_color') }}</label>
                                <div class="flex items-center gap-3">
                                    <input v-model="form.theme_color" type="color" class="w-12 h-10 rounded-lg cursor-pointer border border-gray-300 dark:border-gray-600" />
                                    <input v-model="form.theme_color" type="text" class="form-input w-32 font-mono" placeholder="#7e3af2" />
                                </div>
                                <InputError :message="form.errors.theme_color" />
                            </div>

                            <!-- Status -->
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="form.is_active" class="checkbox-purple">
                                    <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ $t('messages.is_active') }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm mb-6">
                        <div class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900 dark:to-blue-900">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                🔍 {{ $t('messages.seo_settings') }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $t('messages.optimize_your_store_visibility_in_search_engines') || 'تحسين ظهور متجرك في محركات البحث' }}</p>
                        </div>
                        <div class="p-6 space-y-6">

                            <!-- Primary Language & Business Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.primary_language') }}</label>
                                    <select v-model="form.primary_language" class="form-input">
                                        <option value="ar">{{ $t('messages.language_name') || 'العربية' }}</option>
                                        <option value="en">English</option>
                                    </select>
                                    <InputError :message="form.errors.primary_language" />
                                </div>
                            </div>

                            <!-- SEO Title (Localized) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.seo_title') }} (AR)</label>
                                    <input v-model="form.seo_title.ar" type="text" class="form-input"
                                        maxlength="60" :placeholder="$t('messages.seo_title')" />
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ (form.seo_title.ar || '').length }}/60 {{ $t('messages.characters') || 'أحرف' }}
                                    </p>
                                    <InputError :message="form.errors['seo_title.ar']" />
                                </div>
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.seo_title') }} (EN)</label>
                                    <input v-model="form.seo_title.en" type="text" class="form-input"
                                        maxlength="60" :placeholder="$t('messages.seo_title')" />
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ (form.seo_title.en || '').length }}/60 {{ $t('messages.characters') || 'أحرف' }}
                                    </p>
                                    <InputError :message="form.errors['seo_title.en']" />
                                </div>
                            </div>

                            <!-- SEO Description (Localized) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.seo_description') }} (AR)</label>
                                    <textarea v-model="form.seo_description.ar" rows="3" class="form-input"
                                        maxlength="160" :placeholder="$t('messages.seo_description')"></textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ (form.seo_description.ar || '').length }}/160 {{ $t('messages.characters') || 'أحرف' }}
                                    </p>
                                    <InputError :message="form.errors['seo_description.ar']" />
                                </div>
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.seo_description') }} (EN)</label>
                                    <textarea v-model="form.seo_description.en" rows="3" class="form-input"
                                        maxlength="160" :placeholder="$t('messages.seo_description')"></textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ (form.seo_description.en || '').length }}/160 {{ $t('messages.characters') || 'أحرف' }}
                                    </p>
                                    <InputError :message="form.errors['seo_description.en']" />
                                </div>
                            </div>

                            <!-- SEO Keywords (Localized) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.seo_keywords') }} (AR)</label>
                                    <textarea v-model="form.seo_keywords.ar" rows="2" class="form-input"
                                        :placeholder="$t('messages.keywords_comma_separated') || 'الكلمات المفتاحية، مفصولة، بفواصل'"></textarea>
                                    <InputError :message="form.errors['seo_keywords.ar']" />
                                </div>
                                <div class="space-y-2">
                                    <label class="label-dark">{{ $t('messages.seo_keywords') }} (EN)</label>
                                    <textarea v-model="form.seo_keywords.en" rows="2" class="form-input"
                                        :placeholder="$t('messages.keywords_comma_separated') || 'Keywords, separated, by, commas'"></textarea>
                                    <InputError :message="form.errors['seo_keywords.en']" />
                                </div>
                            </div>

                            <!-- Business Information -->
                            <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
                                <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-4">{{ $t('messages.business_info') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="label-dark">{{ $t('messages.street_address') }}</label>
                                        <input v-model="form.business_info.street_address" type="text" class="form-input" />
                                        <InputError :message="form.errors['business_info.street_address']" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="label-dark">{{ $t('messages.city') }}</label>
                                        <input v-model="form.business_info.city" type="text" class="form-input" />
                                        <InputError :message="form.errors['business_info.city']" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="label-dark">{{ $t('messages.country') }}</label>
                                        <input v-model="form.business_info.country" type="text" class="form-input" />
                                        <InputError :message="form.errors['business_info.country']" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="label-dark">{{ $t('messages.contact_phone') }}</label>
                                        <input v-model="form.business_info.phone" type="tel" class="form-input" />
                                        <InputError :message="form.errors['business_info.phone']" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing"
                            class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold shadow-md transition active:scale-95 disabled:opacity-50">
                            {{ $t('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Store Logo -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $t('messages.store_logo') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600 mb-4">
                                <img v-if="store.logo_url" :src="store.logo_url" class="w-full h-full object-cover" />
                                <span v-else class="text-4xl font-bold text-gray-400">
                                    {{ store.name?.[$page.props.locale]?.charAt(0) || 'S' }}
                                </span>
                            </div>
                            <Dropzone :url="route('vendor.media.upload.temp')"
                                :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }"
                                @success="handleLogoUpload"
                                class="w-full" />
                        </div>
                    </div>
                </div>

                <!-- Store Preview -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $t('messages.store') }} Preview
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="rounded-lg overflow-hidden border dark:border-gray-600">
                            <div class="h-16 flex items-center px-4" :style="{ backgroundColor: form.theme_color }">
                                <span class="text-white font-bold">{{ form.name[$page.props.locale] || 'Store Name' }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ form.description[$page.props.locale] || 'Store description...' }}
                                </p>
                            </div>
                        </div>
                        <a :href="`https://${form.subdomain}.watchstore.com`" target="_blank"
                            class="mt-4 block text-center text-sm text-purple-600 hover:underline">
                            {{ form.subdomain }}.watchstore.com
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </VendorLayout>
</template>

<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import Dropzone from '@/Components/Dropzone.vue';
import InputError from '@/Components/InputError.vue';
import { useAlert } from '@/useAlert';

const props = defineProps({
    store: Object,
    currencies: Array,
    storeId: [Number, String]
});

const { success } = useAlert();
const page = usePage();

const form = useForm({
    name: {
        ar: props.store.name?.ar || '',
        en: props.store.name?.en || ''
    },
    description: {
        ar: props.store.description?.ar || '',
        en: props.store.description?.en || ''
    },
    subdomain: props.store.subdomain || '',
    currency_id: props.store.currency_id || '',
    theme_color: props.store.theme_color || '#7e3af2',
    contact_email: props.store.contact_email || '',
    contact_phone: props.store.contact_phone || '',
    primary_language: props.store.primary_language || 'ar',
    seo_title: {
        ar: props.store.seo_title?.ar || '',
        en: props.store.seo_title?.en || ''
    },
    seo_description: {
        ar: props.store.seo_description?.ar || '',
        en: props.store.seo_description?.en || ''
    },
    seo_keywords: {
        ar: props.store.seo_keywords?.ar || '',
        en: props.store.seo_keywords?.en || ''
    },
    business_info: {
        street_address: props.store.business_info?.street_address || '',
        city: props.store.business_info?.city || '',
        country: props.store.business_info?.country || 'SA',
        phone: props.store.business_info?.phone || props.store.contact_phone || '',
        email: props.store.business_info?.email || props.store.contact_email || ''
    },
    is_active: props.store.is_active ?? true,
    logo_path: null,
});

const handleLogoUpload = (response) => {
    form.logo_path = response.response.path;
};

const submit = () => {
    form.post(route('vendor.settings.update', { store_id: props.storeId }), {
        onSuccess: () => success('messages.store_updated')
    });
};
</script>

<style scoped>
.label-dark {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-400;
}

.form-input {
    @apply w-full mt-1 block text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 transition;
}

.checkbox-purple {
    @apply w-5 h-5 text-purple-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-purple-500;
}
</style>
