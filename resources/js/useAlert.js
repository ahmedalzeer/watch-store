import Swal from 'sweetalert2';
import { usePage } from '@inertiajs/vue3';

export function useAlert() {
    const page = usePage();

    const t = (key) => {
        const translations = page.props.translations || {};
        const keys = key.split(".");
        let translation = translations;
        keys.forEach((k) => {
            translation = translation ? translation[k] : null;
        });
        return translation || key;
    };

    const success = (messageKey) => {
        Swal.fire({
            icon: 'success',
            title: t('messages.success'),
            text: t(messageKey),
            timer: 2000,
            showConfirmButton: false,
            background: document.documentElement.classList.contains('dark') ? '#1a1c23' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
        });
    };

    const confirmDelete = (callback) => {
        Swal.fire({
            title: t('messages.are_you_sure'),
            text: t('messages.delete_warning'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7e3af2',
            cancelButtonColor: '#d33',
            confirmButtonText: t('messages.yes_delete'),
            cancelButtonText: t('messages.cancel'),
            reverseButtons: true,
            background: document.documentElement.classList.contains('dark') ? '#1a1c23' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    };

    return { success, confirmDelete };
}
