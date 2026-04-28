<template>
  <nav class="bg-white p-4 rounded-lg shadow mb-6 flex justify-between items-center">
    <div class="text-xl font-bold">Tralleo</div>

    <div class="flex items-center gap-2">
      <button
        @click="router.push('/')"
        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
      >
        Home
      </button>

      <button
        @click="router.push('/dashboard')"
        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
      >
        Dashboard
      </button>

      <button
        @click="handleLogout"
        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
      >
        Logout
      </button>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/auth'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>
