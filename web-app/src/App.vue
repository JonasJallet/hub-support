<script setup lang="ts">
import { RouterView } from 'vue-router'
</script>

<template>
  <header class="app-header">
    <h1 class="title">Hub Support</h1>
  </header>

  <main>
    <RouterView />
  </main>
</template>

<style scoped>
/* Header stylisé */
.app-header {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  padding: 1.5rem 2rem;
  text-align: center;
  color: white;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.title {
  font-size: 2rem;
  font-weight: 700;
  letter-spacing: 1px;
  text-shadow: 2px 2px 6px rgba(0,0,0,0.2);
}
</style>
