import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

export default defineNuxtConfig({
  compatibilityDate: '2025-07-25',
  devtools: { enabled: true },
  ssr: false,
	pages: true,
  
  modules: [
    '@nuxt/eslint',
    '@nuxt/icon',
    '@nuxt/image',
    '@pinia/nuxt'
  ],

  css: [
    'quasar/src/css/index.sass',
    '@quasar/extras/material-icons/material-icons.css',
    'assets/index.scss'
  ],

  build: {
    transpile: ['quasar']
  },

  vite: {
    vue: {
      template: { transformAssetUrls }
    },
    plugins: [
      quasar({
        sassVariables: 'assets/quasar-variables.sass'
      })
    ]
  },

  runtimeConfig: {
    apiSecret: process.env.API_SECRET,
    
    public: {
      apiBase: process.env.API_BASE_URL || 'http://localhost:8000/api',
      appName: 'SSA - Secret Service Agency'
    }
  },

  app: {
    head: {
      title: 'SSA - Secret Service Agency',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Secret Service Agency Management System' },
        { name: 'format-detection', content: 'telephone=no' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    }
  },

  router: {
    options: {
      linkActiveClass: 'active-link',
      linkExactActiveClass: 'exact-active-link'
    }
  },

  typescript: {
    strict: true,
    typeCheck: true
  },
})