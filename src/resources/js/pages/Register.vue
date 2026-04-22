<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Create Account</h2>
                <p class="mt-2 text-sm text-gray-600">Join us today</p>
            </div>

            <!-- Form -->
            <form class="bg-white rounded-lg shadow-md p-8 space-y-6" @submit.prevent="handleRegister">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input
                        id="name"
                        v-model="formData.name"
                        type="text"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="John Doe"
                    />
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input
                        id="email"
                        v-model="formData.email"
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
                        v-model="formData.password"
                        type="password"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="••••••••"
                    />
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        v-model="formData.password_confirmation"
                        type="password"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="••••••••"
                    />
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
                    <span v-if="!authStore.isLoading">Create Account</span>
                    <span v-else>Creating Account...</span>
                </button>
            </form>

            <!-- Login Link -->
            <p class="text-center text-sm text-gray-600">
                Already have an account?
                <router-link to="/login" class="text-indigo-600 hover:text-indigo-500 font-medium">
                    Sign in
                </router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/auth';

const router = useRouter();
const authStore = useAuthStore();

const formData = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const handleRegister = async () => {
    if (formData.value.password !== formData.value.password_confirmation) {
        authStore.error = 'Passwords do not match';
        return;
    }

    try {
        await authStore.register(formData.value);
        router.push('/');
    } catch (error) {
        console.error('Registration error:', error);
    }
};
</script>

<style scoped>
input:focus {
    @apply ring-2 ring-indigo-500 border-transparent;
}
</style>
