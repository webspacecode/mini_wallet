<template>
<div class="flex items-center justify-center">
  <div class="w-full max-w-sm">
    <nav class="shadow-lg flex h-16 p-2 justify-between">
      <p class="text-2xl mt-2 font-extrabold text-purple-900 text-start tracking-wide drop-shadow-lg select-none mb-20">
        Mini Wallet
      </p>

      <p class="flex text-sm mt-3 text-purple-900 text-start tracking-wide drop-shadow-lg select-none mb-20">
       <router-link to="/welcome" class="pr-2 -mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75V21a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 21V9.75z"/>
            </svg>
        </router-link>
        <span @click="logout">
           Logout
        </span>
      </p>
    </nav>
    <div class="flex h-16 p-2 justify-between bg-red-100">
      <p class="text-sm mt-3 font-bold text-black text-start tracking-wide drop-shadow-lg select-none mb-20">
        {{name}}
      </p>

      <p class="text-sm mt-3 font-extrabold text-black text-start tracking-wide drop-shadow-lg select-none mb-20">
        Balance {{balance}}
      </p>
    </div>
    <!-- Search input -->
    <div class="mt-4">
      <input 
        v-model="search"
        placeholder="Search users..."
        class="border border-gray-300 p-2 w-full rounded"
      >
    </div>
    
     <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded shadow-lg w-80 relative">
        <h2 class="text-xl font-bold mb-2">{{ selectedUser.name }}</h2>
        <p class="mb-2">Email: {{ selectedUser.email }}</p>
        <input class="p-2 border w-full border-2 border-purple-300 rounded-md" v-model="amount" type="text" placeholder="Enter Amount" required />
        <p class="text-sm text-red-900 mt-1" v-if="errors.error">
            {{ errors.error }}
        </p>
        <button @click="payAmount()" class="mt-4 px-4 py-2 text-white bg-purple-950 font-bold rounded w-full">Pay</button>
        <button @click="closeModal()" class="mt-4 px-4 py-2 text-white bg-red-950 font-bold rounded w-full">Cancel</button>

      </div>
    </div>

    <!-- Simple pagination buttons -->
    <div class="flex justify-between mt-2">
      <button class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50">Prev</button>
      <button class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50">Next</button>
    </div>

    <!-- Cards -->
    <div class="mt-4">
      <div @click="openModal(user)" v-for="user in filteredUsers" :key="user.id" class="border border-gray-300 p-4 mt-2 cursor-pointer rounded hover:bg-gray-100">
        <h2>Name: {{user.name}}</h2>
        <h2>Email: {{user.email}}</h2>
      </div>
    </div>

  </div>
</div>

</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

import axios from "axios"

const router = useRouter()

const name = ref('')
const balance = ref('$ 0.00')
const users = ref([]); 
const search = ref("");
const showModal = ref(false)          // modal visibility
const selectedUser = ref({}) 
const amount = ref('')
const errors = ref({})


const user = JSON.parse(localStorage.getItem("user"))
name.value = user.name
balance.value = '$ '+user.balance

function logout() {
  localStorage.removeItem('auth_token')
  router.push({ name: 'Login' })
}

const filteredUsers = computed(() => {
  if (!search.value) return users.value;
  return users.value.filter(u =>
    u.name.toLowerCase().includes(search.value.toLowerCase()) ||
    u.email.toLowerCase().includes(search.value.toLowerCase())
  );
});

async function fetchUser() {
    try {
        const token = localStorage.getItem("auth_token")

        const response = await axios.get("/api/users", {
            headers: { Authorization: `Bearer ${token}` }
        });

        users.value = response.data.users.data;
        console.log(response.data.users.data, "USERS")

    } catch (error) {

    }
}

function openModal(user) {
  selectedUser.value = user
  showModal.value = true
}
function closeModal() {
  showModal.value = false
}

async function payAmount() {
    try {
        const token = localStorage.getItem("auth_token")
        const payload = {
            "receiver_id": selectedUser.value.id,
            "amount": amount.value
        }
        const response = await axios.post("/api/transactions", payload, {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log(response.data, "TRANSACION")
        if (response.data.success) {
            router.push({ name: 'Welcome' })
        }
    } catch (error) {
        if (error.response) {
            console.error("Error response:", error.response.data)
            errors.value = error.response.data
        } else if (error.request) {
            console.error("No response received:", error.request)
            alert("No response from server. Please try again.")
        } else {
            console.error("Error:", error.message)
            alert("An error occurred. Please try again.")
        }
    }
} 
onMounted(() => {
  fetchUser()
})
</script>
