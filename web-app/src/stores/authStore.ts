import { defineStore } from "pinia";
import { ref } from "vue";
import { useFetch, useStorage } from "@vueuse/core";
import type { User, PasswordAccess, RegistrationAccess } from "../types/auth";
import router from '../router';

// const baseUrl = import.meta.env.VITE_API_URL;
const baseUrl = "https://localhost/api/"

export const useAuthStore = defineStore("auth", () => {
  const user = useStorage<User | null>("user", null);
  const isAuthenticated = useStorage<boolean>("isAuthenticated", false);
  const currentLocale = useStorage<{ name: string; code: string }>(
    "currentLocale",
    { name: "🇺🇸", code: "en" },
  );
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  const failedLogin = (reason: string | null = "common.invalid-credentials") => {
    isAuthenticated.value = false;
    user.value = null;
    isLoading.value = false;
    error.value = reason;
  };

  const login = async (credentials: { email: string; password: string }): Promise<boolean> => {
    isLoading.value = true;
    error.value = null;

    const { data: userData, error: loginError, statusCode, response } = await useFetch(`${baseUrl}login`)
      .post(credentials)
      .json();

    if (loginError.value) {
      if (statusCode.value === 403) {
        const res = await response.value?.json();
        error.value = res?.message || "common.permission-denied";
      } else {
        error.value = "Une erreur est survenue";
      }
      return false;
    }

    user.value = userData.value.data;
    isAuthenticated.value = true;
    isLoading.value = false;
    return true;
  };

  const logout = () => {
    user.value = null;
    isAuthenticated.value = false;
    isLoading.value = false;
    error.value = null;
    window.location.reload();
  };

  const setCurrentLocale = (newCurrentLocale: {
    name: string;
    code: string;
  }) => {
    currentLocale.value = newCurrentLocale;
  };

  const getCurrentLocale = () => {
    return currentLocale.value;
  };


  const resetPassword = async (payload: PasswordAccess) => {
    isLoading.value = true;
    error.value = null;

    const reset = await useFetch(`${baseUrl}users/reset-password`)
      .patch(payload)
      .json();

    isLoading.value = false;

    if (!reset.error.value) {
      await router.push("/");
    } else {
      error.value = "common.reset-password-failed";
    }
  };

  const register = async (credentials: RegistrationAccess) => {
    isLoading.value = true;
    error.value = null;

    const registered = await useFetch(
      `${baseUrl}users`,
    )
      .post(credentials)
      .json();

    isLoading.value = false;

    if (registered.error.value) {
      console.log(registered.error)
      failedLogin("Echec de l'inscription");
      return;
    }

    if (registered.data.value?.data) {
      const userData = registered.data.value.data;
      const username = `${userData.firstName} ${userData.lastName}`;

      console.log(`[SUCCESS] User registered: ${username}. Account is now active.`);
    }

    await router.push("/");
  };

  return {
    user,
    isAuthenticated,
    isLoading,
    error,
    login,
    logout,
    setCurrentLocale,
    getCurrentLocale,
    register,
    resetPassword,
  };
});
