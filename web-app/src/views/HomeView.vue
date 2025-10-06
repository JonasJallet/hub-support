<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore.ts'
import SupportCase from '@/components/SupportCase.vue'

const auth = useAuthStore()
const handleLogout = () => {
  auth.logout()
}
</script>

<template>
  <main class="p-6 flex flex-col gap-6">
    <SupportCase />

    <div v-if="auth.isAuthenticated" class="flex justify-end">
      <button
        @click="handleLogout"
        class="flex items-center gap-2 bg-white text-indigo-600 font-semibold px-4 py-2 m-8 rounded-lg shadow-md hover:bg-indigo-600 hover:text-white transition-colors duration-300"
      >
        <font-awesome-icon icon="sign-out-alt" />
        Déconnexion
      </button>
    </div>
  </main>
</template>
