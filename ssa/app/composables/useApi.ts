import axios, { type AxiosRequestConfig } from 'axios'

export const useApi = () => {
  const authStore = useAuthStore()

  const apiCall = async <T = unknown>(endpoint: string, options: AxiosRequestConfig = {}): Promise<T> => {
    try {
      const response = await axios({
        url: endpoint,
        ...options
      })

      return response.data as T
    } catch (error: unknown) {
      if (axios.isAxiosError(error) && error.response?.status === 401) {
        await authStore.logout()
      }
      throw error
    }
  }

  const get = <T = unknown>(endpoint: string, config = {}) => apiCall<T>(endpoint, { method: 'GET', ...config })
  const post = <T = unknown>(endpoint: string, data = {}, config = {}) => apiCall<T>(endpoint, { method: 'POST', data, ...config })
  const put = <T = unknown>(endpoint: string, data = {}, config = {}) => apiCall<T>(endpoint, { method: 'PUT', data, ...config })
  const del = <T = unknown>(endpoint: string, config = {}) => apiCall<T>(endpoint, { method: 'DELETE', ...config })

  return {
    apiCall,
    get,
    post,
    put,
    delete: del
  }
}