<template>
    <div v-if="canAccess" class="w-full">
        <div ref="dropzoneElement"
            class="dropzone border-2 border-dashed border-purple-500 rounded-lg bg-gray-50 dark:bg-gray-700">
            <div class="dz-message">
                <p class="text-gray-500 dark:text-gray-300">{{ $t('messages.drop_files_here') }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';

const props = defineProps({
    url: String,
    headers: Object,
    requiredPermission: { type: String, default: null }
});

const emit = defineEmits(['success', 'error']);
const dropzoneElement = ref(null);
const page = usePage();

const canAccess = computed(() => {
    const auth = page.props.auth;
    // If user is Admin OR if no permission required OR if user has permission
    return auth.is_admin || !props.requiredPermission || (auth.permissions && auth.permissions.includes(props.requiredPermission));
});

onMounted(() => {
    if (dropzoneElement.value && canAccess.value) {
        new Dropzone(dropzoneElement.value, {
            url: props.url,
            headers: props.headers,
            maxFilesize: 2,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
        }).on('success', (file, response) => emit('success', { file, response }))
            .on('error', (file, message) => emit('error', { file, message }));
    }
});
</script>
