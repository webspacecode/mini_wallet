<template>
 <div class="flex items-center justify-center min-h-screen">
  <div class="w-full max-w-sm">

    <p class="text-5xl font-extrabold text-purple-900 text-center tracking-wide drop-shadow-lg select-none mb-20">
      Mini Wallet
    </p>

    <h2 class="text-2xl font-bold text-center">Register a free account</h2>
    
    <form class="bg-white p-6 rounded-2xl shadow-lg">
      <div class="p-2">
        <input class="p-2 border w-full border-2 border-purple-300 rounded-md" v-model="name" type="text" placeholder="Full Name" required />
        <p class="text-sm text-red-900 mt-1" v-if="errors.name">
            {{ errors.name[0] }}
        </p>
    </div>
      <div class="p-2">
        <input class="p-2 border w-full border-2 border-purple-300 rounded-md" v-model="email" type="email" placeholder="Email" required />
        <p class="text-sm text-red-900 mt-1" v-if="errors.email">
            {{ errors.email[0] }}
        </p>
    </div>
      <div class="p-2">
        <input class="p-2 border w-full border-2 border-purple-300 rounded-md" v-model="password" type="password" placeholder="Password" required />
        <p class="text-sm text-red-900 mt-1" v-if="errors.password">
            {{ errors.password[0] }}
        </p>
    </div>
      <div class="p-2">
        <input class="p-2 border w-full border-2 border-purple-300 rounded-md" v-model="password_confirmation" type="password" placeholder="Confirm Password" required />
        <p class="text-sm text-red-900 mt-1" v-if="errors.password_confirmation">
            {{ errors.password_confirmation[0] }}
        </p>
    </div>
      <div class="p-2">
        <button class="bg-purple-900 text-white font-bold p-2 w-full uppercase rounded-md" @click="register">{{actionName}}</button>
      </div>

      <div class="p-2 flex items-center justify-center">
        <router-link class="text-sm text-gray-400 hover:text-purple-700" to="/login">Sign In Now</router-link>
      </div>
    </form>
  </div>
</div>

</template>

<script setup>
import { ref } from 'vue'
import axios from "axios"
import { useRouter } from 'vue-router'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const actionName = ref('Register')
const errors = ref({})

function register() {
    const registerUser = async() => {
        try {
            const payload = {
                "name": name.value,
                "email": email.value,
                "password": password.value,
                "password_confirmation": password_confirmation.value,
                "login": true
            }

            const response = await axios.post("/api/register", payload)

            const token = response.data.token 
            console.log("Registration details: ", response.data)
            if (token) {
                localStorage.setItem("auth_token", token)

                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`

                localStorage.setItem("user", JSON.stringify(response.data.user))

                alert("You've got $ 1000 balance on registration & logged in successfully!")
            }

        } catch (error) {
            actionName.value = "Register"

            if (error.response) {
                console.error("Error response:", error.response.data)
                errors.value = error.response.data
                alert(error.response.data.message || "Registration failed!")
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
    registerUser()
}
</script>
