import { defineConfig } from "vite"
import stylelint from "vite-plugin-stylelint"
import eslint from "vite-plugin-eslint"
import { resolve } from "path"
import dotenv from 'dotenv'

dotenv.config() // load env vars from .env

export default defineConfig({
    plugins: [stylelint(), eslint()],
    base: './',
    root: 'src',
    publicDir: '../public',
    build: {
        rollupOptions: {
            input: {
                // een entrypoint voor elke HTML pagina
                // uiteraard is dat altijd index.html
                // om goed te kunnen werken heb ik alle content in de 'src' folder gestopt
                // dat is netter
                home: resolve(__dirname, 'src/index.html'),
                art_detail: resolve(__dirname, 'src/art-detail/index.html'),
                profile_detail: resolve(__dirname, 'src/profile-detail/index.html'),
                products: resolve(__dirname, 'src/products/index.html'),
                my_profile: resolve(__dirname, 'src/my-profile/index.html'),
                authentication: resolve(__dirname, 'src/authentication/index.html'),
                add_art: resolve(__dirname, 'src/my-profile/add_art/index.html'),
            },
        }
    }
})