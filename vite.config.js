import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 'resources/js/app.js', 'resources/js/checkin.js', 'resources/js/leaflet-setup.js',
                // Puntos de entrada React del evento "Go Left!!" (banner +
                // registro + ticket flotante, componente compartido en
                // resources/js/components/GoLeftShowcase.jsx): la pagina
                // standalone del evento y el panel deslizante del landing.
                // El resto del sitio sigue siendo Blade + JS vanilla puro.
                'resources/js/go-left-showcase-mount.jsx',
                'resources/js/go-left-panel-mount.jsx',
                // Solo utilidades de Tailwind (sin Preflight), para que el
                // panel de Eventos del landing pueda usar las mismas clases
                // sueltas que ya usa GoLeftShowcase.jsx en la pagina
                // standalone (que carga el app.css completo) sin arrastrar
                // el reset de Tailwind sobre el resto del landing.
                'resources/css/gls-panel-utilities.css',
            ],
            refresh: true,
        }),
        react(),
    ],
});
