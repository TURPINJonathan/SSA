import axios from 'axios'
import { defineStore } from 'pinia'
import type { Agent } from '~/types/Agent'
import type { TokenData } from '~/types/Token'

interface AuthState {
  agent: Agent | null
  isLoading: boolean
}

interface AuthResponse {
	token: string
	agent: Agent
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    agent: null,
    isLoading: false
  }),

  getters: {
    isAuthenticated: (state): boolean => {
      if (import.meta.server) return false

      const tokenData = getStoredTokenData()
      return !!tokenData && !isTokenExpired(tokenData)
    },
		currentAgent: (state): Agent | null => state.agent,
    hasRole: (state) => (role: Agent['roles'][number]): boolean => state.agent?.roles?.includes(role) || false
  },

  actions: {
    async login(credentials: { email: string; password: string }): Promise<{ success: boolean }> {
      this.isLoading = true
      
      try {
        const response = await axios.post<AuthResponse>('/auth', credentials)
        const { token, agent } = response.data

        if (import.meta.client) {
          const tokenData: TokenData = {
            token: token,
            timestamp: Date.now(),
            ttl: 3600
          }
          localStorage.setItem('ssa-token', JSON.stringify(tokenData))
        }

        this.agent = agent

        await navigateTo('/dashboard')
        
        return { success: true }
      } catch (error: unknown) {
        console.error('Erreur de connexion:', error)
        let message = 'Erreur de connexion'

        if (axios.isAxiosError(error)) {
          message = error.response?.data?.message || error.message || message
        } else if (error instanceof Error) {
          message = error.message
        }

        throw new Error(message)
      } finally {
        this.isLoading = false
      }
    },

    async logout(): Promise<void> {
      try {
        this.clearAuth()
        await navigateTo('/login')
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error)
      }
    },

    async checkAuth(): Promise<boolean> {
      if (import.meta.server) return false

      const tokenData: TokenData | null = getStoredTokenData()

      if (!tokenData || isTokenExpired(tokenData)) {
        this.clearAuth()
        return false
      }

      if (this.agent) {
        return true
      } else {
				try {
					const response = await axios.get<{ agent: Agent }>('/get-agent-from-token', {
						headers: { Authorization: `Bearer ${tokenData.token}` }
					})
					this.agent = response.data.agent

					return true
				} catch (e) {
					this.clearAuth()
					return false
				}
  		}
    },

    clearAuth(): void {
      this.agent = null
      if (import.meta.client) {
        localStorage.removeItem('ssa-token')
      }
    }
  }
})

