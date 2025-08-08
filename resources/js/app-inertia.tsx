import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`../inertia/Pages/${name}.tsx`, import.meta.glob('../inertia/Pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el) // This requires react-dom/client
        root.render(<App {...props} />)
    },
    progress: {
        color: '#4B5563',
    },
})
