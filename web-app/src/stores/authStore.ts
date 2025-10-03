import { defineStore } from "pinia";
import { ref } from "vue";
import { useFetch, useStorage } from "@vueuse/core";
import type { AuthResponse, User, PasswordAccess, RegistrationAccess } from "../types/auth";
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
  const token = useStorage<string | null>("token", null);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  const failedLogin = (reason: string | null = "common.invalid-credentials") => {
    isAuthenticated.value = false;
    user.value = null;
    isLoading.value = false;
    error.value = reason;
  };

  const login = async (credentials: { email: string; password: string }) => {
    isLoading.value = true;
    error.value = null;

    const {
      data,
      error: loginError,
      statusCode,
      response,
    } = await useFetch<AuthResponse>(`${baseUrl}login`)
      .post(credentials)
      .json();

    if (loginError.value) {
      if (statusCode.value === 403) {
        const res = await response.value?.json();
        failedLogin(res?.message || "common.permission-denied");
        return;
      }
      failedLogin();
      return;
    }

    if (data.value?.token) {
      token.value = data.value.token;
      await authenticate();
    } else {
      failedLogin("common.login-failed");
    }
  };

  const authenticate = async (): Promise<boolean> => {
    isLoading.value = true;
    error.value = null;

    const { data: userData, error: userError } = await useFetch<User>(
      `${baseUrl}users/me`,
      {
        headers: { Authorization: `Bearer ${token.value}` },
      },
    ).json();

    isLoading.value = false;

    if (userError.value) {
      failedLogin();
      return false;
    }

    if (userData.value?.data) {
      user.value = userData.value.data;
      isAuthenticated.value = true;
      return true;
    } else {
      failedLogin("common.user-data-missing");
      return false;
    }
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

    const registered = await useFetch<AuthResponse>(
      // Updated endpoint path as requested
      `${baseUrl}users`,
    )
      // Using standard POST with JSON body (fixes 415 error, reintroduces OPTIONS preflight)
      .post(credentials)
      .json();

    isLoading.value = false;

    if (registered.error.value) {
      console.log(registered.error)
      // Use existing failedLogin function
      failedLogin("Echec de l'inscription");
      return;
    }

    // Adapt success logic
    if (registered.data.value?.data) {
      const userData = registered.data.value.data;
      // The API response structure must match this path: data.value.data.firstName/lastName
      const username = `${userData.firstName} ${userData.lastName}`;

      // NOTE: 'toast.add' and 't' (translation) are external dependencies
      // that must be imported/injected into your Pinia store (e.g., from PrimeVue and vue-i18n).
      // Using console.log as a placeholder for the toast notification:
      console.log(`[SUCCESS] User registered: ${username}. Account is now active.`);

      /* // Placeholder for original toast logic:
      toast.add({
        severity: "success",
        summary: t("pages.unauth.registration.welcome", { user: username }),
        detail: t("pages.unauth.registration.account-is-active"),
        life: 3000,
      });
      */
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
    authenticate
  };
});
