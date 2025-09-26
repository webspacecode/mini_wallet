<template>
   <div class="flex items-center justify-center">
  <div class="w-full max-w-sm">

   <nav class="fixed top-0 left-0 right-0 bg-white z-50">
      <div class="flex h-16 p-2 justify-between max-w-sm mx-auto shadow-lg rounded-b-lg">
        <p class="text-2xl mt-2 font-extrabold text-purple-900 tracking-wide drop-shadow-lg select-none">
          Mini Wallet
        </p>

        <p 
          @click="logout" 
          class="text-sm mt-3 text-purple-900 tracking-wide drop-shadow-lg select-none cursor-pointer"
        >
          Logout
        </p>
      </div>

      <div class="">
        <div class="flex h-16 p-2 justify-between bg-red-100 max-w-sm mx-auto">
          <p class="text-sm mt-3 font-bold text-black tracking-wide drop-shadow-lg select-none mb-20">
            {{name}}
          </p>

          <p class="text-sm mt-3 font-extrabold text-black tracking-wide drop-shadow-lg select-none mb-20">
            Balance {{balance}}
          </p>
        </div>
      </div>
    </nav>

    

    <div class="pt-31 flex justify-between mt-2" v-if="transactionsData.transaction_history && transactionsData.transaction_history.data && transactionsData.transaction_history.data.length > 0" >
      <button class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50">Prev</button>
      <button class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50">Next</button>
    </div>

    <!-- Cards -->
    <div class="mt-4">
      <div v-if="transactionsData.transaction_history && transactionsData.transaction_history.data && transactionsData.transaction_history.data.length > 0" v-for="transaction in transactionsData.transaction_history.data" :key="transaction.id" class="flex items-center justify-between border border-gray-300 p-4 mt-2 cursor-pointer rounded hover:bg-gray-100">
        <div>
          <div class="p-2 rounded-full bg-gray-500 text-white font-bold" v-if="transactionsData.user.id === transaction.sender.id">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
          </div>
          <div  class="p-2 rounded-full bg-gray-500 text-white font-bold" v-else>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </div>

        </div>
        <div>
          <div v-if="transactionsData.user.id === transaction.sender.id">
            <h3>Paid to</h3>
            <h2>{{transaction.receiver.name}}</h2>
            <h2>{{transaction.receiver.email}}</h2>
            <h2>{{humanReadable(transaction.created_at)}}</h2>
          </div>
          <div v-else>
            <h3>Received From</h3>
            <h2>{{transaction.sender.name}}</h2>
            <h2>{{transaction.sender.email}}</h2>
            <h2>{{humanReadable(transaction.created_at)}}</h2>
          </div>
        </div>

        <div>
          <div>
            <h2>$ {{transaction.amount}}</h2>
          </div>
        </div>

       
      </div>
      <div v-else class="flex items-center justify-center min-h-screen bg-red-50">
        <div>
          <h2 class="text-1xl text-gray-600 font-bold text-center">No Transaction history to show</h2>
        </div>
      </div>
    </div>

    
    <router-link to="/transfer" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 w-full max-w-sm">
      <div class="bg-purple-950 text-white p-4 text-center rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold">Transfer Money</h2>
      </div>
    </router-link>
    
    
  </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from "axios"
import dayjs from "dayjs"
import relativeTime from "dayjs/plugin/relativeTime"

const router = useRouter()

const name = ref('')
const balance = ref('$ 0.00')
const transactionsData = ref({}); 
const errors = ref({})
const currentPage = ref('1')

const user = JSON.parse(localStorage.getItem("user"))
name.value = user.name
balance.value = '$ '+user.balance

// enable the plugin
dayjs.extend(relativeTime)

function humanReadable(dateStr) {
  return dayjs(dateStr).fromNow()
}

function logout() {
  localStorage.removeItem('auth_token')
  router.push({ name: 'Login' })
}

async function fetchTransactions() {
    try {
        const token = localStorage.getItem("auth_token")
        
        const response = await axios.get("/api/transactions", {
            headers: { Authorization: `Bearer ${token}` }
        });
        transactionsData.value = response.data;
        currentPage.value = response.data.transaction_history.current_page;

        console.log(response.data, "GET TRANSACION")
        
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
  window.Echo.private(`user.${user.id}`)
  .listen('.money.transferred', (e) => {
      if (user.id === e.transaction.sender_id) {
        alert(`You sent ₹${e.transaction.amount}`);
      } else {
        alert(`You received ₹${e.transaction.amount}`);
      }

      // Optionally refresh transaction list
      fetchTransactions();
  });

  fetchTransactions()
})
</script>
