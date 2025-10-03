import {
  createRouter,
  createWebHistory,
  type NavigationGuardNext,
  type RouteLocationNormalized,
} from 'vue-router'
import { useAuthStore } from '@/stores/authStore.ts'
import HomeView from '@/views/HomeView.vue'

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
      component: () => import("../pages/auth/Login.vue"),
      meta: { requiresAuth: false },
    },
    {
      path: "/registration",
      name: "registration",
      component: () => import("../pages/auth/UserRegistration.vue"),
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

    if (
      !authStore.isAuthenticated && !publicPages.includes(to.path)
    ) {
      return next({
        path: "/registration",
        query: { redirect: to.fullPath },
      });
    }

    return next();
  },
);

export default router;
