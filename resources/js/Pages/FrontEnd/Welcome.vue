<template>

    <Head :title="$t('messages.home')" />

    <Navbar />

    <div :dir="$page.props.locale === 'ar' ? 'rtl' : 'ltr'" :class="[
        $page.props.locale === 'ar' ? 'font-arabic' : 'font-poppins',
        'bg-white dark:bg-gray-950 min-h-screen transition-all duration-300'
    ]">
        <HeroSection :banners="heroBanners" />

        <ProductSliderSection id="best-sellers" :title="$t('messages.best_sellers')"
            :subtitle="$t('messages.trending_now')" :products="bestSellers"
            customClass="bg-gray-50/50 dark:bg-gray-900/60 py-10" />

        <template v-for="(section, index) in dynamicSections" :key="section.id">
            <ProductSliderSection :id="section.id" :title="section.title[$page.props.locale] || section.title['en']"
                :subtitle="$t('messages.explore_category')" :products="section.products"
                :customClass="index % 2 === 0 ? 'bg-white dark:bg-black py-10' : 'bg-gray-50/50 dark:bg-gray-900/60 py-10'" />
        </template>

        <FooterSection />
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Pages/FrontEnd/Partials/Navbar.vue';
import HeroSection from '@/Pages/FrontEnd/Partials/HeroSection.vue';
import ProductSliderSection from '@/Pages/FrontEnd/Partials/ProductSliderSection.vue';
import FooterSection from '@/Pages/FrontEnd/Partials/FooterSection.vue';

// تعريف الـ Props القادمة من المتحكم (Controller) في Laravel 12
defineProps({
    heroBanners: Array,
    bestSellers: Array,
    dynamicSections: Array
});
</script>

<style scoped>
/* إضافة الخطوط لدعم الـ RTL و LTR بشكل احترافي
   تأكد من استدعاء هذه الخطوط في ملف app.blade.php أو CSS العام
*/
.font-poppins {
    font-family: 'Poppins', sans-serif;
}

.font-arabic {
    font-family: 'Cairo', 'Tajawal', sans-serif;
}

/* تحسين شكل السلايدر ليتناسب مع تصميم Velstore */
:deep(.product-slider-container) {
    padding: 20px 0;
}

/* ضمان أن الاتجاه يؤثر على الهوامش تلقائياً */
[dir="rtl"] .text-start {
    text-align: right !important;
}

[dir="ltr"] .text-start {
    text-align: left !important;
}
</style>
