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
              class="text-xs text-blue-600 hover:text-blue-800 font-semibold underline ml-1 cursor-pointer"
            >
              ✕ Hapus Filter
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Data Barang via DataTables.net -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 overflow-hidden">
      <div v-if="loading" class="py-12 text-center text-slate-400">
        <div class="flex items-center justify-center gap-2">
          <svg class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          <span class="text-xs font-medium">Memuat data barang masuk...</span>
        </div>
      </div>

      <div v-show="!loading" class="w-full">
        <DataTable
          :data="displayBarangs"
          :columns="columns"
          :options="dtOptions"
          class="display stripe hover w-full text-xs"
        >
          <!-- Slot No -->
          <template #col-no="{ rowData, rowIndex }">
            <span class="font-mono text-slate-400 font-medium">{{ rowData.no || (rowIndex + 1) }}</span>
          </template>

          <!-- Slot Nama Barang -->
          <template #col-nama="{ rowData }">
            <span class="font-bold text-slate-900">{{ rowData.nama_barang }}</span>
          </template>

          <!-- Slot Tanggal Masuk -->
          <template #col-tgl-masuk="{ rowData }">
            <span class="tabular-nums text-slate-600 whitespace-nowrap">{{ formatDate(rowData.tanggal_masuk) }}</span>
          </template>

          <!-- Slot Expired -->
          <template #col-expired="{ rowData }">
            <span class="tabular-nums text-slate-600 whitespace-nowrap">{{ formatDate(rowData.tanggal_expired) }}</span>
          </template>

          <!-- Slot Masa Berlaku -->
          <template #col-masa-berlaku="{ rowData }">
            <span class="font-medium text-slate-600">{{ rowData.masa_berlaku || '-' }}</span>
          </template>

          <!-- Slot Status Expired -->
          <template #col-status="{ rowData }">
            <span
              :class="rowData.status_expired_info?.class || 'bg-slate-100 text-slate-700'"
              class="px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wide inline-flex items-center gap-1.5 shadow-xs whitespace-nowrap"
            >
              <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: rowData.status_expired_info?.dot || 'currentColor' }"></span>
              <span>{{ rowData.status_expired_info?.label || '-' }}</span>
            </span>
          </template>

          <!-- Slot Kategori -->
          <template #col-kategori="{ rowData }">
            <span class="text-slate-600">{{ rowData.kategori || '-' }}</span>
          </template>

          <!-- Slot Jumlah -->
          <template #col-jumlah="{ rowData }">
            <span class="font-bold tabular-nums text-slate-900">{{ (rowData.jumlah || 0).toLocaleString('id-ID') }}</span>
          </template>

          <!-- Slot Supplier -->
          <template #col-supplier="{ rowData }">
            <span class="text-slate-600">{{ rowData.supplier || '-' }}</span>
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
                @click="deleteBarang(rowData)"
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
import { DataTable, defaultDtOptions } from '../services/datatables';

const authStore = useAuthStore();
const barangs = ref([]);
const statusFilter = ref('');
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
  { data: 'no', title: 'No', render: '#col-no', className: 'text-center' },
  { data: 'nama_barang', title: 'Nama Barang', render: '#col-nama' },
  { data: 'tanggal_masuk', title: 'Tanggal Masuk', render: '#col-tgl-masuk' },
  { data: 'tanggal_expired', title: 'Expired', render: '#col-expired' },
  { data: 'masa_berlaku', title: 'Masa Berlaku', render: '#col-masa-berlaku' },
  { data: 'status_expired', title: 'Status Expired', render: '#col-status', className: 'text-center' },
  { data: 'kategori', title: 'Jenis/Kategori', render: '#col-kategori' },
  { data: 'jumlah', title: 'Jumlah', render: '#col-jumlah', className: 'text-right' },
  { data: 'supplier', title: 'Supplier', render: '#col-supplier' },
  { data: null, title: 'Aksi', render: '#col-aksi', orderable: false, searchable: false, className: 'text-center' },
];

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

// Filter list berdasarkan status (DataTables menangani pencarian & paginasi secara mandiri)
const displayBarangs = computed(() => {
  return barangs.value.filter((b) => {
    if (statusFilter.value && b.status_expired_info?.label !== statusFilter.value) {
      return false;
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
