import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { VitePWA } from "vite-plugin-pwa";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: "autoUpdate",
            includeAssets: [
                "favicon.ico",
                "robots.txt",
                "icons/icon-192.svg",
                "icons/icon-512.svg",
            ],
            manifest: {
                id: "/?source=pwa",
                name: "MeroHisab - Family Income & Expense Tracker",
                short_name: "MeroHisab",
                description:
                    "Track family income and expenses with role-based access and clear reporting.",
                start_url: "/login",
                display: "standalone",
                background_color: "#f8fafc",
                theme_color: "#0f172a",
                orientation: "portrait-primary",
                scope: "/",
                icons: [
                    {
                        src: "/icons/icon-192.svg",
                        sizes: "192x192",
                        type: "image/svg+xml",
                        purpose: "any maskable",
                    },
                    {
                        src: "/icons/icon-512.svg",
                        sizes: "512x512",
                        type: "image/svg+xml",
                        purpose: "any maskable",
                    },
                ],
            },
            workbox: {
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
                navigateFallback: "/offline.html",
                navigateFallbackDenylist: [/^\/api\//, /^\/sanctum\//],
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) =>
                            request.destination === "script" ||
                            request.destination === "style" ||
                            request.destination === "font",
                        handler: "StaleWhileRevalidate",
                        options: {
                            cacheName: "asset-cache",
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24 * 30,
                            },
                        },
                    },
                    {
                        urlPattern: ({ request }) =>
                            request.destination === "image",
                        handler: "CacheFirst",
                        options: {
                            cacheName: "image-cache",
                            expiration: {
                                maxEntries: 60,
                                maxAgeSeconds: 60 * 60 * 24 * 30,
                            },
                        },
                    },
                ],
            },
            devOptions: {
                enabled: true,
                navigateFallback: "/offline.html",
            },
        }),
    ],
});
