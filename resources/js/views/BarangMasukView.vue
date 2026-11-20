<template>
  <div class="p-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Barang Masuk</h1>
          <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
            {{ authStore.userCabang }}
          </span>
        </div>
        <p class="text-xs text-slate-500 mt-1">Daftar stok inventori barang masuk cabang beserta pantauan masa berlaku & expired date.</p>
      </div>

      <button
        @click="openModalCreate"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Barang Masuk</span>
      </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-3">
      <div class="relative w-full max-w-md">
        <input
          v-model="search"
          @input="fetchBarangs"
          type="text"
          placeholder="Cari SKU atau nama barang..."
          class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none"
        />
        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <div class="text-xs font-semibold text-slate-500">
        Total: <span class="text-slate-900 font-bold">{{ barangs.length }}</span> barang
      </div>
    </div>

    <!-- Table Data Barang -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">
            <th class="py-3 px-4 w-12 text-center">No</th>
            <th class="py-3 px-4">Nama Barang</th>
            <th class="py-3 px-4">Tanggal Masuk</th>
            <th class="py-3 px-4">Expired</th>
            <th class="py-3 px-4">Masa Berlaku</th>
            <th class="py-3 px-4 text-center">Status Expired</th>
            <th class="py-3 px-4">Jenis/Kategori</th>
            <th class="py-3 px-4 text-right">Jumlah</th>
            <th class="py-3 px-4">Supplier</th>
            <th class="py-3 px-4 text-center w-28">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 whitespace-nowrap">
          <tr v-for="(b, idx) in barangs" :key="b.id" class="hover:bg-slate-50/50 transition">
            <td class="py-3 px-4 text-center font-mono text-slate-400">{{ b.no || idx + 1 }}</td>
            <td class="py-3 px-4 font-bold text-slate-900">
              {{ b.nama_barang }}
            </td>
            <td class="py-3 px-4 tabular-nums text-slate-600">{{ b.tanggal_masuk_formatted }}</td>
            <td class="py-3 px-4 tabular-nums text-slate-600">{{ b.expired_formatted }}</td>
            <td class="py-3 px-4 font-medium text-slate-600">{{ b.masa_berlaku }}</td>
            <td class="py-3 px-4 text-center">
              <span
                :class="b.status_expired_info?.class || 'bg-slate-100 text-slate-700'"
                class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider inline-block"
              >
                {{ b.status_expired_info?.label || 'Aman' }}
              </span>
            </td>
            <td class="py-3 px-4 text-slate-600">{{ b.kategori || '-' }}</td>
            <td class="py-3 px-4 text-right font-bold tabular-nums text-slate-900">
              {{ b.jumlah.toLocaleString('id-ID') }}
            </td>
            <td class="py-3 px-4 text-slate-600">{{ b.supplier || '-' }}</td>
            <td class="py-3 px-4 text-center">
              <div class="inline-flex items-center gap-1.5">
                <button
                  @click="openModalEdit(b)"
                  class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] transition"
                >
                  Edit
                </button>
                <button
                  @click="deleteBarang(b)"
                  class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="barangs.length === 0">
            <td colspan="10" class="py-8 text-center text-slate-400 text-xs font-medium">Belum ada barang masuk untuk cabang ini.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form (Tambah / Edit Barang) -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg my-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-bold text-slate-900">{{ isEditing ? 'Edit Data Barang Masuk' : 'Tambah Barang Masuk' }}</h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="saveBarang" class="p-6 space-y-4 text-xs">
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">No / SKU</label>
              <input v-model="form.no" type="text" placeholder="001" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600 font-mono" />
            </div>
            <div class="col-span-2">
              <label class="block font-bold text-slate-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
              <input v-model="form.nama_barang" required type="text" placeholder="Glution Inject / HSK Secret..." class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Tanggal Masuk <span class="text-rose-500">*</span></label>
              <input v-model="form.tanggal_masuk" required type="date" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Tanggal Expired <span class="text-rose-500">*</span></label>
              <input v-model="form.tanggal_expired" required type="date" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Jenis / Kategori</label>
              <input v-model="form.kategori" type="text" placeholder="obat / krim / pembersih" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
              <input v-model.number="form.jumlah" required type="number" min="0" placeholder="10" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600 font-bold" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Supplier</label>
              <input v-model="form.supplier" type="text" placeholder="PT BIO KUSUMA" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Satuan</label>
              <input v-model="form.satuan" type="text" placeholder="pcs / botol / box" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
            <button @click="showModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold transition">Batal</button>
            <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition">Simpan Barang</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const barangs = ref([]);
const search = ref('');
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const form = ref({
  no: '',
  nama_barang: '',
  tanggal_masuk: new Date().toISOString().substring(0, 10),
  tanggal_expired: '',
  kategori: '',
  jumlah: 1,
  supplier: 'PT BIO KUSUMA',
  satuan: 'pcs',
});

const fetchBarangs = async () => {
  try {
    const res = await api.get('/barangs', { params: { search: search.value } });
    barangs.value = res.data.data;
  } catch (err) {
    console.error(err);
  }
};

const openModalCreate = () => {
  isEditing.value = false;
  currentId.value = null;
  form.value = {
    no: '',
    nama_barang: '',
    tanggal_masuk: new Date().toISOString().substring(0, 10),
    tanggal_expired: '',
    kategori: 'obat',
    jumlah: 1,
    supplier: 'PT BIO KUSUMA',
    satuan: 'pcs',
  };
  showModal.value = true;
};

const openModalEdit = (b) => {
  isEditing.value = true;
  currentId.value = b.id;
  form.value = {
    no: b.no,
    nama_barang: b.nama_barang,
    tanggal_masuk: b.tanggal_masuk,
    tanggal_expired: b.tanggal_expired,
    kategori: b.kategori,
    jumlah: b.jumlah,
    supplier: b.supplier,
    satuan: b.satuan,
  };
  showModal.value = true;
};

const saveBarang = async () => {
  try {
    if (isEditing.value) {
      await api.put(`/barangs/${currentId.value}`, form.value);
    } else {
      await api.post('/barangs', form.value);
    }
    showModal.value = false;
    fetchBarangs();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menyimpan barang.');
  }
};

const deleteBarang = async (b) => {
  if (confirm(`Yakin ingin menghapus ${b.nama_barang}?`)) {
    try {
      await api.delete(`/barangs/${b.id}`);
      fetchBarangs();
    } catch (err) {
      alert(err.response?.data?.message || 'Gagal menghapus barang.');
    }
  }
};

onMounted(() => {
  fetchBarangs();
});
</script>
