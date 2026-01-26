<!-- SEO Meta Tags Component -->
<template>
  <Head>
    <!-- Basic Meta Tags -->
    <title>{{ seoData.title }}</title>
    <meta name="description" :content="seoData.description" />
    <meta name="keywords" :content="seoData.keywords" />
    <meta name="robots" :content="robotsMeta" />
    <meta name="language" :content="primaryLanguage === 'ar' ? 'Arabic' : 'English'" />
    <meta :lang="primaryLanguage" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Canonical URL -->
    <link rel="canonical" :href="seoData.canonical_url" />

    <!-- Theme Color -->
    <meta name="theme-color" :content="themeColor || '#7F9CF5'" />

    <!-- Open Graph (OG) Tags for Social Media -->
    <meta property="og:type" content="website" />
    <meta property="og:title" :content="seoData.og_title" />
    <meta property="og:description" :content="seoData.og_description" />
    <meta property="og:image" :content="seoData.og_image" />
    <meta property="og:url" :content="seoData.og_url" />
    <meta property="og:site_name" :content="currentStore.name" />
    <meta property="og:locale" :content="primaryLanguage === 'ar' ? 'ar_SA' : 'en_US'" />

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="seoData.title" />
    <meta name="twitter:description" :content="seoData.description" />
    <meta name="twitter:image" :content="seoData.og_image" />

    <!-- Language Alternatives -->
    <link v-if="primaryLanguage !== 'ar'" rel="alternate" hreflang="ar" :href="getAlternateUrl('ar')" />
    <link v-if="primaryLanguage !== 'en'" rel="alternate" hreflang="en" :href="getAlternateUrl('en')" />
    <link rel="alternate" :hreflang="`${primaryLanguage}`" :href="seoData.canonical_url" />

    <!-- Favicon -->
    <link rel="icon" :href="currentStore.logo_url" type="image/png" />
    <link rel="shortcut icon" :href="currentStore.logo_url" type="image/png" />

    <!-- Additional Meta Tags -->
    <meta name="author" :content="currentStore.contact_email" />
    <meta property="article:author" :content="currentStore.contact_email" />
    <meta name="copyright" :content="`© ${new Date().getFullYear()} ${currentStore.name}`" />
  </Head>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  seoData: {
    type: Object,
    required: true,
    default: () => ({
      title: '',
      description: '',
      keywords: '',
      og_title: '',
      og_description: '',
      og_image: '',
      og_url: '',
      canonical_url: ''
    })
  },
  schemaData: {
    type: Object,
    required: true
  },
  currentStore: {
    type: Object,
    required: true
  },
  primaryLanguage: {
    type: String,
    default: 'ar'
  },
  themeColor: {
    type: String,
    default: null
  }
})

// Inject structured data into head
watch(
  () => props.schemaData,
  (newSchema) => {
    if (newSchema && Object.keys(newSchema).length > 0) {
      // Remove existing schema script if present
      const existingScript = document.querySelector('script[type="application/ld+json"]')
      if (existingScript) {
        existingScript.remove()
      }

      // Create and inject new schema script
      const script = document.createElement('script')
      script.type = 'application/ld+json'
      script.innerHTML = JSON.stringify(newSchema)
      document.head.appendChild(script)
    }
  },
  { immediate: true, deep: true }
)

const robotsMeta = computed(() => {
  return props.currentStore.is_active ? 'index, follow' : 'noindex, nofollow'
})

const getAlternateUrl = (locale) => {
  const currentUrl = props.seoData.canonical_url
  return currentUrl.replace(`/${props.primaryLanguage}/`, `/${locale}/`)
}
</script>
