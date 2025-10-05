<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore.ts'

const authStore = useAuthStore()

const registrationData = ref({
  firstName: '',
  lastName: '',
  email: '',
  password: '',
})

const passwordChecks = computed(() => {
  const p = registrationData.value.password
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

const isFormValid = computed(() => {
  const data = registrationData.value
  return (
    data.firstName.trim() !== '' &&
    data.lastName.trim() !== '' &&
    /\S+@\S+\.\S+/.test(data.email) &&
    isPasswordValid.value
  )
})

const handleRegister = async () => {
  if (!isFormValid.value) {
    authStore.error = 'Veuillez vérifier tous les champs et les critères du mot de passe.'
    return
  }

  authStore.error = null

  const payload = {
    email: registrationData.value.email,
    firstName: registrationData.value.firstName,
    lastName: registrationData.value.lastName,
    password: registrationData.value.password,
  }

  await authStore.register(payload)
}

const validationClass = (isValid: boolean, isDirty: boolean) => {
  if (!isDirty) return 'text-gray-400'
  return isValid ? 'text-green-500' : 'text-red-500'
}

onMounted(() => {
  authStore.error = null
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl space-y-6">
      <h2 class="text-3xl font-extrabold text-gray-900 text-center">Créer un compte</h2>

      <form @submit.prevent="handleRegister" class="space-y-6">
        <div>
          <label for="firstName" class="block text-sm font-medium text-gray-700">Prénom</label>
          <input
            id="firstName"
            name="firstName"
            type="text"
            v-model="registrationData.firstName"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="lastName" class="block text-sm font-medium text-gray-700">Nom</label>
          <input
            id="lastName"
            name="lastName"
            type="text"
            v-model="registrationData.lastName"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Adresse Email</label>
          <input
            id="email"
            name="email"
            type="email"
            v-model="registrationData.email"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
          <input
            id="password"
            name="password"
            type="password"
            v-model="registrationData.password"
            required
            :disabled="authStore.isLoading"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />

          <ul class="text-xs mt-2 space-y-1">
            <li
              :class="
                validationClass(passwordChecks.minLength, registrationData.password.length > 0)
              "
            >
              <span class="font-semibold">{{ passwordChecks.minLength ? '✓' : '✗' }}</span> 8
              caractères minimum
            </li>
            <li
              :class="
                validationClass(passwordChecks.hasUppercase, registrationData.password.length > 0)
              "
            >
              <span class="font-semibold">{{ passwordChecks.hasUppercase ? '✓' : '✗' }}</span> Au
              moins une majuscule
            </li>
            <li
              :class="
                validationClass(passwordChecks.hasDigit, registrationData.password.length > 0)
              "
            >
              <span class="font-semibold">{{ passwordChecks.hasDigit ? '✓' : '✗' }}</span> Au moins
              un chiffre
            </li>
            <li
              :class="
                validationClass(passwordChecks.hasSpecialChar, registrationData.password.length > 0)
              "
            >
              <span class="font-semibold">{{ passwordChecks.hasSpecialChar ? '✓' : '✗' }}</span> Au
              moins un caractère spécial
            </li>
          </ul>
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
            Inscription en cours...
          </span>
          <span v-else>S'inscrire</span>
        </button>
      </form>
    </div>
  </div>
</template>
