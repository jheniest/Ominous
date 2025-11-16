import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/creepy-background.css', 'resources/js/creepy-background.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
