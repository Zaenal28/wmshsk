<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    /**
     * List Transaksi Barang Keluar
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $query = BarangKeluar::with('items');

        if ($user && $user->role !== 'superadmin' && $user->role !== 'gudang_pusat') {
            $query->where('cabang_asal', $user->cabang);
        }

        if ($request->search) {
            $term = "%{$request->search}%";
            $query->where(function ($q) use ($term) {
                $q->where('no_surat', 'like', $term)
                    ->orWhere('tujuan', 'like', $term)
                    ->orWhere('pic', 'like', $term);
            });
        }

        $list = $query->orderBy('tanggal_keluar', 'desc')->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $list,
        ]);
    }

    /**
     * Store new Barang Keluar
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $validated = $request->validate([
            'tanggal_keluar' => 'required|date',
            'no_surat' => 'required|string|max:100',
            'pic' => 'required|string|max:100',
            'tujuan' => 'required|string|max:150',
            'nama_penerima' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'nullable|integer',
            'items.*.nama_barang' => 'required|string|max:150',
            'items.*.no_barang' => 'nullable|string|max:50',
            'items.*.tanggal_expired' => 'nullable|date',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        $cabangAsal = ($user && $user->role !== 'superadmin' && $user->role !== 'gudang_pusat')
            ? $user->cabang
            : 'Gudang Pusat';

        return DB::transaction(function () use ($validated, $cabangAsal, $user) {
            $totalQty = collect($validated['items'])->sum('jumlah');

            $header = BarangKeluar::create([
                'tanggal_keluar' => $validated['tanggal_keluar'],
                'no_surat' => $validated['no_surat'],
                'pic' => $validated['pic'],
                'tujuan' => $validated['tujuan'],
                'nama_penerima' => $validated['nama_penerima'] ?? '',
                'total_jumlah' => $totalQty,
                'cabang_asal' => $cabangAsal,
                'user_id' => $user?->id,
            ]);

            foreach ($validated['items'] as $it) {
                BarangKeluarItem::create([
                    'barang_keluar_id' => $header->id,
                    'barang_id' => $it['barang_id'] ?? null,
                    'no_barang' => $it['no_barang'] ?? '-',
                    'nama_barang' => $it['nama_barang'],
                    'tanggal_expired' => $it['tanggal_expired'] ?? null,
                    'jumlah' => (int) $it['jumlah'],
                    'keterangan' => $it['keterangan'] ?? null,
                ]);

                // Kurangi stok barang terkait jika ada id barang
                if (! empty($it['barang_id'])) {
                    Barang::where('id', $it['barang_id'])
                        ->where('jumlah', '>=', (int) $it['jumlah'])
                        ->decrement('jumlah', (int) $it['jumlah']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Barang Keluar berhasil disimpan.',
                'data' => $header->load('items'),
            ], 201);
        });
    }

    /**
     * Show single record
     */
    public function show(int $id): JsonResponse
    {
        $data = BarangKeluar::with('items')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update Barang Keluar
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $header = BarangKeluar::with('items')->findOrFail($id);

        $validated = $request->validate([
            'tanggal_keluar' => 'required|date',
            'no_surat' => 'required|string|max:100',
            'pic' => 'required|string|max:100',
            'tujuan' => 'required|string|max:150',
            'nama_penerima' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'nullable|integer',
            'items.*.nama_barang' => 'required|string|max:150',
            'items.*.no_barang' => 'nullable|string|max:50',
            'items.*.tanggal_expired' => 'nullable|date',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($header, $validated) {
            // 1. Rollback previous stock deductions
            foreach ($header->items as $oldItem) {
                if ($oldItem->barang_id) {
                    Barang::where('id', $oldItem->barang_id)->increment('jumlah', $oldItem->jumlah);
                }
            }

            // 2. Delete old items
            $header->items()->delete();

            // 3. Update header
            $totalQty = collect($validated['items'])->sum('jumlah');
            $header->update([
                'tanggal_keluar' => $validated['tanggal_keluar'],
                'no_surat' => $validated['no_surat'],
                'pic' => $validated['pic'],
                'tujuan' => $validated['tujuan'],
                'nama_penerima' => $validated['nama_penerima'] ?? '',
                'total_jumlah' => $totalQty,
            ]);

            // 4. Create new items and decrement stock
            foreach ($validated['items'] as $it) {
                BarangKeluarItem::create([
                    'barang_keluar_id' => $header->id,
                    'barang_id' => $it['barang_id'] ?? null,
                    'no_barang' => $it['no_barang'] ?? '-',
                    'nama_barang' => $it['nama_barang'],
                    'tanggal_expired' => $it['tanggal_expired'] ?? null,
                    'jumlah' => (int) $it['jumlah'],
                    'keterangan' => $it['keterangan'] ?? null,
                ]);

                if (! empty($it['barang_id'])) {
                    Barang::where('id', $it['barang_id'])
                        ->where('jumlah', '>=', (int) $it['jumlah'])
                        ->decrement('jumlah', (int) $it['jumlah']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Barang Keluar berhasil diperbarui.',
                'data' => $header->load('items'),
            ]);
        });
    }

    /**
     * Delete record
     */
    public function destroy(int $id): JsonResponse
    {
        $record = BarangKeluar::with('items')->findOrFail($id);

        // Kembalikan stok
        foreach ($record->items as $item) {
            if ($item->barang_id) {
                Barang::where('id', $item->barang_id)->increment('jumlah', $item->jumlah);
            }
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi Barang Keluar berhasil dihapus dan stok dikembalikan.',
        ]);
    }
}
