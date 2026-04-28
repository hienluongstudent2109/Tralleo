import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import * as authAPI from '../api/auth';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(localStorage.getItem('user') ? localStorage.getItem('user') : null);
    const token = ref(localStorage.getItem('auth_token') || null);
    const isLoading = ref(false);
    const error = ref(null);

    const isAuthenticated = computed(() => !!token.value && !!user.value);

    const setUser = (userData) => {
        user.value = userData;
        localStorage.setItem('user', JSON.stringify(userData));
    };

    const setToken = (authToken) => {
        token.value = authToken;
        localStorage.setItem('auth_token', authToken);
    };

    const login = async (credentials) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await authAPI.login(credentials);
            setToken(response.token);
            setUser(response.user);
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Login failed';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async () => {
        isLoading.value = true;
        try {
            await authAPI.logout();
            token.value = null;
            user.value = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
        } catch (err) {
            error.value = err.response?.data?.message || 'Logout failed';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const register = async (userData) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await authAPI.register(userData);
            setToken(response.token);
            setUser(response.user);
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Registration failed';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const loadProfile = async () => {
        if (!token.value) return;
        try {
            const response = await authAPI.getProfile();
            setUser(response.user);
        } catch (err) {
            token.value = null;
            user.value = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
        }
    };

    const clearError = () => {
        error.value = null;
    };

    return {
        user,
        token,
        isLoading,
        error,
        isAuthenticated,
        login,
        logout,
        register,
        loadProfile,
        setUser,
        setToken,
        clearError,
    };
});
