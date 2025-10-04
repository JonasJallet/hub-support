import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import history from 'connect-history-api-fallback'
import type { Connect } from 'vite'

export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
    {
      name: 'spa-fallback',
      configureServer(server) {
        server.middlewares.use(history() as Connect.NextHandleFunction)
      }
    }
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
})
