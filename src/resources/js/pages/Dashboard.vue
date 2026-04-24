<template>
  <div class="min-h-screen bg-gray-100 p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Workspaces</h1>

      <button
        @click="showModal = true"
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
      >
        + Create Workspace
      </button>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="text-center py-10">
      Loading...
    </div>

    <!-- Empty state -->
    <div v-else-if="store.workspaces.length === 0" class="text-center py-10 text-gray-500">
      No workspace yet. Create one 🚀
    </div>

    <!-- Workspace list -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div
        v-for="ws in store.workspaces"
        :key="ws.id"
        @click="goToWorkspace(ws.id)"
        class="bg-white p-4 rounded-xl shadow cursor-pointer hover:shadow-lg transition"
      >
        <h2 class="text-lg font-semibold">{{ ws.name }}</h2>
        <p class="text-sm text-gray-500 mt-2">
          Owner: {{ ws.owner_id }}
        </p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center">
      <div class="bg-white p-6 rounded-xl w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Create Workspace</h2>

        <input
          v-model="name"
          placeholder="Workspace name"
          class="w-full border px-3 py-2 rounded mb-4"
        />

        <div class="flex justify-end gap-2">
          <button @click="showModal = false" class="px-3 py-1">
            Cancel
          </button>
          <button
            @click="handleCreate"
            class="bg-blue-500 text-white px-4 py-2 rounded"
          >
            Create
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useWorkspaceStore } from '../store/workspace'
import { useRouter } from 'vue-router'

const store = useWorkspaceStore()
const router = useRouter()

const showModal = ref(false)
const name = ref('')

onMounted(() => {
  store.fetchWorkspaces()
})

const handleCreate = async () => {
  if (!name.value) return

  await store.addWorkspace({ name: name.value })

  name.value = ''
  showModal.value = false
}

const goToWorkspace = (id) => {
  router.push(`/workspace/${id}`)
}
</script>
