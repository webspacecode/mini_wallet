import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import axios from 'axios'
import router from './router'
import Echo from "laravel-echo";
import Pusher from "pusher-js";

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || '/api'

window.Pusher = Pusher;

const token = localStorage.getItem('auth_token');

// Listen for broadcasting only if user login 
if (token) {
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        authorizer: (channel, options) => {
            return {
                authorize: (socketId, callback) => {
                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    }, {
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    })
                    .then(response => callback(false, response.data))
                    .catch(error => callback(true, error));
                }
            };
        }
    });
}

const app = createApp(App)

app.use(router)

app.mount('#app')
