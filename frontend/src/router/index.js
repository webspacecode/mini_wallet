import { createRouter, createWebHistory } from 'vue-router'

// Pages
import Login from '@/components/Login.vue'
import Register from '@/components/Register.vue'
import Welcome from '@/components/Welcome.vue'
import Transfer from '@/components/Transfer.vue'


// Check if user is authenticated
function isAuthenticated() {
  return !!localStorage.getItem('auth_token') 
}

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/welcome',
    name: 'Welcome',
    component: Welcome,
    meta: { requiresAuth: true }
  },
  {
    path: '/transfer',
    name: 'Transfer',
    component: Transfer,
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/login'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guard
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    next({ name: 'Login' })
  } else if (to.meta.guest && isAuthenticated()) {
    next({ name: 'Welcome' })
  } else {
    next()
  }
})

export default router