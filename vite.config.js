import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 'resources/js/app.js', 'resources/js/checkin.js', 'resources/js/leaflet-setup.js',
                // Unico punto de entrada React de todo el sitio: monta el
                // boleto "Go Left!!" (TearTicket) dentro de la landing,
                // que por lo demas es Blade + JS vanilla puro. Ver
                // resources/js/tear-ticket-mount.jsx.
                'resources/js/tear-ticket-mount.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
});
