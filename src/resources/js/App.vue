<template>
    <div id="app">
        <router-view />
    </div>
</template>

<script setup>
import { useAuthStore } from './store/auth';
import { onMounted } from 'vue';

const authStore = useAuthStore();

onMounted(async () => {
    // Load user profile on app initialization if token exists
    if (authStore.token && !authStore.user) {
        await authStore.loadProfile();
    }
});
</script>
