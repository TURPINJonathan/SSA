import { Dark, Quasar } from 'quasar'

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.use(Quasar, {
    plugins: { Dark },
    config: {
      dark: true,
      brand: {
        // Couleurs de base
        primary: '#0D47A1',           // $ssa-primary
        secondary: '#ff00ff',         // $ssa-secondary
        accent: '#00f5ff',            // $ssa-accent
        dark: '#0a0a0a',              // $ssa-dark
        positive: '#00ff41',          // $ssa-success
        negative: '#F44336',          // $ssa-danger
        info: '#00f5ff',              // $ssa-accent
        warning: '#FF9800',           // $ssa-warning

        // Classification
        classified: '#B71C1C',        // $ssa-danger-dark
        secret: '#0a0a0a',            // $ssa-dark
        'top-secret': '#ff00ff',      // $ssa-secondary
        public: '#00ff41',            // $ssa-success

        // Niveaux de danger
        'danger-low': '#4CAF50',      // $ssa-success-light
        'danger-medium': '#FF9800',   // $ssa-warning
        'danger-high': '#F44336',     // $ssa-danger
        'danger-critical': '#AD1457', // $ssa-critical

        // Statuts d'agent
        'agent-available': '#4CAF50', // $ssa-success-light
        'agent-mission': '#0D47A1',   // $ssa-primary
        'agent-retired': '#757575',
        'agent-kia': '#212121',

        // Statuts de mission
        'mission-success': '#4CAF50', // $ssa-success-light
        'mission-failure': '#F44336', // $ssa-danger
      }
    }
  })

  Dark.set(true)
})