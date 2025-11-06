import { defineConfig, loadEnv } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    const host = env.VITE_DEV_SERVER_HOST || 'localhost';
    const port = Number(env.VITE_DEV_SERVER_PORT || 5173);
    const https = env.VITE_DEV_SERVER_HTTPS === 'true';

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: [
                    ...refreshPaths,
                    'app/Livewire/**',
                    'Modules/**/resources/views/**',
                ],
            }),
        ],
        server: {
            host: true,
            port,
            https,
            hmr: {
                host,
                port,
                protocol: https ? 'wss' : 'ws',
            },
        },
    };
});
