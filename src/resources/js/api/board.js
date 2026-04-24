import axios from '../utils/axios'

export const getBoard = (projectId) => {
  return axios.get(`/boards/${projectId}`)
}

export const createColumn = (data) => {
  return axios.post('/columns', data)
}

export const createTask = (data) => {
  return axios.post('/tasks', data)
}

export const moveTask = (taskId, data) => {
  return axios.patch(`/tasks/${taskId}`, data)
}
