<template>
  <div class="min-h-screen bg-slate-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0">
      <!-- Brand Header -->
      <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-100">
        <img src="/images/logo-hsk.png" alt="HSK Logo" class="h-8 w-auto object-contain" />
        <div class="leading-tight">
          <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">HSK Secret</div>
          <div class="text-[11px] font-semibold text-blue-600 truncate max-w-[140px]">{{ authStore.userCabang }}</div>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <!-- Section: Super Admin Menu (Khusus Pengelola Akun) -->
        <div v-if="authStore.isSuperAdmin">
          <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">MASTER & AKUN</div>
          <router-link
            to="/users"
            active-class="bg-blue-50 text-blue-700 font-bold"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-xs font-semibold transition"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>Kelola Akun User</span>
          </router-link>
        </div>

        <!-- Section: Inventori & Transaksi (Khusus Akun Cabang) -->
        <div v-if="!authStore.isSuperAdmin">
          <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventori & Transaksi</div>

          <router-link
            to="/barang-masuk"
            active-class="bg-blue-50 text-blue-700 font-bold"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-xs font-semibold transition"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span>Kelola Barang Masuk</span>
          </router-link>

          <router-link
            to="/barang-keluar"
            active-class="bg-blue-50 text-blue-700 font-bold"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-xs font-semibold transition"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span>Kelola Barang Keluar</span>
          </router-link>

          <router-link
            to="/permintaan-barang"
            active-class="bg-blue-50 text-blue-700 font-bold"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-xs font-semibold transition"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Kelola Permintaan Barang</span>
          </router-link>
        </div>
      </nav>

      <!-- User Profile & Logout Bottom -->
      <div class="p-3 border-t border-slate-100">
        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50">
          <div class="overflow-hidden mr-2">
            <div class="text-xs font-bold text-slate-900 truncate">{{ authStore.user?.name }}</div>
            <div class="text-[10px] text-slate-500 truncate">{{ authStore.user?.email }}</div>
          </div>
          <button
            @click="handleLogout"
            title="Keluar dari akun"
            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 overflow-y-auto">
      <div class="p-4 md:p-6 lg:p-8 w-full">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
