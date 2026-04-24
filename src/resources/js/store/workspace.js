import { defineStore } from 'pinia'
import { getWorkspaces, createWorkspace } from '../api/workspace'

export const useWorkspaceStore = defineStore('workspace', {
  state: () => ({
    workspaces: [],
    loading: false
  }),

  actions: {
    async fetchWorkspaces() {
      this.loading = true
      try {
        const res = await getWorkspaces()
        this.workspaces = res.data
      } finally {
        this.loading = false
      }
    },

    async addWorkspace(payload) {
      const res = await createWorkspace(payload)
      this.workspaces.unshift(res.data)
    }
  }
})
