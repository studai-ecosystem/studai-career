import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/studai/theme.css',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Increase the warning threshold to 1 MB (Livewire + Alpine can be large)
        chunkSizeWarningLimit: 1024,
        rollupOptions: {
            output: {
                // Split vendor libraries into their own chunk so they can be
                // cached independently of application code changes.
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('alpinejs') || id.includes('@alpinejs')) {
                            return 'vendor-alpine';
                        }
                        if (id.includes('axios') || id.includes('lodash')) {
                            return 'vendor-utils';
                        }
                        return 'vendor';
                    }
                },
            },
        },
    },
});
