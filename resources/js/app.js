import "../css/app.css";
import "./bootstrap";

import "swiper/css";
import "swiper/css/effect-fade";
import "swiper/css/pagination";
import "swiper/css/navigation";

import { createInertiaApp, usePage, router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Global Helper for Translations
        app.config.globalProperties.$t = (key) => {
            const translations = usePage().props.translations || {};
            const keys = key.split(".");
            let translation = translations;
            keys.forEach((k) => {
                translation = translation ? translation[k] : null;
            });
            return translation || key;
        };

        app.config.globalProperties.$locale = () => {
            return usePage().props.locale;
        };

        // Sync HTML direction and lang with current locale globally
        const updateDirection = (locale) => {
            if (locale) {
                document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr';
                document.documentElement.lang = locale;
            }
        };

        // Update on initial load
        updateDirection(props.initialPage.props.locale);

        // Update on navigation
        router.on('navigate', (event) => {
            updateDirection(event.detail.page.props.locale);
        });

        return app.use(plugin).use(ZiggyVue).mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
