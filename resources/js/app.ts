import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import Toast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, {
                position: 'top-right',
                duration: 3000,
            })
            .mount(el);

        // Initialize AOS (Animate On Scroll) after DOM is ready
        setTimeout(() => {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 100,
                delay: 0,
                disable: false,
                startEvent: 'DOMContentLoaded',
                animatedClassName: 'aos-animate',
                useClassNames: false,
                disableMutationObserver: false,
                debounceDelay: 50,
                throttleDelay: 99,
            });
        }, 100);

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
