<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore.ts'
import type { PasswordAccess } from '@/types/auth.ts'

const authStore = useAuthStore()

const email = ref('')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const localError = ref<string | null>(null)

const passwordChecks = computed(() => {
  const p = newPassword.value
  return {
    minLength: p.length >= 8,
    hasUppercase: /[A-Z]/.test(p),
    hasSpecialChar: /[!@#$%^&*(),.?":{}|<>]/.test(p),
    hasDigit: /\d/.test(p),
  }
})

const isPasswordValid = computed(() => {
  return Object.values(passwordChecks.value).every(Boolean)
})

const passwordsMatch = computed(() => newPassword.value !== '' && newPassword.value === confirmPassword.value)

const isFormValid = computed(() => {
  return /\S+@\S+\.\S+/.test(email.value) && isPasswordValid.value && passwordsMatch.value
})

const handleReset = async () => {
  localError.value = null
  authStore.error = null

  if (!/\S+@\S+\.\S+/.test(email.value)) {
    localError.value = "Veuillez fournir une adresse email valide."
    return
  }

  if (!isPasswordValid.value) {
    localError.value = "Le mot de passe ne respecte pas tous les critères."
    return
  }

  if (!passwordsMatch.value) {
    localError.value = "Les mots de passe ne correspondent pas."
    return
  }

  const payload: PasswordAccess = {
    email: email.value,
    newPassword: newPassword.value,
  }

  await authStore.resetPassword(currentPassword.value, payload)
}

const validationClass = (isValid: boolean, isDirty: boolean) => {
  if (!isDirty) return 'text-gray-400'
  return isValid ? 'text-green-500' : 'text-red-500'
}

onMounted(() => {
  authStore.error = null
  localError.value = null
})
</script>

<template>
  <div class="min-h-[calc(100vh-10rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl space-y-6">
      <h2 class="text-3xl font-extrabold text-gray-900 text-center">Réinitialiser le mot de passe</h2>

      <form @submit.prevent="handleReset" class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Adresse Email</label>
          <input
            id="email"
            name="email"
            type="email"
            v-model="email"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label for="currentPassword" class="block text-sm font-medium text-gray-700">
            Mot de passe actuel
          </label>
          <input
            id="currentPassword"
            name="currentPassword"
            type="password"
            v-model="currentPassword"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label for="newPassword" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
          <input
            id="newPassword"
            name="newPassword"
            type="password"
            v-model="newPassword"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />

          <ul class="text-xs mt-2 space-y-1">
            <li :class="validationClass(passwordChecks.minLength, newPassword.length > 0)">
              <span class="font-semibold">{{ passwordChecks.minLength ? '✓' : '✗' }}</span>
              8 caractères minimum
            </li>
            <li :class="validationClass(passwordChecks.hasUppercase, newPassword.length > 0)">
              <span class="font-semibold">{{ passwordChecks.hasUppercase ? '✓' : '✗' }}</span>
              Au moins une majuscule
            </li>
            <li :class="validationClass(passwordChecks.hasDigit, newPassword.length > 0)">
              <span class="font-semibold">{{ passwordChecks.hasDigit ? '✓' : '✗' }}</span>
              Au moins un chiffre
            </li>
            <li :class="validationClass(passwordChecks.hasSpecialChar, newPassword.length > 0)">
              <span class="font-semibold">{{ passwordChecks.hasSpecialChar ? '✓' : '✗' }}</span>
              Au moins un caractère spécial
            </li>
          </ul>
        </div>

        <div>
          <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
          <input
            id="confirmPassword"
            name="confirmPassword"
            type="password"
            v-model="confirmPassword"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
          <p class="text-xs mt-2"
             :class="validationClass(passwordsMatch, confirmPassword.length > 0)">
            <span class="font-semibold">{{ passwordsMatch ? '✓' : '✗' }}</span>
            Les mots de passe doivent être identiques
          </p>
        </div>

        <p
          v-if="localError || authStore.error"
          class="text-sm font-medium text-red-600 p-2 bg-red-50 border border-red-200 rounded-lg"
        >
          {{ localError || authStore.error }}
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
            Réinitialisation...
          </span>
          <span v-else>Réinitialiser le mot de passe</span>
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
</style>
