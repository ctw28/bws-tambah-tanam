<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaerahIrigasiKecamatan;
use Illuminate\Http\Request;

class DaerahIrigasiKecamatanController extends Controller
{
    // ✅ GET dengan pagination + filter di_id
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $diId    = $request->get('di_id');

        $query = DaerahIrigasiKecamatan::query();
        $query->withCount('desas');
        if ($diId) {
            $query->where('daerah_irigasi_id', $diId);
        }

        $data = $query->orderBy('nama')
            ->paginate($perPage);

        return response()->json($data);
    }

    // ✅ STORE
    public function store(Request $request)
    {
        $request->validate([
            'daerah_irigasi_id' => 'required|exists:daerah_irigasis,id',
            'nama' => 'required|string|max:255'
        ]);

        $data = DaerahIrigasiKecamatan::create([
            'daerah_irigasi_id' => $request->daerah_irigasi_id,
            'nama'              => $request->nama
        ]);

        return response()->json([
            'message' => 'Kecamatan berhasil ditambahkan',
            'data'    => $data
        ]);
    }

    // ✅ SHOW
    public function show($id)
    {
        $data = DaerahIrigasiKecamatan::findOrFail($id);
        return response()->json($data);
    }

    // ✅ UPDATE
    public function update(Request $request, $id)
    {
        $data = DaerahIrigasiKecamatan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $data->update([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Kecamatan berhasil diupdate',
            'data' => $data
        ]);
    }

    // ✅ DELETE
    public function destroy($id)
    {
        $data = DaerahIrigasiKecamatan::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Kecamatan berhasil dihapus'
        ]);
    }
}
