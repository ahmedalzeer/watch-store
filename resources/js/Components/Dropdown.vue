<template>
  <div v-if="canAccess">
    <div ref="dropzoneElement" class="dropzone border-2 border-dashed border-purple-500 rounded-lg bg-gray-50 dark:bg-gray-700 transition-all hover:bg-purple-50">
      <div class="dz-message">
        <p class="text-gray-500 dark:text-gray-300">{{ $t('messages.drop_files_here') }}</p>
      </div>
    </div>
  </div>
  <div v-else class="p-4 bg-red-100 text-red-600 rounded-lg text-sm border border-red-200">
    {{ $t('messages.unauthorized_upload') }}
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

// Logic: Allow if Admin OR if user has the specific permission
const canAccess = computed(() => {
  const auth = page.props.auth;

  // 1. If user is admin, they always have access
  if (auth.is_admin) return true;

  // 2. If no specific permission is required, allow access
  if (!props.requiredPermission) return true;

  // 3. Otherwise, check if the permission exists in the user's list
  return auth.permissions.includes(props.requiredPermission);
});

onMounted(() => {
  if (dropzoneElement.value && canAccess.value) {
    const dropzone = new Dropzone(dropzoneElement.value, {
      url: props.url,
      headers: {
          ...props.headers,
          'X-Requested-With': 'XMLHttpRequest'
      },
      maxFilesize: 2,
      acceptedFiles: 'image/*',
      addRemoveLinks: true,
      dictRemoveFile: page.props.translations?.messages?.remove || "Remove",
    });

    dropzone.on('success', (file, response) => emit('success', { file, response }));
    dropzone.on('error', (file, message) => emit('error', { file, message }));
  }
});
</script>
