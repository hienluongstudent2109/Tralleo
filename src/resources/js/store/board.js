import { defineStore } from 'pinia'
import { getBoard, createColumn, createTask, moveTask } from '../api/board'

export const useBoardStore = defineStore('board', {
  state: () => ({
    columns: [],
    loading: false
  }),

  actions: {
    async fetchBoard(projectId) {
      this.loading = true
      try {
        const res = await getBoard(projectId)
        this.columns = res.data
      } finally {
        this.loading = false
      }
    },

    async addColumn(payload) {
      const res = await createColumn(payload)
      this.columns.push(res.data)
    },

    async addTask(payload) {
      const res = await createTask(payload)

      const col = this.columns.find(c => c.id === payload.column_id)
      col.tasks.push(res.data)
    },

    async moveTask(taskId, toColumnId) {
      await moveTask(taskId, { column_id: toColumnId })

      let movedTask = null

      // remove from old column
      this.columns.forEach(col => {
        const index = col.tasks.findIndex(t => t.id === taskId)
        if (index !== -1) {
          movedTask = col.tasks.splice(index, 1)[0]
        }
      })

      // add to new column
      const target = this.columns.find(c => c.id === toColumnId)
      target.tasks.push(movedTask)
    }
  }
})
