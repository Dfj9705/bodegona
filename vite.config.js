import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/users/index.js',
                'resources/js/products/index.js',
                'resources/js/movements/index.js',
                'resources/js/brands/index.js',
                'resources/js/charts/index.js',
            ],
            refresh: true,
        }),
    ],
});
