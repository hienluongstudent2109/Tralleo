import { defineStore } from 'pinia'
import { getProjects, createProject } from '../api/project'

export const useProjectStore = defineStore('project', {
  state: () => ({
    projects: [],
    loading: false
  }),

  actions: {
    async fetchProjects(workspaceId) {
      this.loading = true
      try {
        const res = await getProjects(workspaceId)
        this.projects = res.data
      } finally {
        this.loading = false
      }
    },

    async addProject(payload) {
      const res = await createProject(payload)
      this.projects.unshift(res.data)
    }
  }
})
