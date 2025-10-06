import {
  createRouter,
  createWebHistory,
  type NavigationGuardNext,
  type RouteLocationNormalized,
} from 'vue-router'
import { useAuthStore } from '@/stores/authStore.ts'
import HomeView from '@/views/HomeView.vue'
import AuthLoginView from '@/pages/auth/AuthLogin.vue';
import UserRegistrationView from '@/pages/user/UserRegistration.vue';
import UserResetPassword from '@/pages/user/UserResetPassword.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: "/login",
      name: "login",
      component: AuthLoginView,
      meta: { requiresAuth: false },
    },
    {
      path: "/registration",
      name: "registration",
      component: UserRegistrationView,
      meta: { requiresAuth: false },
    },
    {
      path: "/reset-password",
      name: "reset-password",
      component: UserResetPassword,
      meta: { requiresAuth: false },
    },
  ],
});

router.beforeEach(
  async (
    to: RouteLocationNormalized,
    from: RouteLocationNormalized,
    next: NavigationGuardNext,
  ) => {
    const authStore = useAuthStore();

    const publicPages = ['/login', '/registration', '/reset-password'];

    if (authStore.isAuthenticated && publicPages.includes(to.path)) {
      return next("/");
    }

    if (
      !authStore.isAuthenticated && !publicPages.includes(to.path)
    ) {
      return next({
        path: "/login",
        query: { redirect: to.fullPath },
      });
    }

    return next();
  },
);

export default router;
