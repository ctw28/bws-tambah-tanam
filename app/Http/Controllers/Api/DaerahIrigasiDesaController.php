<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaerahIrigasiDesa;
use Illuminate\Http\Request;

class DaerahIrigasiDesaController extends Controller
{
    // ✅ GET dengan pagination + filter kecamatan_id
    public function index(Request $request)
    {
        $perPage      = $request->get('per_page', 10);
        $kecamatanId  = $request->get('kecamatan_id');

        $query = DaerahIrigasiDesa::query();

        if ($kecamatanId) {
            $query->where('daerah_irigasi_kecamatan_id', $kecamatanId);
        }

        $data = $query->orderBy('nama')
            ->paginate($perPage);

        return response()->json($data);
    }

    // ✅ STORE
    public function store(Request $request)
    {
        $request->validate([
            'daerah_irigasi_kecamatan_id' => 'required|exists:daerah_irigasi_kecamatans,id',
            'nama' => 'required|string|max:255'
        ]);

        $data = DaerahIrigasiDesa::create([
            'daerah_irigasi_kecamatan_id' => $request->daerah_irigasi_kecamatan_id,
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Desa berhasil ditambahkan',
            'data' => $data
        ]);
    }

    // ✅ SHOW
    public function show($id)
    {
        $data = DaerahIrigasiDesa::findOrFail($id);
        return response()->json($data);
    }

    // ✅ UPDATE
    public function update(Request $request, $id)
    {
        $data = DaerahIrigasiDesa::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $data->update([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Desa berhasil diupdate',
            'data' => $data
        ]);
    }

    // ✅ DELETE
    public function destroy($id)
    {
        $data = DaerahIrigasiDesa::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Desa berhasil dihapus'
        ]);
    }
}
