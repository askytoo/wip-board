import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { svelte } from "@sveltejs/vite-plugin-svelte";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
            ssr: 'resources/js/ssr.js',
        }),
        svelte({
            compilerOptions: {
                hydratable: true,
            },
            preprocess: autoPreprocess(),
        }),
        typescript({
            sourceMap: process.env.NODE_ENV !== "production",
        }),
    ],
    optimizeDeps: {
        include: ["@inertiajs/inertia", "@inertiajs/inertia-svelte"],
    },
    resolve: {
        alias: {
            "@": resolve(projectRootDir, "resources/js"),
            "~": resolve(projectRootDir, "resources"),
        },
        extensions: [".js", ".svelte", ".json"],
    },
});
