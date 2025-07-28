export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore()
  
  if (import.meta.client) {
    await authStore.checkAuth()
  }
})