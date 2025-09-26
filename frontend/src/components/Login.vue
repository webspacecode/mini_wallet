<template>
 <div class="flex items-center justify-center min-h-screen">
  <div class="w-full max-w-sm">
    <p class="text-5xl font-extrabold text-purple-900 text-center tracking-wide drop-shadow-lg select-none mb-20">
      Mini Wallet
    </p>

    <h2 class="text-2xl font-bold text-center">Login Page</h2>
    
    <form class="bg-white p-6 rounded-2xl shadow-lg">
      <div class="p-2">
        <input class="p-2 border w-full border-2 border-purple-300 rounded-md" v-model="email" type="email" placeholder="Email" required />
        <p class="text-sm text-red-900 mt-1" v-if="errors.email">
            {{ errors.email[0] }}
        </p>
      </div>
      <div class="p-2">
        <input class="p-2 border w-full border-2 border-purple-300 rounded-md" v-model="password" type="password" placeholder="Password" required />
        <p class="text-sm text-red-900 mt-1" v-if="errors.email">
            {{ errors.password[0] }}
        </p>
      </div>
      <div class="p-2">
        <button class="bg-purple-900 text-white font-bold p-2 w-full uppercase rounded-md" @click="login">{{actionName}}</button>
      </div>

      <div class="p-2 flex items-center justify-center">
        <router-link class="text-sm text-gray-400 hover:text-purple-700" to="/register">Sign Up Free</router-link>
      </div>
    </form>
  </div>
</div>

</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from "axios"

const router = useRouter()
const email = ref('')
const password = ref('')
const actionName = ref('Login')
const errors = ref({})

function login() {
  const loginUser = async() => {
        try {
            const payload = {
                "email": email.value,
                "password": password.value,
            }

            const response = await axios.post("/api/login", payload)

            const token = response.data.token 
            console.log("Login details: ", response.data)
            if (token) {
                localStorage.setItem("auth_token", token)

                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`

                localStorage.setItem("user", JSON.stringify(response.data.user))

                alert("User registered & logged in successfully!")
            }

        } catch (error) {
            actionName.value = "Login"

            if (error.response) {
                console.error("Error response:", error.response.data)
                errors.value = error.response.data
                alert(error.response.data.message || "Login failed!")
            } else if (error.request) {
                console.error("No response received:", error.request)
                alert("No response from server. Please try again.")
            } else {
                console.error("Error:", error.message)
                alert("An error occurred. Please try again.")
            }
        }
    }
    actionName.value = "Please wait"
    loginUser()
}
</script>
