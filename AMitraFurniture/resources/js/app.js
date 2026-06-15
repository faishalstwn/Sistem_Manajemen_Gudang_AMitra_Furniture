import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

createInertiaApp({
    title: (title) => title ? `${title} — A Mitra Furniture` : 'A Mitra Furniture',
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })

        app.use(plugin)

        // Register global components
        app.component('Head', Head)
        app.component('Link', Link)

        app.mount(el)
    },
    progress: {
        color: '#1565C0', // Brand primary blue
        showSpinner: true,
    },
})
