<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermintaanBarang;
use App\Models\PermintaanBarangItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermintaanBarangController extends Controller
{
    /**
     * List Permintaan Barang
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $query = PermintaanBarang::with('items');

        // Jika cabang, hanya lihat permintaan dari cabangnya
        if ($user && $user->role !== 'superadmin' && $user->role !== 'gudang_pusat') {
            $query->where('cabang', $user->cabang);
        }

        if ($request->search) {
            $term = "%{$request->search}%";
            $query->where(function ($q) use ($term) {
                $q->where('no_surat', 'like', $term)
                    ->orWhere('cabang', 'like', $term)
                    ->orWhere('pic', 'like', $term);
            });
        }

        $list = $query->orderBy('tanggal_permintaan', 'desc')->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $list,
        ]);
    }

    /**
     * Generate Next No Surat Permintaan
     */
    public function nextNoSurat(Request $request): JsonResponse
    {
        $tanggal = $request->query('tanggal', now()->format('Y-m-d'));
        $noSurat = PermintaanBarang::generateNoSurat($tanggal);
        return response()->json([
            'success' => true,
            'no_surat' => $noSurat,
        ]);
    }

    /**
     * Store Permintaan Barang
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $validated = $request->validate([
            'tanggal_permintaan' => 'required|date',
            'no_surat' => 'required|string|max:100|unique:permintaan_barangs,no_surat',
            'cabang' => 'required|string|max:150',
            'pic' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'nullable|integer',
            'items.*.nama_barang' => 'required|string|max:150',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $totalQty = collect($validated['items'])->sum('jumlah');

            $permintaan = PermintaanBarang::create([
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'no_surat' => $validated['no_surat'],
                'cabang' => $validated['cabang'],
                'pic' => $validated['pic'],
                'jumlah_barang' => $totalQty,
                'status' => 'Menunggu',
                'user_id' => $user?->id,
            ]);

            foreach ($validated['items'] as $it) {
                PermintaanBarangItem::create([
                    'permintaan_barang_id' => $permintaan->id,
                    'barang_id' => $it['barang_id'] ?? null,
                    'nama_barang' => $it['nama_barang'],
                    'jumlah' => (int) $it['jumlah'],
                    'satuan' => 'pcs',
                    'catatan' => $it['keterangan'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Surat Permintaan Barang berhasil disimpan.',
                'data' => $permintaan->load('items'),
            ], 201);
        });
    }

    /**
     * Update Permintaan Barang
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $permintaan = PermintaanBarang::findOrFail($id);

        $validated = $request->validate([
            'tanggal_permintaan' => 'required|date',
            'no_surat' => 'required|string|max:100|unique:permintaan_barangs,no_surat,' . $id,
            'cabang' => 'required|string|max:150',
            'pic' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'nullable|integer',
            'items.*.nama_barang' => 'required|string|max:150',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($permintaan, $validated) {
            $totalQty = collect($validated['items'])->sum('jumlah');

            $permintaan->update([
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'no_surat' => $validated['no_surat'],
                'cabang' => $validated['cabang'],
                'pic' => $validated['pic'],
                'jumlah_barang' => $totalQty,
            ]);

            // Replace items
            $permintaan->items()->delete();

            foreach ($validated['items'] as $it) {
                PermintaanBarangItem::create([
                    'permintaan_barang_id' => $permintaan->id,
                    'barang_id' => $it['barang_id'] ?? null,
                    'nama_barang' => $it['nama_barang'],
                    'jumlah' => (int) $it['jumlah'],
                    'satuan' => 'pcs',
                    'catatan' => $it['keterangan'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Surat Permintaan Barang berhasil diperbarui.',
                'data' => $permintaan->load('items'),
            ]);
        });
    }

    /**
     * Show single record
     */
    public function show(int $id): JsonResponse
    {
        $data = PermintaanBarang::with('items')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Delete record
     */
    public function destroy(int $id): JsonResponse
    {
        $record = PermintaanBarang::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Surat Permintaan Barang berhasil dihapus.',
        ]);
    }
}
