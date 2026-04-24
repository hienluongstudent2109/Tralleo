import axios from '../utils/axios'

export const getWorkspaces = () => {
  return axios.get('/workspaces')
}

export const createWorkspace = (data) => {
  return axios.post('/workspaces', data)
}

export const addMember = (workspaceId, data) => {
  return axios.post(`/workspaces/${workspaceId}/members`, data)
}
