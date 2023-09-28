import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/@minvws/nl-rdo-rijksoverheid-ui-theme/fonts',
                    dest: '.'
                }
            ]
        }),
        laravel({
            input: ["resources/scss/pdf.scss"],
            refresh: false,

        }),
    ]
});
