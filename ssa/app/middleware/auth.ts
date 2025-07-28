import type { RouteLocationNormalized } from "vue-router"

export default defineNuxtRouteMiddleware(async (to: RouteLocationNormalized, from: RouteLocationNormalized) => {
  if (import.meta.server) return

  const authStore = useAuthStore()
  
  const publicPages: string[] = ['/login']
  const isPublicPage: boolean = publicPages.includes(to.path)
  
	const isAuthenticated: boolean = await authStore.checkAuth()

  if (!isPublicPage) {
    
    if (!isAuthenticated) {
      return navigateTo('/login')
    }
	} else if (to.path === '/') {
		return navigateTo(isAuthenticated ? '/dashboard' : '/login')
  } else {
    if (authStore.isAuthenticated && to.path === '/login') {
      return navigateTo('/dashboard')
    }
  }
})