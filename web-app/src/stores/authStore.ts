import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { useFetch, useStorage } from '@vueuse/core'
import type { PasswordAccess, RegistrationAccess, User } from '../types/auth'
import router from '../router'

const baseUrl = import.meta.env.VITE_API_URL;

export const useAuthStore = defineStore("auth", () => {
  const user = useStorage<User | null>("user", null, sessionStorage);
  const token = useStorage<string | null>("token", null, sessionStorage);
  const isAuthenticated = computed(() => !!user.value && !!token.value);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  const failedLogin = (reason: string | null = "common.invalid-credentials") => {
    user.value = null;
    token.value = null;
    isLoading.value = false;
    error.value = reason;
  };

  const authenticate = async (): Promise<boolean> => {
    if (!token.value) {
      failedLogin();
      return false;
    }

    const { error: userError } = await useFetch<User>(
      `${baseUrl}users/authenticate`,
      {
        headers: { Authorization: `Bearer ${token.value}` },
      },
    ).json();

    if (userError.value) {
      if (!userError.value.is_internal) failedLogin();
      return false;
    }

    return true;
  };

  const login = async (credentials: { email: string; password: string }): Promise<boolean> => {
    isLoading.value = true;
    error.value = null;

    const {
      data: userData,
      error: loginError,
      statusCode,
      response
    } = await useFetch(`${baseUrl}login`)
      .post(credentials)
      .json();

    if (loginError.value) {
      if (statusCode.value === 403) {
        const res = await response.value?.json();
        error.value = res?.message || "common.permission-denied";
      } else {
        error.value = "Une erreur est survenue";
      }
      failedLogin(error.value);
      return false;
    }

    const responseData = userData.value?.data;
    if (responseData?.token) {
      token.value = responseData.token;
      user.value = responseData;
      await authenticate();
      return true;
    } else {
      failedLogin("common.no-token-received");
      return false;
    }
  };

  const logout = async () => {
    user.value = null;
    token.value = null;
    isLoading.value = false;
    error.value = null;
    await router.push("/login");
  };

  const resetPassword = async (currentPassword: string, payload: PasswordAccess) => {
    isLoading.value = true;
    error.value = null;

    await login({ email: payload.email, password: currentPassword })

    console.log(token.value)

    const reset = await useFetch(`${baseUrl}users/reset-password`, {
      headers: { Authorization: `Bearer ${token.value}` },
    })
      .patch(payload)
      .json();

    isLoading.value = false;

    if (!reset.error.value) {
      await logout();
    } else {
      error.value = "Échec de changement du mot de passe.";
    }
  };

  const register = async (credentials: RegistrationAccess) => {
    isLoading.value = true;
    error.value = null;

    const response = await useFetch(`${baseUrl}users`)
      .post(credentials)
      .json();

    isLoading.value = false;

    if (response.error.value) {
      error.value = response.data.value?.message ||
        response.error.value?.message ||
        "Échec de l'inscription. Veuillez réessayer.";
      return;
    }

    await router.push("/");
  };

  return {
    user,
    token,
    isAuthenticated,
    isLoading,
    error,
    login,
    logout,
    register,
    resetPassword,
  };
});
