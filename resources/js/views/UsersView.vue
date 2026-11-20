<template>
  <div class="p-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Akun Pengguna</h1>
        <p class="text-xs text-slate-500 mt-1">Super Admin Dashboard untuk mengelola akun cabang, role, dan hak akses WMS.</p>
      </div>
      <button
        @click="openModalCreate"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Akun Baru</span>
      </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-6">
      <div class="relative max-w-md">
        <input
          v-model="search"
          @input="fetchUsers"
          type="text"
          placeholder="Cari nama akun, email, atau cabang..."
          class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none"
        />
        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
    </div>

    <!-- Table Users -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <th class="py-3 px-4 w-12 text-center">No</th>
            <th class="py-3 px-4">Nama User / Akun</th>
            <th class="py-3 px-4">Alamat Email</th>
            <th class="py-3 px-4">Cabang</th>
            <th class="py-3 px-4">Role Hak Akses</th>
            <th class="py-3 px-4">PIC</th>
            <th class="py-3 px-4 text-center w-36">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs">
          <tr v-for="(u, index) in users" :key="u.id" class="hover:bg-slate-50/50 transition">
            <td class="py-3 px-4 text-center font-mono text-slate-400">{{ index + 1 }}</td>
            <td class="py-3 px-4 font-bold text-slate-900">{{ u.name }}</td>
            <td class="py-3 px-4 font-mono text-slate-600">{{ u.email }}</td>
            <td class="py-3 px-4 font-semibold text-slate-700">{{ u.cabang || '-' }}</td>
            <td class="py-3 px-4">
              <span :class="getRoleBadge(u.role)" class="px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wide uppercase">
                {{ formatRole(u.role) }}
              </span>
            </td>
            <td class="py-3 px-4 text-slate-600">{{ u.pic || '-' }}</td>
            <td class="py-3 px-4 text-center">
              <div class="inline-flex items-center gap-1.5">
                <button
                  @click="openModalEdit(u)"
                  class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] transition"
                >
                  Edit
                </button>
                <button
                  v-if="u.email !== 'gudangpusat@wms.com' && u.email !== 'superadmin@wms.com'"
                  @click="deleteUser(u)"
                  class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="7" class="py-8 text-center text-slate-400 text-xs font-medium">Tidak ada data pengguna yang ditemukan.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-bold text-slate-900">{{ isEditing ? 'Edit Akun Pengguna' : 'Tambah Akun Pengguna Baru' }}</h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="saveUser" class="p-6 space-y-4 text-xs">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Akun</label>
            <input v-model="form.name" required type="text" placeholder="contoh: Ruang Dokter Cianjur" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Alamat Email</label>
            <input v-model="form.email" required type="email" placeholder="contoh: ruangdoktercianjur@wms.com" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Kata Sandi {{ isEditing ? '(Biarkan kosong jika tidak ingin diubah)' : '' }}</label>
            <input v-model="form.password" :required="!isEditing" type="text" placeholder="••••••••" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600 font-mono" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Role Hak Akses</label>
              <select v-model="form.role" required class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600 bg-white">
                <option value="superadmin">Super Admin / Pusat</option>
                <option value="ruang_dokter">Ruang Dokter</option>
                <option value="gudang_cabang">Gudang Cabang</option>
                <option value="apotek">Apotek</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Cabang</label>
              <input v-model="form.cabang" required type="text" placeholder="contoh: Ruang Dokter Cianjur" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Nama PIC (Penanggung Jawab)</label>
            <input v-model="form.pic" type="text" placeholder="contoh: dr. Rahma / Apoteker Ayu" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
            <button @click="showModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold transition">Batal</button>
            <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition">Simpan Akun</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const users = ref([]);
const search = ref('');
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'ruang_dokter',
  cabang: '',
  pic: '',
  is_active: true,
});

const fetchUsers = async () => {
  try {
    const res = await api.get('/users', { params: { search: search.value } });
    users.value = res.data.data;
  } catch (err) {
    console.error(err);
  }
};

const openModalCreate = () => {
  isEditing.value = false;
  currentId.value = null;
  form.value = {
    name: '',
    email: '',
    password: '',
    role: 'ruang_dokter',
    cabang: '',
    pic: '',
    is_active: true,
  };
  showModal.value = true;
};

const openModalEdit = (u) => {
  isEditing.value = true;
  currentId.value = u.id;
  form.value = {
    name: u.name,
    email: u.email,
    password: '',
    role: u.role,
    cabang: u.cabang,
    pic: u.pic,
    is_active: u.is_active,
  };
  showModal.value = true;
};

const saveUser = async () => {
  try {
    if (isEditing.value) {
      await api.put(`/users/${currentId.value}`, form.value);
    } else {
      await api.post('/users', form.value);
    }
    showModal.value = false;
    fetchUsers();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menyimpan user.');
  }
};

const deleteUser = async (u) => {
  if (confirm(`Yakin ingin menghapus akun ${u.name}?`)) {
    try {
      await api.delete(`/users/${u.id}`);
      fetchUsers();
    } catch (err) {
      alert(err.response?.data?.message || 'Gagal menghapus user.');
    }
  }
};

const formatRole = (r) => {
  const map = {
    superadmin: 'Super Admin',
    gudang_pusat: 'Gudang Pusat',
    ruang_dokter: 'Ruang Dokter',
    gudang_cabang: 'Gudang Cabang',
    apotek: 'Apotek',
  };
  return map[r] || r;
};

const getRoleBadge = (r) => {
  switch (r) {
    case 'superadmin':
    case 'gudang_pusat':
      return 'bg-purple-50 text-purple-700 border border-purple-200';
    case 'ruang_dokter':
      return 'bg-blue-50 text-blue-700 border border-blue-200';
    case 'gudang_cabang':
      return 'bg-amber-50 text-amber-700 border border-amber-200';
    case 'apotek':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
    default:
      return 'bg-slate-50 text-slate-700';
  }
};

onMounted(() => {
  fetchUsers();
});
</script>
