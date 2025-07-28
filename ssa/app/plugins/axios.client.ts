import axios from 'axios'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  
  axios.defaults.baseURL = config.public.apiBase || 'http://localhost:8000'
  axios.defaults.headers.common['Content-Type'] = 'application/json'
  
  axios.interceptors.request.use((config) => {
    if (import.meta.client) {
      const tokenData = getStoredTokenData()
      if (tokenData && !isTokenExpired(tokenData)) {
        config.headers.Authorization = `Bearer ${tokenData.token}`
      }
    }
    return config
  }, (error) => {
    return Promise.reject(error)
  })
  
  axios.interceptors.response.use(
    (response) => response,
    async (error) => {
      if (error.response?.status === 401) {
        if (import.meta.client) {
          localStorage.removeItem('ssa-token')
        }
				
        await navigateTo('/login')
      }
      return Promise.reject(error)
    }
  )
  
  return {
    provide: {
      axios
    }
  }
})