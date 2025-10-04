<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore.ts'
import { computed } from 'vue'

const auth = useAuthStore()

console.log("toto", auth);

// const userName = computed(() => {
//   if (auth.user?) {
//     return `${auth.user.firstName} ${auth.user.lastName}`
//   }
//   return ''
// })

const handleLogout = () => {
  auth.logout()
}
</script>

<template>
  <header style="display: flex; justify-content: space-between; align-items: center; padding: 1rem;">
    <div>
      <a v-if="auth.isAuthenticated" href="#" @click.prevent="handleLogout">Déconnexion</a>
    </div>

    <div>
<!--      <span>Bienvenue, {{ userName }}</span>-->
    </div>
  </header>

  <main>
  </main>
</template>
