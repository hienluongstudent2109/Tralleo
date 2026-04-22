<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Welcome Back</h2>
                <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
            </div>

            <!-- Form -->
            <form class="bg-white rounded-lg shadow-md p-8 space-y-6" @submit.prevent="handleLogin">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input
                        id="email"
                        v-model="credentials.email"
                        type="email"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="you@example.com"
                    />
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input
                        id="password"
                        v-model="credentials.password"
                        type="password"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="••••••••"
                    />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input
                            v-model="credentials.remember"
                            type="checkbox"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500"
                        />
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                </div>

                <!-- Error Message -->
                <div v-if="authStore.error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ authStore.error }}</p>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="authStore.isLoading"
                    class="w-full py-2 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="!authStore.isLoading">Sign In</span>
                    <span v-else>Signing In...</span>
                </button>
            </form>

            <!-- Register Link -->
            <p class="text-center text-sm text-gray-600">
                Don't have an account?
                <router-link to="/register" class="text-indigo-600 hover:text-indigo-500 font-medium">
                    Sign up
                </router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../store/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const credentials = ref({
    email: '',
    password: '',
    remember: false,
});

const handleLogin = async () => {
    try {
        await authStore.login({
            email: credentials.value.email,
            password: credentials.value.password,
            remember: credentials.value.remember,
        });

        // Redirect to the requested page or home
        const redirectTo = route.query.redirect || '/';
        router.push(redirectTo);
    } catch (error) {
        // Error is handled by the store
        console.error('Login error:', error);
    }
};
</script>

<style scoped>
input:focus {
    @apply ring-2 ring-indigo-500 border-transparent;
}
</style>
