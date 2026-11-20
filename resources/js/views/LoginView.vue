<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
      <!-- Logo Brand -->
      <div class="flex flex-col items-center mb-6">
        <img src="/images/logo-hsk.png" alt="HSK Secret" class="h-16 w-auto object-contain mb-3" />
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">WMS HSK Secret</h1>
        <p class="text-xs text-slate-500 font-medium">Warehouse Management System Multi-Cabang</p>
      </div>

      <!-- Alert Error -->
      <div v-if="errorMessage" class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Form Login -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alamat Email</label>
          <input
            v-model="email"
            type="email"
            required
            placeholder="contoh: ruangdoktercianjur@wms.com"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-sm text-slate-900 outline-none transition"
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kata Sandi</label>
          <input
            v-model="password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-sm text-slate-900 outline-none transition"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-2 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm shadow-md shadow-blue-500/25 transition flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
        >
          <svg v-if="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          <span>{{ loading ? 'Memverifikasi...' : 'Masuk ke Sistem' }}</span>
        </button>
      </form>

      <!-- Info Preset Akun Cepat untuk Pengujian -->
      <div class="mt-6 pt-5 border-t border-slate-100">
        <p class="text-[11px] font-semibold text-slate-400 text-center uppercase tracking-wider mb-2">Preset Akun Pengujian</p>
        <div class="grid grid-cols-2 gap-1.5 text-[10px]">
          <button @click="quickFill('gudangpusat@wms.com', 'password123')" type="button" class="p-1.5 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-medium text-left truncate">
            👑 Super Admin
          </button>
          <button @click="quickFill('ruangdoktercianjur@wms.com', 'hsksecret0208')" type="button" class="p-1.5 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-medium text-left truncate">
            🩺 Dokter Cianjur
          </button>
          <button @click="quickFill('gudangbandung@wms.com', 'hsksecret0208')" type="button" class="p-1.5 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-medium text-left truncate">
            📦 Gudang Bandung
          </button>
          <button @click="quickFill('apotekcianjur@wms.com', 'hsksecret0208')" type="button" class="p-1.5 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-medium text-left truncate">
            💊 Apotek Cianjur
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');

const quickFill = (e, p) => {
  email.value = e;
  password.value = p;
};

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';

  const res = await authStore.login(email.value, password.value);
  loading.value = false;

  if (res.success) {
    if (authStore.isSuperAdmin) {
      router.push('/users');
    } else {
      router.push('/barang-masuk');
    }
  } else {
    errorMessage.value = res.message;
  }
};
</script>
