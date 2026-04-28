<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <AppNavbar />

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Workspace #{{ workspaceId }}
      </h1>
    </div>

    <!-- Tabs -->
    <div class="mb-6 flex gap-4">
      <button
        @click="tab = 'projects'"
        :class="tabClass('projects')"
      >Projects</button>

      <button
        @click="tab = 'members'"
        :class="tabClass('members')"
      >Members</button>
    </div>

    <!-- PROJECT TAB -->
    <div v-if="tab === 'projects'">

      <div class="flex justify-end mb-4">
        <button
          @click="showProjectModal = true"
          class="bg-blue-500 text-white px-4 py-2 rounded"
        >
          + Create Project
        </button>
      </div>

      <!-- Loading -->
      <div v-if="projectStore.loading" class="text-center py-10">
        Loading...
      </div>

      <!-- Empty -->
      <div v-else-if="projectStore.projects.length === 0" class="text-gray-500 text-center">
        No project yet
      </div>

      <!-- List -->
      <div class="grid md:grid-cols-3 gap-4">
        <div
          v-for="p in projectStore.projects"
          :key="p.id"
          @click="goToProject(p.id)"
          class="bg-white p-4 rounded-xl shadow cursor-pointer hover:shadow-lg"
        >
          <h2 class="font-semibold">{{ p.name }}</h2>
          <p class="text-sm text-gray-500 mt-2">
            {{ p.description }}
          </p>
        </div>
      </div>
    </div>

    <!-- MEMBER TAB -->
    <div v-if="tab === 'members'">

      <div class="flex justify-end mb-4">
        <button
          @click="showMemberModal = true"
          class="bg-blue-500 text-white px-4 py-2 rounded"
        >
          + Add Member
        </button>
      </div>

      <div class="text-gray-500">
        (Member list sẽ load sau — focus API trước)
      </div>
    </div>

    <!-- CREATE PROJECT MODAL -->
    <div v-if="showProjectModal" class="modal">
      <div class="modal-box">
        <h2 class="font-bold mb-3">Create Project</h2>

        <input v-model="projectName" placeholder="Name" class="input mb-2" />
        <textarea v-model="projectDesc" placeholder="Description" class="input"></textarea>

        <div class="flex justify-end mt-3 gap-2">
          <button @click="showProjectModal = false">Cancel</button>
          <button @click="handleCreateProject" class="btn-primary">Create</button>
        </div>
      </div>
    </div>

    <!-- ADD MEMBER MODAL -->
    <div v-if="showMemberModal" class="modal">
      <div class="modal-box">
        <h2 class="font-bold mb-3">Add Member</h2>

        <input v-model="userId" placeholder="User ID" class="input" />

        <div class="flex justify-end mt-3 gap-2">
          <button @click="showMemberModal = false">Cancel</button>
          <button @click="handleAddMember" class="btn-primary">Add</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectStore } from '../store/project'
import { addMember } from '../api/workspace'
import AppNavbar from '../components/AppNavbar.vue'

const route = useRoute()
const router = useRouter()
const workspaceId = route.params.id

const projectStore = useProjectStore()

const tab = ref('projects')

const showProjectModal = ref(false)
const showMemberModal = ref(false)

const projectName = ref('')
const projectDesc = ref('')
const userId = ref('')

onMounted(() => {
  projectStore.fetchProjects(workspaceId)
})

const handleCreateProject = async () => {
  await projectStore.addProject({
    name: projectName.value,
    description: projectDesc.value,
    workspace_id: workspaceId
  })

  showProjectModal.value = false
  projectName.value = ''
  projectDesc.value = ''
}

const handleAddMember = async () => {
  await addMember(workspaceId, { user_id: userId.value })
  showMemberModal.value = false
  userId.value = ''
}

const goToProject = (id) => {
  router.push(`/project/${id}`)
}

const tabClass = (t) =>
  `px-4 py-2 rounded ${tab.value === t ? 'bg-blue-500 text-white' : 'bg-gray-200'}`
</script>

<style>
.modal {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.3);
}

.modal-box {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
}

.input {
  width: 100%;
  border: 1px solid #ddd;
  padding: 8px;
  border-radius: 6px;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
}
</style>
