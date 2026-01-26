<!-- QUICK START: Vendor Settings Form Update -->
<!-- Add this to your Vendor/Settings/Edit.vue component -->

<template>
  <div class="settings-container">
    <!-- Existing store info section -->

    <!-- NEW: SEO Settings Section -->
    <div class="seo-settings-card">
      <h3>⚙️ إعدادات السيو</h3>

      <!-- Primary Language Selection -->
      <div class="form-group">
        <label for="primary_language">اللغة الرئيسية</label>
        <select
          v-model="form.primary_language"
          id="primary_language"
          class="form-control"
        >
          <option value="ar">العربية</option>
          <option value="en">الإنجليزية</option>
        </select>
        <small>اختر اللغة الأساسية لمتجرك</small>
      </div>

      <!-- Theme Color Picker -->
      <div class="form-group">
        <label for="theme_color_hex">لون الثيم</label>
        <input
          v-model="form.theme_color_hex"
          type="color"
          id="theme_color_hex"
          class="form-control color-picker"
          placeholder="#7F9CF5"
        />
        <small>اختر اللون الذي يعكس هوية متجرك</small>
      </div>

      <!-- Arabic SEO Fields -->
      <div class="seo-section">
        <h4>🌐 معلومات السيو - العربية</h4>

        <div class="form-group">
          <label for="seo_title_ar">عنوان السيو (عربي)</label>
          <input
            v-model="form.seo_title.ar"
            type="text"
            id="seo_title_ar"
            class="form-control"
            maxlength="60"
            placeholder="مثال: ساعات فاخرة أصلية | متجر الساعات"
          />
          <small>{{ form.seo_title.ar?.length || 0 }}/60 حرف</small>
        </div>

        <div class="form-group">
          <label for="seo_description_ar">وصف السيو (عربي)</label>
          <textarea
            v-model="form.seo_description.ar"
            id="seo_description_ar"
            class="form-control"
            rows="3"
            maxlength="160"
            placeholder="اكتشف أجمل الساعات الفاخرة مع ضمان عالمي..."
          ></textarea>
          <small>{{ form.seo_description.ar?.length || 0 }}/160 حرف</small>
        </div>

        <div class="form-group">
          <label for="seo_keywords_ar">الكلمات المفتاحية (عربي)</label>
          <textarea
            v-model="form.seo_keywords.ar"
            id="seo_keywords_ar"
            class="form-control"
            rows="2"
            placeholder="ساعات فاخرة, ساعات أصلية, ساعات سويسرية"
          ></textarea>
          <small>فصل الكلمات بفاصلة (,)</small>
        </div>
      </div>

      <!-- English SEO Fields -->
      <div class="seo-section">
        <h4>🌐 SEO Information - English</h4>

        <div class="form-group">
          <label for="seo_title_en">SEO Title (English)</label>
          <input
            v-model="form.seo_title.en"
            type="text"
            id="seo_title_en"
            class="form-control"
            maxlength="60"
            placeholder="Example: Luxury Watches | Official Store"
          />
          <small>{{ form.seo_title.en?.length || 0 }}/60 characters</small>
        </div>

        <div class="form-group">
          <label for="seo_description_en">SEO Description (English)</label>
          <textarea
            v-model="form.seo_description.en"
            id="seo_description_en"
            class="form-control"
            rows="3"
            maxlength="160"
            placeholder="Discover premium luxury watches with worldwide warranty..."
          ></textarea>
          <small>{{ form.seo_description.en?.length || 0 }}/160 characters</small>
        </div>

        <div class="form-group">
          <label for="seo_keywords_en">Keywords (English)</label>
          <textarea
            v-model="form.seo_keywords.en"
            id="seo_keywords_en"
            class="form-control"
            rows="2"
            placeholder="luxury watches, original watches, swiss watches"
          ></textarea>
          <small>Separate keywords with commas (,)</small>
        </div>
      </div>

      <!-- Business Information -->
      <div class="business-info-section">
        <h4>🏢 معلومات العمل</h4>

        <div class="form-group">
          <label for="street_address">العنوان</label>
          <input
            v-model="form.business_info.street_address"
            type="text"
            id="street_address"
            class="form-control"
            placeholder="شارع النيل, حي المرسى"
          />
        </div>

        <div class="form-group">
          <label for="city">المدينة</label>
          <input
            v-model="form.business_info.city"
            type="text"
            id="city"
            class="form-control"
            placeholder="الرياض"
          />
        </div>

        <div class="form-group">
          <label for="country">الدولة</label>
          <input
            v-model="form.business_info.country"
            type="text"
            id="country"
            class="form-control"
            placeholder="المملكة العربية السعودية"
          />
        </div>

        <div class="form-group">
          <label for="business_phone">رقم الهاتف</label>
          <input
            v-model="form.business_info.phone"
            type="tel"
            id="business_phone"
            class="form-control"
            placeholder="+966-1-1234-5678"
          />
        </div>

        <div class="form-group">
          <label for="business_email">البريد الإلكتروني</label>
          <input
            v-model="form.business_info.email"
            type="email"
            id="business_email"
            class="form-control"
            placeholder="contact@yourstore.com"
          />
        </div>
      </div>

      <!-- Save Button -->
      <button
        @click="submitForm"
        class="btn btn-primary"
        :disabled="form.processing"
      >
        {{ form.processing ? 'جاري الحفظ...' : 'حفظ الإعدادات' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  store: Object
})

const form = useForm({
  primary_language: props.store.primary_language || 'ar',
  theme_color_hex: props.store.theme_color_hex || '#7F9CF5',

  // SEO Fields
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

  // Business Info
  business_info: {
    street_address: props.store.business_info?.street_address || '',
    city: props.store.business_info?.city || '',
    country: props.store.business_info?.country || '',
    phone: props.store.business_info?.phone || '',
    email: props.store.business_info?.email || ''
  }
})

const submitForm = () => {
  form.post(route('vendor.settings.update'), {
    preserveScroll: true
  })
}
</script>

<style scoped>
.settings-container {
  max-width: 800px;
  margin: 0 auto;
}

.seo-settings-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-top: 24px;
}

.seo-settings-card h3 {
  color: #333;
  margin-bottom: 20px;
  font-size: 18px;
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 12px;
}

.seo-section,
.business-info-section {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #f0f0f0;
}

.seo-section h4,
.business-info-section h4 {
  color: #555;
  font-size: 16px;
  margin-bottom: 16px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #333;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
  font-size: 14px;
}

.form-control:focus {
  outline: none;
  border-color: #7F9CF5;
  box-shadow: 0 0 0 3px rgba(127, 156, 245, 0.1);
}

.color-picker {
  height: 40px;
  cursor: pointer;
}

small {
  display: block;
  margin-top: 4px;
  color: #999;
  font-size: 12px;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  margin-top: 24px;
  width: 100%;
}

.btn-primary {
  background-color: #7F9CF5;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #5a7cdb;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
