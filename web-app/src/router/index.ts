import {
  createRouter,
  createWebHistory,
  type NavigationGuardNext,
  type RouteLocationNormalized,
} from 'vue-router'
import { useAuthStore } from '@/stores/authStore.ts'
import HomeView from '@/views/HomeView.vue'
import AuthLoginView from '@/pages/auth/AuthLogin.vue';
import AuthRegistrationView from '@/pages/auth/AuthRegistration.vue';

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
      component: AuthRegistrationView,
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

    const publicPages = ['/login', '/registration'];

    if (authStore.isAuthenticated && publicPages.includes(to.path)) {
      return next("/");
    }

    console.log("To:", to.path);
    console.log("Is Authenticated:", authStore.isAuthenticated);

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
