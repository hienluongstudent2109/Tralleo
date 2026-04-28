<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-7xl mx-auto">
            <AppNavbar />

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Home page</h1>
            </div>

            <!-- Welcome Message -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome, {{ authStore.user?.name }}!</h2>
                <p class="text-gray-600">You're successfully logged in.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/auth';
import AppNavbar from '../components/AppNavbar.vue';

const router = useRouter();
const authStore = useAuthStore();
</script>
