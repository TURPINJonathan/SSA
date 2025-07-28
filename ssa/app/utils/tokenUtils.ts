import type { TokenData } from "~/types/Token"

export function getStoredTokenData(): TokenData | null {
  if (import.meta.server) return null

  try {
    const stored = localStorage.getItem('ssa-token')
    return stored ? JSON.parse(stored) : null
  } catch {
    return null
  }
}

export function isTokenExpired(tokenData: TokenData): boolean {
  const now = Date.now()
  const expirationTime = tokenData.timestamp + (tokenData.ttl * 1000)
  return now >= expirationTime
}