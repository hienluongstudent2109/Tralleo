import axios from '../utils/axios'

export const getProjects = (workspaceId) => {
  return axios.get(`/projects?workspace_id=${workspaceId}`)
}

export const createProject = (data) => {
  return axios.post('/projects', data)
}
