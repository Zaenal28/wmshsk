<template>
  <div class="space-y-6 w-full">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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

    <!-- Monitoring Expired & Ketentuan Status (Persis Sesuai Gambar Ketentuan Status) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <h2 class="text-sm md:text-base font-bold text-slate-900 tracking-tight">Daftar Master Barang & Monitoring Expired</h2>
          <div class="flex flex-wrap items-center gap-2 mt-2.5 text-xs">
            <span class="text-slate-500 font-medium mr-1">Ketentuan Status:</span>

            <!-- Safe Badge -->
            <button
              type="button"
              @click="toggleStatusFilter('Safe')"
              :class="[
                'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition cursor-pointer',
                statusFilter === 'Safe'
                  ? 'ring-2 ring-[#10b981] bg-[#d1fae5] text-[#047857] border-[#10b981]'
                  : 'bg-[#ecfdf5] text-[#059669] border-[#a7f3d0] hover:bg-[#d1fae5]'
              ]"
              title="Klik untuk filter status Safe"
            >
              <span class="w-2 h-2 rounded-full bg-[#10b981]"></span>
              <span>Safe (≥ 2 Tahun)</span>
              <span v-if="countByStatus.Safe > 0" class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white/80 text-emerald-800">
                {{ countByStatus.Safe }}
              </span>
            </button>

            <!-- Warning Badge -->
            <button
              type="button"
              @click="toggleStatusFilter('Warning')"
              :class="[
                'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition cursor-pointer',
                statusFilter === 'Warning'
                  ? 'ring-2 ring-[#f59e0b] bg-[#fef3c7] text-[#b45309] border-[#f59e0b]'
                  : 'bg-[#fffbeb] text-[#d97706] border-[#fde68a] hover:bg-[#fef3c7]'
              ]"
              title="Klik untuk filter status Warning"
            >
              <span class="w-2 h-2 rounded-full bg-[#f59e0b]"></span>
              <span>Warning (Menuju 1 Tahun)</span>
              <span v-if="countByStatus.Warning > 0" class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white/80 text-amber-800">
                {{ countByStatus.Warning }}
              </span>
            </button>

            <!-- Danger Badge -->
            <button
              type="button"
              @click="toggleStatusFilter('Danger')"
              :class="[
                'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition cursor-pointer',
                statusFilter === 'Danger'
                  ? 'ring-2 ring-[#f43f5e] bg-[#fee2e2] text-[#be123c] border-[#f43f5e]'
                  : 'bg-[#fef2f2] text-[#e11d48] border-[#fecdd3] hover:bg-[#fee2e2]'
              ]"
              title="Klik untuk filter status Danger"
            >
              <span class="w-2 h-2 rounded-full bg-[#f43f5e]"></span>
              <span>Danger (Beberapa Bulan Lagi)</span>
              <span v-if="countByStatus.Danger > 0" class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white/80 text-rose-800">
                {{ countByStatus.Danger }}
              </span>
            </button>

            <!-- Expired Badge -->
            <button
              type="button"
              @click="toggleStatusFilter('Expired')"
              :class="[
                'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition cursor-pointer',
                statusFilter === 'Expired'
                  ? 'ring-2 ring-red-400 bg-[#581313] text-white border-white'
                  : 'bg-[#7f1d1d] text-white border-[#991b1b] hover:bg-[#991b1b]'
              ]"
              title="Klik untuk filter status Expired"
            >
              <span class="w-2 h-2 rounded-full bg-[#fca5a5]"></span>
              <span>Expired (Melebihi Tgl Expired)</span>
              <span v-if="countByStatus.Expired > 0" class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white/20 text-white">
                {{ countByStatus.Expired }}
              </span>
            </button>

            <!-- Reset Filter -->
            <button
              v-if="statusFilter"
              @click="statusFilter = ''"
              type="button"
              class="text-xs text-blue-600 hover:text-blue-800 font-semibold underline ml-1"
            >
              ✕ Hapus Filter
            </button>
          </div>
        </div>

        <!-- Search Input -->
        <div class="relative w-full lg:w-72 shrink-0">
          <input
            v-model="search"
            type="text"
            placeholder="Cari SKU, nama, kategori..."
            class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Table Data Barang -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
      <!-- Bar Info Jumlah Data -->
      <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs">
        <span class="text-slate-500">
          Menampilkan: <strong class="text-slate-800">{{ displayBarangs.length }}</strong> dari total <strong class="text-slate-800">{{ barangs.length }}</strong> barang
          <span v-if="statusFilter" class="ml-1 font-semibold text-blue-600">(Filter: Status {{ statusFilter }})</span>
        </span>
      </div>

      <div class="w-full">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3 px-3 w-12 text-center">No</th>
              <th class="py-3 px-3">Nama Barang</th>
              <th class="py-3 px-3 whitespace-nowrap">Tanggal Masuk</th>
              <th class="py-3 px-3 whitespace-nowrap">Expired</th>
              <th class="py-3 px-3">Masa Berlaku</th>
              <th class="py-3 px-3 text-center whitespace-nowrap">Status Expired</th>
              <th class="py-3 px-3">Jenis/Kategori</th>
              <th class="py-3 px-3 text-right whitespace-nowrap">Jumlah</th>
              <th class="py-3 px-3">Supplier</th>
              <th class="py-3 px-3 text-center whitespace-nowrap w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading">
              <td colspan="10" class="py-8 text-center text-slate-400">
                <div class="flex items-center justify-center gap-2">
                  <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <span>Memuat data barang masuk...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="displayBarangs.length === 0">
              <td colspan="10" class="py-8 text-center text-slate-400 italic">
                Tidak ada data barang yang sesuai dengan pencarian atau filter.
              </td>
            </tr>
            <tr v-for="(b, idx) in displayBarangs" :key="b.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-3 text-center font-mono text-slate-400">{{ b.no || idx + 1 }}</td>
              <td class="py-3 px-3 font-bold text-slate-900">
                {{ b.nama_barang }}
              </td>
              <td class="py-3 px-3 tabular-nums text-slate-600 whitespace-nowrap">{{ formatDate(b.tanggal_masuk) }}</td>
              <td class="py-3 px-3 tabular-nums text-slate-600 whitespace-nowrap">{{ formatDate(b.tanggal_expired) }}</td>
              <td class="py-3 px-3 font-medium text-slate-600">{{ b.masa_berlaku || '-' }}</td>
              <td class="py-3 px-3 text-center whitespace-nowrap">
                <!-- Status Badge Sesuai Ketentuan -->
                <span
                  :class="b.status_expired_info?.class || 'bg-slate-100 text-slate-700'"
                  class="px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wide inline-flex items-center gap-1.5 shadow-xs"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: b.status_expired_info?.dot || 'currentColor' }"></span>
                  <span>{{ b.status_expired_info?.label || '-' }}</span>
                </span>
              </td>
              <td class="py-3 px-3 text-slate-600">{{ b.kategori || '-' }}</td>
              <td class="py-3 px-3 text-right font-bold tabular-nums text-slate-900 whitespace-nowrap">
                {{ (b.jumlah || 0).toLocaleString('id-ID') }}
              </td>
              <td class="py-3 px-3 text-slate-600">{{ b.supplier || '-' }}</td>
              <td class="py-3 px-3 text-center whitespace-nowrap">
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
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-bold text-slate-900">{{ isEditing ? 'Edit Barang Masuk' : 'Tambah Barang Masuk' }}</h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="saveBarang" class="p-6 space-y-4 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">No / SKU Barang</label>
              <input v-model="form.no" type="text" placeholder="contoh: 001" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600 font-mono" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Jenis / Kategori</label>
              <input v-model="form.kategori" type="text" placeholder="obat / cream / serum" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Nama Barang *</label>
            <input v-model="form.nama_barang" required type="text" placeholder="contoh: Glution Inject" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Tanggal Masuk *</label>
              <input v-model="form.tanggal_masuk" required type="date" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Tanggal Expired *</label>
              <input v-model="form.tanggal_expired" required type="date" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Jumlah *</label>
              <input v-model.number="form.jumlah" required min="0" type="number" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600 font-bold" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Satuan</label>
              <input v-model="form.satuan" type="text" placeholder="pcs / box / ampul" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
            </div>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Supplier</label>
            <input v-model="form.supplier" type="text" placeholder="bio Kusuma" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none focus:border-blue-600" />
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
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const barangs = ref([]);
const search = ref('');
const statusFilter = ref('');
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const form = ref({
  no: '',
  nama_barang: '',
  tanggal_masuk: new Date().toISOString().substring(0, 10),
  tanggal_expired: '',
  kategori: 'obat',
  jumlah: 1,
  supplier: 'bio Kusuma',
  satuan: 'pcs',
});

// Hitung total item per status
const countByStatus = computed(() => {
  const counts = { Safe: 0, Warning: 0, Danger: 0, Expired: 0 };
  barangs.value.forEach((b) => {
    const lbl = b.status_expired_info?.label;
    if (counts[lbl] !== undefined) {
      counts[lbl]++;
    }
  });
  return counts;
});

// Filter list berdasarkan teks pencarian dan filter status (jika dipilih)
const displayBarangs = computed(() => {
  return barangs.value.filter((b) => {
    // 1. Filter Status
    if (statusFilter.value && b.status_expired_info?.label !== statusFilter.value) {
      return false;
    }
    // 2. Filter Search query
    if (search.value.trim()) {
      const q = search.value.trim().toLowerCase();
      const matchName = b.nama_barang?.toLowerCase().includes(q);
      const matchNo = b.no?.toLowerCase().includes(q);
      const matchKat = b.kategori?.toLowerCase().includes(q);
      const matchSup = b.supplier?.toLowerCase().includes(q);
      return matchName || matchNo || matchKat || matchSup;
    }
    return true;
  });
});

function toggleStatusFilter(status) {
  if (statusFilter.value === status) {
    statusFilter.value = '';
  } else {
    statusFilter.value = status;
  }
}

const fetchBarangs = async () => {
  loading.value = true;
  try {
    const res = await api.get('/barangs');
    barangs.value = res.data.data;
  } catch (err) {
    console.error('Gagal mengambil data barang:', err);
  } finally {
    loading.value = false;
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
    supplier: 'bio Kusuma',
    satuan: 'pcs',
  };
  showModal.value = true;
};

const openModalEdit = (b) => {
  isEditing.value = true;
  currentId.value = b.id;
  form.value = {
    no: b.no || '',
    nama_barang: b.nama_barang,
    tanggal_masuk: b.tanggal_masuk ? b.tanggal_masuk.substring(0, 10) : '',
    tanggal_expired: b.tanggal_expired ? b.tanggal_expired.substring(0, 10) : '',
    kategori: b.kategori || '',
    jumlah: b.jumlah,
    supplier: b.supplier || '',
    satuan: b.satuan || 'pcs',
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
    await fetchBarangs();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menyimpan barang.');
  }
};

const deleteBarang = async (b) => {
  if (confirm(`Yakin ingin menghapus ${b.nama_barang}?`)) {
    try {
      await api.delete(`/barangs/${b.id}`);
      await fetchBarangs();
    } catch (err) {
      alert(err.response?.data?.message || 'Gagal menghapus barang.');
    }
  }
};

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

onMounted(() => {
  fetchBarangs();
});
</script>
