<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore.ts'
import { computed } from 'vue'
import SupportCase from '@/components/SupportCase.vue'

const auth = useAuthStore()

const userName = computed(() => {
  if (auth.user) {
    return `${auth.user.firstName} ${auth.user.lastName}`
  }
  return ''
})

const handleLogout = () => {
  auth.logout()
}
</script>

<template>
  <header style="display: flex; justify-content: space-between; align-items: center; padding: 1rem;">
    <div>
      <span>Bienvenue, {{ userName }}</span>
    </div>

    <div>
      <a v-if="auth.isAuthenticated" href="#" @click.prevent="handleLogout" style="display: flex; align-items: center; gap: 0.5rem;">
        <font-awesome-icon icon="sign-out-alt" /> Déconnexion
      </a>
    </div>
  </header>

  <main>
    <SupportCase />
  </main>
</template>
