<template>
  <div class="p-6 bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="flex justify-between mb-6">
      <h1 class="text-xl font-bold">Project Board</h1>

      <button @click="showColumnModal = true" class="btn">
        + Add Column
      </button>
    </div>

    <!-- Board -->
    <div class="flex gap-4 overflow-x-auto">

      <div
        v-for="col in store.columns"
        :key="col.id"
        class="w-64 bg-white rounded-xl p-3 shadow"
      >
        <h2 class="font-semibold mb-3">{{ col.name }}</h2>

        <!-- Tasks -->
        <div
          class="min-h-[100px]"
          @dragover.prevent
          @drop="onDrop(col.id)"
        >
          <div
            v-for="task in col.tasks"
            :key="task.id"
            draggable="true"
            @dragstart="onDragStart(task.id)"
            class="bg-gray-100 p-2 rounded mb-2 cursor-move"
          >
            {{ task.title }}
          </div>
        </div>

        <button @click="openTaskModal(col.id)" class="text-blue-500 text-sm">
          + Add Task
        </button>
      </div>

    </div>

    <!-- Create Column -->
    <div v-if="showColumnModal" class="modal">
      <div class="modal-box">
        <input v-model="columnName" placeholder="Column name" class="input" />

        <button @click="handleCreateColumn" class="btn-primary mt-2">
          Create
        </button>
      </div>
    </div>

    <!-- Create Task -->
    <div v-if="showTaskModal" class="modal">
      <div class="modal-box">
        <input v-model="taskTitle" placeholder="Task title" class="input" />

        <button @click="handleCreateTask" class="btn-primary mt-2">
          Create
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useBoardStore } from '../store/board'

const route = useRoute()
const projectId = route.params.id

const store = useBoardStore()

const showColumnModal = ref(false)
const columnName = ref('')

const showTaskModal = ref(false)
const taskTitle = ref('')
const currentColumn = ref(null)

const draggedTaskId = ref(null)

onMounted(() => {
  store.fetchBoard(projectId)
})

const handleCreateColumn = async () => {
  await store.addColumn({
    name: columnName.value,
    project_id: projectId
  })

  showColumnModal.value = false
  columnName.value = ''
}

const openTaskModal = (colId) => {
  currentColumn.value = colId
  showTaskModal.value = true
}

const handleCreateTask = async () => {
  await store.addTask({
    title: taskTitle.value,
    column_id: currentColumn.value,
    project_id: projectId
  })

  showTaskModal.value = false
  taskTitle.value = ''
}

const onDragStart = (taskId) => {
  draggedTaskId.value = taskId
}

const onDrop = (columnId) => async () => {
  if (!draggedTaskId.value) return

  await store.moveTask(draggedTaskId.value, columnId)

  draggedTaskId.value = null
}
</script>

<style>
.btn {
  background: #3b82f6;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
}

.btn-primary {
  background: #2563eb;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
}

.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-box {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 300px;
}

.input {
  width: 100%;
  border: 1px solid #ddd;
  padding: 8px;
  border-radius: 6px;
}
</style>
