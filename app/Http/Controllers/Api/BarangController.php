<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    /**
     * List Barang Masuk (Scoped by cabang user)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $query = Barang::query();

        // Jika bukan superadmin, tampilkan barang milik cabang yang login
        if ($user && $user->role !== 'superadmin' && $user->role !== 'gudang_pusat') {
            $query->where('cabang', $user->cabang);
        } elseif ($request->has('cabang') && $request->cabang !== '') {
            $query->where('cabang', $request->cabang);
        }

        if ($request->search) {
            $term = "%{$request->search}%";
            $query->where(function ($q) use ($term) {
                $q->where('nama_barang', 'like', $term)
                    ->orWhere('no', 'like', $term)
                    ->orWhere('kategori', 'like', $term);
            });
        }

        $barangs = $query->orderBy('no', 'asc')->get();

        // Tambahkan atribut masa_berlaku dan status_expired
        $data = $barangs->map(function ($b) {
            return [
                'id' => $b->id,
                'no' => $b->no,
                'nama_barang' => $b->nama_barang,
                'status_racikan' => $b->status_racikan,
                'tanggal_masuk' => $b->tanggal_masuk ? $b->tanggal_masuk->format('Y-m-d') : null,
                'tanggal_masuk_formatted' => $b->tanggal_masuk ? $b->tanggal_masuk->format('d/m/Y') : '-',
                'tanggal_expired' => $b->tanggal_expired ? $b->tanggal_expired->format('Y-m-d') : null,
                'expired_formatted' => $b->tanggal_expired ? $b->tanggal_expired->format('m/Y') : '-',
                'kategori' => $b->kategori,
                'alokasi_penyimpanan' => $b->alokasi_penyimpanan,
                'supplier' => $b->supplier,
                'jumlah' => (int) $b->jumlah,
                'satuan' => $b->satuan ?: 'pcs',
                'harga_barang' => (float) $b->harga_barang,
                'cabang' => $b->cabang,
                'masa_berlaku' => $b->masa_berlaku,
                'status_expired_info' => $b->status_expired_info,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store new Barang
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'no' => 'nullable|string|max:50',
            'status_racikan' => 'nullable|string|max:50',
            'tanggal_masuk' => 'required|date',
            'tanggal_expired' => 'required|date',
            'kategori' => 'nullable|string|max:100',
            'alokasi_penyimpanan' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:50',
            'harga_barang' => 'nullable|numeric|min:0',
            'cabang' => 'nullable|string|max:100',
        ]);

        // Auto no urut jika kosong
        if (empty($validated['no'])) {
            $lastNo = Barang::max('no');
            $next = ((int) $lastNo) + 1;
            $validated['no'] = str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        }

        $validated['cabang'] = ($user && $user->role !== 'superadmin' && $user->role !== 'gudang_pusat')
            ? $user->cabang
            : ($validated['cabang'] ?? 'Gudang Pusat');

        $validated['user_id'] = $user?->id;
        $validated['status_racikan'] = $validated['status_racikan'] ?? 'Non Racikan';

        $barang = Barang::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan.',
            'data' => $barang,
        ], 201);
    }

    /**
     * Show single record
     */
    public function show(int $id): JsonResponse
    {
        $barang = Barang::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $barang,
        ]);
    }

    /**
     * Update Barang
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'no' => 'required|string|max:50',
            'status_racikan' => 'nullable|string|max:50',
            'tanggal_masuk' => 'required|date',
            'tanggal_expired' => 'required|date',
            'kategori' => 'nullable|string|max:100',
            'alokasi_penyimpanan' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:50',
            'harga_barang' => 'nullable|numeric|min:0',
            'cabang' => 'nullable|string|max:100',
        ]);

        $barang->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diperbarui.',
            'data' => $barang,
        ]);
    }

    /**
     * Delete Barang
     */
    public function destroy(int $id): JsonResponse
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dihapus.',
        ]);
    }
}
