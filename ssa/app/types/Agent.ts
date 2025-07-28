import type { AgentStatus } from "~/enum/AgentStatus"

export interface Agent {
  id: string
  codename: string
  roles: Array<'ROLE_USER' | 'ROLE_AGENT' | 'ROLE_ADMIN'>
  status: AgentStatus
}