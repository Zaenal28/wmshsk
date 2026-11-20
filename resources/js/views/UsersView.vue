<template>
  <div class="space-y-6 w-full">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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

    <!-- Table Users via DataTables.net -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 overflow-hidden">
      <div v-if="loading" class="py-12 text-center text-slate-400">
        <div class="flex items-center justify-center gap-2">
          <svg class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          <span class="text-xs font-medium">Memuat data pengguna...</span>
        </div>
      </div>

      <div v-show="!loading" class="w-full">
        <DataTable
          :data="users"
          :columns="columns"
          :options="dtOptions"
          class="display stripe hover w-full text-xs"
        >
          <!-- Slot No -->
          <template #col-no="{ rowIndex }">
            <span class="font-mono text-slate-400 font-medium">{{ rowIndex + 1 }}</span>
          </template>

          <!-- Slot Nama -->
          <template #col-name="{ rowData }">
            <span class="font-bold text-slate-900">{{ rowData.name }}</span>
          </template>

          <!-- Slot Email -->
          <template #col-email="{ rowData }">
            <span class="font-mono text-slate-600">{{ rowData.email }}</span>
          </template>

          <!-- Slot Cabang -->
          <template #col-cabang="{ rowData }">
            <span class="font-semibold text-slate-700">{{ rowData.cabang || '-' }}</span>
          </template>

          <!-- Slot Role -->
          <template #col-role="{ rowData }">
            <span :class="getRoleBadge(rowData.role)" class="px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wide uppercase whitespace-nowrap">
              {{ formatRole(rowData.role) }}
            </span>
          </template>

          <!-- Slot PIC -->
          <template #col-pic="{ rowData }">
            <span class="text-slate-600">{{ rowData.pic || '-' }}</span>
          </template>

          <!-- Slot Aksi -->
          <template #col-aksi="{ rowData }">
            <div class="inline-flex items-center gap-1.5 whitespace-nowrap">
              <button
                @click="openModalEdit(rowData)"
                class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] transition cursor-pointer"
              >
                Edit
              </button>
              <button
                v-if="rowData.email !== 'gudangpusat@wms.com' && rowData.email !== 'superadmin@wms.com'"
                @click="deleteUser(rowData)"
                class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition cursor-pointer"
              >
                Hapus
              </button>
            </div>
          </template>
        </DataTable>
      </div>
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
import { DataTable, defaultDtOptions } from '../services/datatables';

const users = ref([]);
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const dtOptions = {
  ...defaultDtOptions,
  searching: true,
  paging: true,
  info: true,
  order: [[0, 'asc']],
};

const columns = [
  { data: null, title: 'No', render: '#col-no', className: 'text-center' },
  { data: 'name', title: 'Nama User / Akun', render: '#col-name' },
  { data: 'email', title: 'Alamat Email', render: '#col-email', className: 'font-mono' },
  { data: 'cabang', title: 'Cabang', render: '#col-cabang' },
  { data: 'role', title: 'Role Hak Akses', render: '#col-role' },
  { data: 'pic', title: 'PIC', render: '#col-pic' },
  { data: null, title: 'Aksi', render: '#col-aksi', orderable: false, searchable: false, className: 'text-center' },
];

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
  loading.value = true;
  try {
    const res = await api.get('/users');
    users.value = res.data.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
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
