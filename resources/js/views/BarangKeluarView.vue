<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Barang Keluar</h1>
        <p class="text-sm text-slate-500">
          Cabang: <span class="font-semibold text-blue-600">{{ currentCabang }}</span> |
          PIC: <span class="font-semibold text-slate-700">{{ currentPic }}</span>
        </p>
      </div>
      <div>
        <button
          v-if="!showForm"
          @click="openCreateForm"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Catat Barang Keluar
        </button>
        <button
          v-else
          @click="closeForm"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium text-sm rounded-lg transition-colors"
        >
          Tutup Form
        </button>
      </div>
    </div>

    <!-- Alert / Notification -->
    <div
      v-if="message.text"
      :class="[
        'p-4 rounded-lg text-sm flex items-center justify-between',
        message.type === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'
      ]"
    >
      <span>{{ message.text }}</span>
      <button @click="message.text = ''" class="text-xs font-bold underline">Tutup</button>
    </div>

    <!-- Form Tambah / Edit Transaksi Barang Keluar -->
    <div v-if="showForm" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-base font-semibold text-slate-800">
          {{ isEditing ? 'Edit Transaksi Barang Keluar' : 'Form Tambah Barang Keluar' }}
        </h2>
        <span class="text-xs text-slate-500">* Wajib diisi</span>
      </div>

      <form @submit.prevent="saveBarangKeluar" class="p-6 space-y-6">
        <!-- Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Keluar *</label>
            <input
              type="date"
              v-model="formHeader.tanggal_keluar"
              required
              class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">No Surat *</label>
            <input
              type="text"
              v-model="formHeader.no_surat"
              required
              placeholder="Contoh: BK-001/XI/2026"
              class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2 font-mono"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tujuan *</label>
            <div class="space-y-1">
              <select
                v-model="formHeader.tujuan"
                class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2"
              >
                <option value="">-- Pilih Tujuan --</option>
                <option v-for="opt in tujuanPresets" :key="opt" :value="opt">{{ opt }}</option>
                <option value="__custom__">+ Tujuan Lainnya / Ketik Sendiri</option>
              </select>
              <input
                v-if="formHeader.tujuan === '__custom__' || isCustomTujuan"
                type="text"
                v-model="customTujuanInput"
                placeholder="Ketik nama tujuan..."
                class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2 mt-1"
                required
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">PIC (Penanggung Jawab) *</label>
            <input
              type="text"
              v-model="formHeader.pic"
              required
              class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2"
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Penerima (Admin Cabang / Penerima)</label>
            <input
              type="text"
              v-model="formHeader.nama_penerima"
              placeholder="Nama staf penerima / pasien"
              class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2"
            />
          </div>
        </div>

        <hr class="border-slate-200" />

        <!-- Input Item Barang -->
        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">
              {{ editingItemIndex >= 0 ? 'Edit Item Barang' : 'Tambah Item ke Daftar Keluar' }}
            </h3>
            <span v-if="editingItemIndex >= 0" class="text-xs text-amber-600 font-medium">
              Sedang mengedit item baris ke-{{ editingItemIndex + 1 }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
              <label class="block text-xs font-medium text-slate-600 mb-1">Nama Barang *</label>
              <div class="relative">
                <input
                  type="text"
                  v-model="itemInput.nama_barang"
                  placeholder="Ketik atau pilih barang..."
                  @input="searchBarangStok"
                  class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2"
                />
                <!-- Autocomplete Dropdown dari Stok Cabang -->
                <div
                  v-if="filteredStokList.length > 0"
                  class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                >
                  <div
                    v-for="b in filteredStokList"
                    :key="b.id"
                    @click="selectBarangFromStok(b)"
                    class="p-2 hover:bg-blue-50 cursor-pointer text-xs flex justify-between items-center border-b border-slate-100 last:border-none"
                  >
                    <div>
                      <p class="font-semibold text-slate-800">{{ b.nama_barang }}</p>
                      <p class="text-slate-500 text-[11px]">SKU: {{ b.no || '-' }} | Exp: {{ b.tanggal_expired || '-' }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                      Stok: {{ b.jumlah }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">No Barang / SKU</label>
              <input
                type="text"
                v-model="itemInput.no_barang"
                placeholder="SKU-001"
                class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2 font-mono"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Expired Date</label>
              <input
                type="date"
                v-model="itemInput.tanggal_expired"
                class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Jumlah *</label>
              <input
                type="number"
                v-model.number="itemInput.jumlah"
                min="1"
                placeholder="1"
                class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2 font-bold"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex-1 mr-4">
              <input
                type="text"
                v-model="itemInput.keterangan"
                placeholder="Keterangan tambahan (opsional)..."
                class="w-full text-xs rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border p-2"
              />
            </div>
            <div class="flex gap-2">
              <button
                v-if="editingItemIndex >= 0"
                type="button"
                @click="cancelEditItem"
                class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-medium rounded-lg"
              >
                Batal Edit
              </button>
              <button
                type="button"
                @click="addOrUpdateItem"
                class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg flex items-center gap-1"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ editingItemIndex >= 0 ? 'Simpan Perubahan Item' : 'Masukkan ke Tabel' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Tabel Item Barang yang Ditambahkan -->
        <div class="border border-slate-200 rounded-lg overflow-hidden">
          <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-100 text-slate-700 font-semibold uppercase text-[10px]">
              <tr>
                <th class="px-3 py-2.5 w-12 text-center">No</th>
                <th class="px-3 py-2.5">No Barang / SKU</th>
                <th class="px-3 py-2.5">Nama Barang</th>
                <th class="px-3 py-2.5">Expired Date</th>
                <th class="px-3 py-2.5 text-center">Jumlah Barang</th>
                <th class="px-3 py-2.5">Keterangan</th>
                <th class="px-3 py-2.5 text-center w-28">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-if="formItems.length === 0">
                <td colspan="7" class="px-4 py-6 text-center text-slate-400 italic">
                  Belum ada item barang yang dimasukkan. Silakan isi form di atas lalu klik "Masukkan ke Tabel".
                </td>
              </tr>
              <tr
                v-for="(it, idx) in formItems"
                :key="idx"
                :class="editingItemIndex === idx ? 'bg-amber-50' : 'hover:bg-slate-50'"
              >
                <td class="px-3 py-2 text-center font-mono font-medium">{{ idx + 1 }}</td>
                <td class="px-3 py-2 font-mono text-slate-700">{{ it.no_barang || '-' }}</td>
                <td class="px-3 py-2 font-medium text-slate-800">{{ it.nama_barang }}</td>
                <td class="px-3 py-2">{{ it.tanggal_expired || '-' }}</td>
                <td class="px-3 py-2 text-center font-bold text-blue-600">{{ it.jumlah }}</td>
                <td class="px-3 py-2 text-slate-500">{{ it.keterangan || '-' }}</td>
                <td class="px-3 py-2 text-center space-x-1">
                  <button
                    type="button"
                    @click="editItemRow(idx)"
                    class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded bg-blue-50 hover:bg-blue-100"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    @click="removeItemRow(idx)"
                    class="text-rose-600 hover:text-rose-800 font-medium text-xs px-2 py-1 rounded bg-rose-50 hover:bg-rose-100"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot v-if="formItems.length > 0" class="bg-slate-50 font-semibold text-slate-800">
              <tr>
                <td colspan="4" class="px-3 py-2.5 text-right uppercase text-[10px]">Total Jumlah Barang:</td>
                <td class="px-3 py-2.5 text-center font-bold text-sm text-blue-700">{{ totalItemJumlah }}</td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
          <button
            type="button"
            @click="closeForm"
            class="px-4 py-2 border border-slate-300 text-slate-700 font-medium text-sm rounded-lg hover:bg-slate-50 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="submitting || formItems.length === 0"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-medium text-sm rounded-lg transition-colors flex items-center gap-2"
          >
            <svg v-if="submitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ isEditing ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Data Table Transaksi Barang Keluar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <!-- Search & Refresh bar -->
      <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="relative w-full md:w-80">
          <input
            type="text"
            v-model="searchQuery"
            @input="filterList"
            placeholder="Cari No Surat, Tujuan, PIC..."
            class="w-full text-xs rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 border pl-8 pr-3 py-2"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <div class="text-xs text-slate-500">
          Total Transaksi: <span class="font-bold text-slate-700">{{ listData.length }}</span>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-slate-700 font-semibold uppercase text-[11px] border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 w-12 text-center">No</th>
              <th class="px-4 py-3">No Surat</th>
              <th class="px-4 py-3">Tanggal Keluar</th>
              <th class="px-4 py-3 text-center">Jumlah Barang</th>
              <th class="px-4 py-3">Tujuan</th>
              <th class="px-4 py-3">PIC</th>
              <th class="px-4 py-3 text-center w-48">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading">
              <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                <div class="flex items-center justify-center gap-2">
                  <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <span>Memuat data transaksi...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredList.length === 0">
              <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                Tidak ada data transaksi barang keluar yang ditemukan.
              </td>
            </tr>
            <tr v-for="(row, idx) in filteredList" :key="row.id" class="hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3 text-center font-mono font-medium text-slate-500">{{ idx + 1 }}</td>
              <td class="px-4 py-3 font-semibold text-slate-800 font-mono">{{ row.no_surat }}</td>
              <td class="px-4 py-3 text-slate-600">{{ formatDate(row.tanggal_keluar) }}</td>
              <td class="px-4 py-3 text-center">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  {{ row.total_jumlah }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span class="font-medium text-slate-800">{{ row.tujuan }}</span>
                <span v-if="row.nama_penerima" class="block text-[11px] text-slate-400">
                  Penerima: {{ row.nama_penerima }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-700 font-medium">{{ row.pic }}</td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Cetak PDF Surat Jalan -->
                  <button
                    @click="cetakPdf(row.id)"
                    title="Cetak PDF Surat Jalan"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[11px] font-medium transition-colors"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    PDF
                  </button>
                  <!-- Edit -->
                  <button
                    @click="editTransaksi(row)"
                    title="Edit Transaksi"
                    class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-medium transition-colors"
                  >
                    Edit
                  </button>
                  <!-- Hapus -->
                  <button
                    @click="hapusTransaksi(row)"
                    title="Hapus Transaksi"
                    class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded text-[11px] font-medium transition-colors"
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();

const listData = ref([]);
const stokList = ref([]);
const filteredStokList = ref([]);
const loading = ref(false);
const submitting = ref(false);
const searchQuery = ref('');
const showForm = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const message = ref({ text: '', type: 'success' });

// Header Form
const formHeader = ref({
  tanggal_keluar: new Date().toISOString().split('T')[0],
  no_surat: '',
  pic: '',
  tujuan: '',
  nama_penerima: '',
});
const customTujuanInput = ref('');
const isCustomTujuan = ref(false);

// Items Form
const formItems = ref([]);
const editingItemIndex = ref(-1);
const itemInput = ref({
  barang_id: null,
  nama_barang: '',
  no_barang: '',
  tanggal_expired: '',
  jumlah: 1,
  keterangan: '',
});

const currentCabang = computed(() => authStore.user?.cabang || 'Semua Cabang');
const currentPic = computed(() => authStore.user?.pic || authStore.user?.name || '-');

// Preset Tujuan berdasarkan Role / Cabang
const tujuanPresets = computed(() => {
  const role = authStore.user?.role || '';
  const cabang = (authStore.user?.cabang || '').toLowerCase();

  if (role === 'ruang_dokter' || cabang.includes('dokter')) {
    return ['Tindakan Dokter', 'Pasien', 'Ibu', 'Bandung', 'PWK'];
  }
  if (role === 'gudang_cabang' || cabang.includes('gudang')) {
    if (cabang.includes('bandung')) return ['Apotek Bandung'];
    if (cabang.includes('purwakarta') || cabang.includes('pwk')) return ['Apotek Purwakarta'];
    return ['Apotek'];
  }
  if (role === 'apotek' || cabang.includes('apotek')) {
    return ['Penjualan'];
  }
  // Superadmin / Pusat
  return ['Tindakan Dokter', 'Penjualan', 'Apotek Bandung', 'Apotek Purwakarta', 'Apotek Cianjur', 'Marketplace', 'Gudang Bandung', 'Gudang PWK', 'Ruang Facial'];
});

const totalItemJumlah = computed(() => {
  return formItems.value.reduce((sum, it) => sum + (parseInt(it.jumlah) || 0), 0);
});

const filteredList = computed(() => {
  if (!searchQuery.value) return listData.value;
  const q = searchQuery.value.toLowerCase();
  return listData.value.filter(
    (row) =>
      row.no_surat?.toLowerCase().includes(q) ||
      row.tujuan?.toLowerCase().includes(q) ||
      row.pic?.toLowerCase().includes(q) ||
      row.nama_penerima?.toLowerCase().includes(q)
  );
});

onMounted(() => {
  fetchListData();
  fetchStokList();
});

async function fetchListData() {
  loading.value = true;
  try {
    const res = await api.get('/barang-keluars');
    if (res.data.success) {
      listData.value = res.data.data;
    }
  } catch (err) {
    console.error('Gagal memuat barang keluar:', err);
  } finally {
    loading.value = false;
  }
}

async function fetchStokList() {
  try {
    const res = await api.get('/barangs');
    if (res.data.success) {
      stokList.value = res.data.data;
    }
  } catch (err) {
    console.error('Gagal memuat stok barang:', err);
  }
}

function searchBarangStok() {
  const q = itemInput.value.nama_barang.trim().toLowerCase();
  if (!q) {
    filteredStokList.value = [];
    return;
  }
  filteredStokList.value = stokList.value.filter(
    (b) =>
      b.nama_barang?.toLowerCase().includes(q) ||
      b.no?.toLowerCase().includes(q)
  ).slice(0, 8);
}

function selectBarangFromStok(b) {
  itemInput.value.barang_id = b.id;
  itemInput.value.nama_barang = b.nama_barang;
  itemInput.value.no_barang = b.no || '';
  itemInput.value.tanggal_expired = b.tanggal_expired || '';
  filteredStokList.value = [];
}

function openCreateForm() {
  isEditing.value = false;
  editingId.value = null;
  formHeader.value = {
    tanggal_keluar: new Date().toISOString().split('T')[0],
    no_surat: 'BK/' + Date.now().toString().slice(-6),
    pic: currentPic.value,
    tujuan: tujuanPresets.value[0] || '',
    nama_penerima: '',
  };
  customTujuanInput.value = '';
  isCustomTujuan.value = false;
  formItems.value = [];
  resetItemInput();
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  isEditing.value = false;
  editingId.value = null;
  resetItemInput();
}

function resetItemInput() {
  itemInput.value = {
    barang_id: null,
    nama_barang: '',
    no_barang: '',
    tanggal_expired: '',
    jumlah: 1,
    keterangan: '',
  };
  editingItemIndex.value = -1;
  filteredStokList.value = [];
}

function addOrUpdateItem() {
  if (!itemInput.value.nama_barang || !itemInput.value.jumlah || itemInput.value.jumlah < 1) {
    alert('Nama barang dan jumlah minimal 1 harus diisi!');
    return;
  }

  const payloadItem = {
    barang_id: itemInput.value.barang_id,
    nama_barang: itemInput.value.nama_barang,
    no_barang: itemInput.value.no_barang || '-',
    tanggal_expired: itemInput.value.tanggal_expired || null,
    jumlah: parseInt(itemInput.value.jumlah),
    keterangan: itemInput.value.keterangan || '',
  };

  if (editingItemIndex.value >= 0) {
    // Update existing item in cart
    formItems.value[editingItemIndex.value] = payloadItem;
  } else {
    // Add new item to cart
    formItems.value.push(payloadItem);
  }

  resetItemInput();
}

function editItemRow(index) {
  editingItemIndex.value = index;
  const target = formItems.value[index];
  itemInput.value = {
    barang_id: target.barang_id,
    nama_barang: target.nama_barang,
    no_barang: target.no_barang,
    tanggal_expired: target.tanggal_expired,
    jumlah: target.jumlah,
    keterangan: target.keterangan,
  };
}

function removeItemRow(index) {
  if (confirm('Hapus item ini dari daftar?')) {
    formItems.value.splice(index, 1);
    if (editingItemIndex.value === index) {
      resetItemInput();
    }
  }
}

function cancelEditItem() {
  resetItemInput();
}

async function saveBarangKeluar() {
  if (formItems.value.length === 0) {
    alert('Masukkan minimal 1 item barang!');
    return;
  }

  let finalTujuan = formHeader.value.tujuan;
  if (finalTujuan === '__custom__' || isCustomTujuan.value) {
    finalTujuan = customTujuanInput.value;
  }

  if (!finalTujuan) {
    alert('Tujuan barang keluar harus dipilih atau diisi!');
    return;
  }

  const payload = {
    tanggal_keluar: formHeader.value.tanggal_keluar,
    no_surat: formHeader.value.no_surat,
    pic: formHeader.value.pic,
    tujuan: finalTujuan,
    nama_penerima: formHeader.value.nama_penerima,
    items: formItems.value,
  };

  submitting.value = true;
  try {
    let res;
    if (isEditing.value) {
      res = await api.put(`/barang-keluars/${editingId.value}`, payload);
    } else {
      res = await api.post('/barang-keluars', payload);
    }

    if (res.data.success) {
      message.value = {
        type: 'success',
        text: res.data.message || 'Transaksi berhasil disimpan!',
      };
      closeForm();
      await fetchListData();
      await fetchStokList();
    }
  } catch (err) {
    console.error('Gagal menyimpan transaksi:', err);
    message.value = {
      type: 'error',
      text: err.response?.data?.message || 'Terjadi kesalahan saat menyimpan transaksi.',
    };
  } finally {
    submitting.value = false;
  }
}

function editTransaksi(row) {
  isEditing.value = true;
  editingId.value = row.id;

  const isPreset = tujuanPresets.value.includes(row.tujuan);
  formHeader.value = {
    tanggal_keluar: row.tanggal_keluar,
    no_surat: row.no_surat,
    pic: row.pic,
    tujuan: isPreset ? row.tujuan : '__custom__',
    nama_penerima: row.nama_penerima || '',
  };

  if (!isPreset) {
    isCustomTujuan.value = true;
    customTujuanInput.value = row.tujuan;
  } else {
    isCustomTujuan.value = false;
    customTujuanInput.value = '';
  }

  formItems.value = (row.items || []).map((it) => ({
    barang_id: it.barang_id,
    nama_barang: it.nama_barang,
    no_barang: it.no_barang || '-',
    tanggal_expired: it.tanggal_expired || '',
    jumlah: it.jumlah,
    keterangan: it.keterangan || '',
  }));

  resetItemInput();
  showForm.value = true;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function hapusTransaksi(row) {
  if (!confirm(`Hapus transaksi surat "${row.no_surat}"? Stok barang akan dikembalikan.`)) {
    return;
  }

  try {
    const res = await api.delete(`/barang-keluars/${row.id}`);
    if (res.data.success) {
      message.value = {
        type: 'success',
        text: res.data.message || 'Transaksi berhasil dihapus dan stok dikembalikan.',
      };
      await fetchListData();
      await fetchStokList();
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menghapus transaksi.');
  }
}

function cetakPdf(id) {
  const token = authStore.token;
  window.open(`/print/barang-keluar/${id}?token=${token}`, '_blank');
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>
