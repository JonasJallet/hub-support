<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore.ts'

const router = useRouter()
const authStore = useAuthStore()

const loginData = ref({
  email: '',
  password: '',
})

const isFormValid = computed(() => {
  return /\S+@\S+\.\S+/.test(loginData.value.email) && loginData.value.password.trim().length > 0
})

const handleLogin = async () => {
  if (!isFormValid.value) {
    authStore.error = 'Veuillez renseigner un email et un mot de passe valides.'
    return
  }

  authStore.error = null

  const payload = {
    email: loginData.value.email,
    password: loginData.value.password,
  }

  const success = await authStore.login(payload)

  if (success) {
    await router.push('/')
  }
}

onMounted(() => {
  authStore.error = null
})
</script>

<template>
  <div class="min-h-[calc(100vh-10rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl space-y-6">
      <h2 class="text-3xl font-extrabold text-gray-900 text-center">Se connecter</h2>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700"> Adresse Email </label>
          <input
            id="email"
            name="email"
            type="email"
            v-model="loginData.email"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Mot de passe
          </label>
          <input
            id="password"
            name="password"
            type="password"
            v-model="loginData.password"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />

          <div class="text-right mt-2">
            <router-link
              to="/reset-password"
              class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
              Mot de passe oublié ?
            </router-link>
          </div>
        </div>
        <p
          v-if="authStore.error"
          class="text-sm font-medium text-red-600 p-2 bg-red-50 border border-red-200 rounded-lg"
        >
          {{ authStore.error }}
        </p>
        <button
          type="submit"
          :disabled="authStore.isLoading || !isFormValid"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition duration-150 ease-in-out"
          :class="{
            'bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500':
              isFormValid && !authStore.isLoading,
            'bg-indigo-400 cursor-not-allowed': !isFormValid || authStore.isLoading,
          }"
        >
          <span v-if="authStore.isLoading" class="flex items-center">
            <svg
              class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Connexion...
          </span>
          <span v-else>Se connecter</span>
        </button>
      </form>

      <div class="text-center text-sm text-gray-600">
        Pas encore de compte ?
        <router-link to="/registration" class="font-medium text-indigo-600 hover:text-indigo-500">
          Créez-en un ici
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
