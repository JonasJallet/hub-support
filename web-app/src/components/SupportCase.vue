<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/authStore.ts'
import { useFetch } from '@vueuse/core'

const baseUrl = import.meta.env.VITE_API_URL;
const authStore = useAuthStore()

const supportData = ref({
  subject: '',
  message: ''
})
const fileInputRef = ref<HTMLInputElement | null>(null)
const file = ref<File | null>(null)
const fileError = ref<string | null>(null)
const maxFileSize = 2 * 1024 * 1024 // 2 Mo
const isDragOver = ref(false)

function handleFileSelect(selected: File) {
  fileError.value = null
  if (selected.size > maxFileSize) {
    fileError.value = 'La taille maximale autorisée est de 2 Mo.'
    file.value = null
    return
  }
  const allowedTypes = ['application/pdf', 'text/plain']
  if (
    !allowedTypes.includes(selected.type) &&
    !selected.type.startsWith('image/')
  ) {
    fileError.value = 'Type de fichier non autorisé.'
    file.value = null
    return
  }
  file.value = selected
}

function onDrop(e: DragEvent) {
  e.preventDefault()
  isDragOver.value = false
  const droppedFile = e.dataTransfer?.files?.[0]
  if (droppedFile) {
    handleFileSelect(droppedFile)
  }
}

function onDragOver(e: DragEvent) {
  e.preventDefault()
  isDragOver.value = true
}

function onDragLeave() {
  isDragOver.value = false
}

function triggerFileInput() {
  fileInputRef.value?.click()
}

function onFileChange(e: Event) {
  const target = e.target as HTMLInputElement
  const selectedFile = target.files?.[0]
  if (selectedFile) handleFileSelect(selectedFile)
}

// === Cooldown dynamique ===
const lastSentKey = 'support_case_last_sent'
const canSend = ref(true)
const cooldownRemaining = ref(0) // en secondes
const cooldownDuration = 60 * 60 // 1h en secondes
let intervalId: number | null = null

function updateCooldown() {
  const lastSent = localStorage.getItem(lastSentKey)
  if (lastSent) {
    const diff = Math.floor((Date.now() - parseInt(lastSent)) / 1000)
    const remaining = cooldownDuration - diff
    if (remaining > 0) {
      canSend.value = false
      cooldownRemaining.value = remaining
    } else {
      canSend.value = true
      cooldownRemaining.value = 0
    }
  } else {
    canSend.value = true
    cooldownRemaining.value = 0
  }
}

onMounted(() => {
  updateCooldown()
  intervalId = window.setInterval(updateCooldown, 1000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})

// Format mm:ss
const formattedCooldown = computed(() => {
  const minutes = Math.floor(cooldownRemaining.value / 60)
  const seconds = cooldownRemaining.value % 60
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
})

// Progress pour cercle
const cooldownProgress = computed(() => {
  return ((cooldownDuration - cooldownRemaining.value) / cooldownDuration) * 100
})

const isFormValid = computed(() => {
  return (
    supportData.value.subject.trim() !== '' &&
    supportData.value.message.trim() !== '' &&
    canSend.value &&
    (file.value === null || fileError.value === null)
  )
})

const isLoading = ref(false)
const errorMsg = ref<string | null>(null)
const successMsg = ref<string | null>(null)

async function handleSubmit() {
  if (!isFormValid.value) return

  errorMsg.value = null
  successMsg.value = null
  isLoading.value = true

  const formData = new FormData()
  formData.append('subject', supportData.value.subject)
  formData.append('message', supportData.value.message)
  if (file.value) {
    formData.append('file', file.value)
  }

  const { error } = await useFetch(`${baseUrl}support-cases`, {
    method: 'POST',
    body: formData,
    headers: {
      Authorization: `Bearer ${authStore.token}`,
    }
  }).json()

  if (error.value) {
    isLoading.value = false
    errorMsg.value = error.value?.message || 'Erreur inconnue'
    return
  }

  successMsg.value = 'Votre demande de support a été envoyée avec succès ✅'
  localStorage.setItem(lastSentKey, Date.now().toString())
  updateCooldown()

  supportData.value.subject = ''
  supportData.value.message = ''
  file.value = null
  isLoading.value = false
}
</script>

<template>
  <div class="w-full flex justify-center bg-transparent">
    <div class="w-full max-w-lg bg-white p-8 mt-20 rounded-xl shadow-2xl space-y-6">
      <h2 class="text-3xl font-extrabold text-gray-900 text-center">
        Ticket de support
      </h2>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
          <label for="subject" class="block text-sm font-medium text-gray-700">Sujet</label>
          <input
            id="subject"
            v-model="supportData.subject"
            type="text"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
          <textarea
            id="message"
            v-model="supportData.message"
            required
            rows="4"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          ></textarea>
        </div>

        <div
          class="mt-4 p-4 border-2 border-dashed rounded-lg text-center cursor-pointer transition"
          :class="isDragOver ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
          @drop="onDrop"
          @dragover="onDragOver"
          @dragleave="onDragLeave"
          @click="triggerFileInput"
        >
          <input ref="fileInputRef" type="file" class="hidden" @change="onFileChange" />
          <p v-if="!file" class="text-gray-500 text-sm">
            Glissez-déposez un fichier ici ou cliquez pour parcourir<br />
            <span class="text-xs">(PDF, image ou texte – max 2 Mo)</span>
          </p>
          <p v-else class="text-sm font-medium text-gray-700">
            📎 {{ file.name }} ({{ (file.size / 1024).toFixed(1) }} Ko)
          </p>
        </div>
        <p v-if="fileError" class="text-sm text-red-600">{{ fileError }}</p>

        <!-- Messages -->
        <p v-if="errorMsg" class="text-sm font-medium text-red-600 p-2 bg-red-50 border border-red-200 rounded-lg">
          {{ errorMsg }}
        </p>
        <p v-if="successMsg" class="text-sm font-medium text-green-600 p-2 bg-green-50 border border-green-200 rounded-lg">
          {{ successMsg }}
        </p>

        <div v-if="!canSend" class="flex flex-col items-center space-y-2">
          <p class="text-xs text-gray-500">
            ⏳ Vous pourrez renvoyer une demande dans
          </p>
          <div class="relative w-16 h-16">
            <svg class="transform -rotate-90 w-16 h-16">
              <circle cx="32" cy="32" r="28" stroke="gray" stroke-width="4" fill="none" class="opacity-20" />
              <circle
                cx="32"
                cy="32"
                r="28"
                stroke="indigo"
                stroke-width="4"
                fill="none"
                stroke-linecap="round"
                :stroke-dasharray="2 * Math.PI * 28"
                :stroke-dashoffset="(1 - cooldownProgress / 100) * 2 * Math.PI * 28"
                class="transition-all duration-300 ease-linear"
              />
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-xs font-mono text-gray-600">
              {{ formattedCooldown }}
            </span>
          </div>
        </div>

        <button
          type="submit"
          :disabled="!isFormValid || isLoading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition duration-150 ease-in-out"
          :class="{
            'bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500': isFormValid && !isLoading,
            'bg-indigo-400 cursor-not-allowed': !isFormValid || isLoading
          }"
        >
          <span v-if="isLoading" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Envoi...
          </span>
          <span v-else>Envoyer</span>
        </button>
      </form>
    </div>
  </div>
</template>
