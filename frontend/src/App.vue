<script setup>
import { ref, onMounted } from 'vue'
import { emitter } from './event-bus'

const msg = ref('')
const isPopup = ref(false)

function showPopup(text) {
  msg.value = text
  isPopup.value = true
  console.log("HEREEE", msg.value, isPopup.value)
  setTimeout(function() {
    isPopup.value = false
  },5000)
}

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user'))
  if (!user) return

  window.Echo.private(`user.${user.id}`)
    .listen('.money.transferred', (e) => {
      let message = ''
      if (user.id === e.transaction.sender_id) {
        message = `You sent ₹${e.transaction.amount}`
      } else {
        message = `You received ₹${e.transaction.amount}`
      }

      // Optionally refresh transaction list
      showPopup(message);
      emitter.emit('transaction-event', e.transaction)
    })
})
</script>

<template>
    <router-view /> 
    <div v-show="isPopup" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="flex items-center space-x-2 p-4 w-64 bg-green-100 text-green-800 rounded shadow-lg">
        <!-- Check mark icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        
        <!-- Message -->
        <span class="font-semibold text-sm">
          {{ msg }}
        </span>
      </div>
    </div>

</template>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
