<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-1">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" 
                class="px-3 py-1.5 text-sm text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg cursor-not-allowed select-none"
                v-html="formatLabel(link.label)" />
            <Link v-else
                :href="link.url"
                preserve-scroll
                preserve-state
                class="px-3 py-1.5 text-sm border rounded-lg transition-all duration-200 hover:scale-105 active:scale-95"
                :class="link.active 
                    ? 'bg-purple-600 text-white border-purple-600 shadow-md shadow-purple-500/20' 
                    : 'text-gray-700 bg-white border-gray-300 hover:bg-purple-50 hover:border-purple-300 hover:text-purple-600 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700'"
                v-html="formatLabel(link.label)" />
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true
    }
})

const formatLabel = (label) => {
    // Convert HTML entities and make arrows RTL friendly
    return label
        .replace(/&laquo;/g, '«')
        .replace(/&raquo;/g, '»')
}
</script>
