import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import devFilePlugin from './admin/src/vite-plugin-dev-file.js';
import { resolve } from 'path';

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        react(),
        tailwindcss(),
        devFilePlugin()
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, './admin/src')
        },
    },
    build: {
        outDir: 'admin/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                main: resolve(__dirname, './admin/src/main.tsx'),
            },
            output: {
                entryFileNames: 'js/[name]-[hash].js',
                chunkFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name?.endsWith('.css')) {
                        return 'css/[name]-[hash][extname]';
                    }
                    return 'build/[name]-[hash][extname]';
                },
            },
        },
        // Generate source maps in development
        sourcemap: process.env.NODE_ENV === 'development',
        // Minify in production
        minify: process.env.NODE_ENV === 'production' ? 'esbuild' : false,
    },
    server: {
        port: 3001,
        open: false,
        cors: true,
    },
});
