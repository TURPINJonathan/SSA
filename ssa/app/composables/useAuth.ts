export const useAuth = () => {
  const authStore = useAuthStore()
  
  return {
    agent: computed(() => authStore.agent),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    isLoading: computed(() => authStore.isLoading),
    login: authStore.login,
    logout: authStore.logout,
    checkAuth: authStore.checkAuth,
    hasRole: authStore.hasRole
  }
}