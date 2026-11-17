import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
            ],

            refresh: true,

            fonts: [
                bunny('Instrument Sans', {
                    weights: [200, 300, 400],
                }),
            ],
        }),
        tailwindcss(
            theme => ({
                content: [
                    './resources/**/*.blade.php',
                    './resources/**/*.js',
                    './resources/**/*.vue',
                    './vendor/resma/filament-awin-theme/resources/**/*.blade.php',
                ],
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Instrument Sans', 'sans-serif'],
                        },
                    },
                },
            }),
        ),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
