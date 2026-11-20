import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

import LoginView from '../views/LoginView.vue';
import UsersView from '../views/UsersView.vue';
import BarangMasukView from '../views/BarangMasukView.vue';
import BarangKeluarView from '../views/BarangKeluarView.vue';
import PermintaanBarangView from '../views/PermintaanBarangView.vue';

const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guestOnly: true },
  },
  {
    path: '/',
    redirect: (to) => {
      const auth = useAuthStore();
      if (auth.isSuperAdmin) {
        return { name: 'users' };
      }
      return { name: 'barang-masuk' };
    },
  },
  {
    path: '/users',
    name: 'users',
    component: UsersView,
    meta: { requiresAuth: true, superAdminOnly: true },
  },
  {
    path: '/barang-masuk',
    name: 'barang-masuk',
    component: BarangMasukView,
    meta: { requiresAuth: true },
  },
  {
    path: '/barang-keluar',
    name: 'barang-keluar',
    component: BarangKeluarView,
    meta: { requiresAuth: true },
  },
  {
    path: '/permintaan-barang',
    name: 'permintaan-barang',
    component: PermintaanBarangView,
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Jika ada token tersimpan tapi data user belum di-load
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser();
  }

  const isAuthenticated = authStore.isAuthenticated;

  // Halaman khusus tamu (contoh: /login)
  if (to.meta.guestOnly && isAuthenticated) {
    if (authStore.isSuperAdmin) {
      return next({ name: 'users' });
    }
    return next({ name: 'barang-masuk' });
  }

  // Halaman yang butuh login
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({ name: 'login' });
  }

  // Halaman khusus superadmin
  if (to.meta.superAdminOnly && !authStore.isSuperAdmin) {
    return next({ name: 'barang-masuk' });
  }

  next();
});

export default router;
