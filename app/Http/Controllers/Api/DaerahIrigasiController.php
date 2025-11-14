<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaerahIrigasi;

class DaerahIrigasiController extends Controller
{
    public function index(Request $request)
    {
        // return "aaa";
        // return $request->id;
        $perPage = $request->query('per_page', 25);
        $search = $request->query('search');

        $query = DaerahIrigasi::with([
            'kabupatens:id,nama',
            'parent:id,nama',
            'children',
            'salurans.petugas',
            'upis'
        ]);

        // ✅ Filter berdasarkan ID jika dikirim
        if ($request->has('id')) {
            $data = $query->where('id', $request->id)->first();

            if (!$data) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            return response()->json($data);
        }

        if ($request->has('kabupaten_id')) {
            $query->whereHas('kabupatens', function ($q) use ($request) {
                $q->where('kabupatens.id', $request->kabupaten_id);
            });
        }
        // Filter induk/anak via param child
        // if ($request->has('child')) {
        //     if ($request->child === 'no') {
        //         $query->whereNull('parent_id');
        //     } elseif ($request->child === 'yes') {
        //         $query->whereNotNull('parent_id');
        //     }
        // }

        // Filter induk/anak
        if ($request->boolean('is_induk')) {
            $query->whereNull('parent_id');
        } elseif ($request->boolean('is_child')) {
            $query->whereNotNull('parent_id');
        }

        // Search nama
        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        // Pagination atau semua data
        if ($perPage === 'all' || $perPage == 0) {
            $data = $query->orderBy('id', 'desc')->get();
        } else {
            $data = $query->orderBy('nama', 'asc')->paginate($perPage);
        }

        return response()->json($data);
    }





    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kabupaten_ids' => 'required|array',
            'parent_id' => 'nullable|exists:daerah_irigasis,id', // ✅ validasi parent
            'luas_baku' => 'required|numeric',
            'luas_potensial' => 'required|numeric',
            'luas_fungsional' => 'required|numeric'
        ]);

        $di = DaerahIrigasi::create([
            'nama' => $request->nama,
            'parent_id' => $request->parent_id, // ✅ simpan parent
            'luas_baku' => $request->luas_baku,
            'luas_potensial' => $request->luas_potensial,
            'luas_fungsional' => $request->luas_fungsional,
        ]);

        $di->kabupatens()->sync($request->kabupaten_ids);

        return response()->json([
            'message' => 'Daerah Irigasi berhasil ditambahkan',
            'data' => $di->load('kabupatens', 'parent', 'children')
        ], 201);
    }

    public function show(DaerahIrigasi $daerahIrigasi)
    {
        return $daerahIrigasi->load('kabupatens:id,nama', 'parent:id,nama', 'children:id,nama,parent_id');
    }

    public function update(Request $request, DaerahIrigasi $daerahIrigasi)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kabupaten_ids' => 'required|array',
            'parent_id' => 'nullable|exists:daerah_irigasis,id',
            'luas_baku' => 'required|numeric',
            'luas_potensial' => 'required|numeric',
            'luas_fungsional' => 'required|numeric'

        ]);

        $daerahIrigasi->update([
            'nama' => $request->nama,
            'parent_id' => $request->parent_id,
            'luas_baku' => $request->luas_baku,
            'luas_potensial' => $request->luas_potensial,
            'luas_fungsional' => $request->luas_fungsional,

        ]);

        $daerahIrigasi->kabupatens()->sync($request->kabupaten_ids);

        return response()->json([
            'message' => 'Daerah Irigasi berhasil diperbarui',
            'data' => $daerahIrigasi->load('kabupatens', 'parent', 'children')
        ]);
    }


    public function destroy(DaerahIrigasi $daerahIrigasi)
    {
        $daerahIrigasi->delete();

        return response()->json(['message' => 'Daerah Irigasi berhasil dihapus']);
    }

    public function rekap(Request $request)
    {
        $diId = $request->di_id;

        // Jika user memilih DI tertentu, filter semua entitas berdasar ID tersebut
        if ($diId) {
            $total_saluran = \App\Models\Saluran::where('daerah_irigasi_id', $diId)->count();

            $total_bangunan = \App\Models\Bangunan::whereHas('saluran', function ($q) use ($diId) {
                $q->where('daerah_irigasi_id', $diId);
            })->count();

            $total_petak = \App\Models\Petak::whereHas('bangunan.saluran', function ($q) use ($diId) {
                $q->where('daerah_irigasi_id', $diId);
            })->count();

            $total_pengamat = \App\Models\Pengamat::where('daerah_irigasi_id', $diId)->count();
            $total_juru = \App\Models\Petugas::whereHas('salurans', function ($q) use ($diId) {
                $q->where('daerah_irigasi_id', $diId);
            })->count();


            return response()->json([
                'total_saluran' => $total_saluran,
                'total_bangunan' => $total_bangunan,
                'total_petak' => $total_petak,
                'total_pengamat' => $total_pengamat,
                'total_juru' => $total_juru,
            ]);
        }

        // Jika tidak ada filter, hitung semua data
        return response()->json([
            'total_laporan_valid' => FormPengisian::whereHas('validasi', function ($q) {
                $q->where('pengamat_valid', 1);
            })->count(),
            'total_daerah_irigasi' => \App\Models\DaerahIrigasi::whereNull('parent_id')->count(),
            'total_saluran' => \App\Models\Saluran::count(),
            'total_bangunan' => \App\Models\Bangunan::count(),
            'total_petak' => \App\Models\Petak::count(),
            'total_pengamat' => \App\Models\Pengamat::count(),
            'total_juru' => \App\Models\Petugas::count(),
            'total_p3a' => \App\Models\P3a::count(),
        ]);
    }
}
