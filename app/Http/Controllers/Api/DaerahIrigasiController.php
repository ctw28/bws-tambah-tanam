<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaerahIrigasi;

class DaerahIrigasiController extends Controller
{
    public function index(Request $request)
    {

        $query = DaerahIrigasi::with(['kabupatens:id,nama']);

        if ($request->has('kabupaten_id')) {
            $query->whereHas('kabupatens', function ($q) use ($request) {
                $q->where('kabupatens.id', $request->kabupaten_id);
            });
        }

        return response()->json($query->get());

        // return DaerahIrigasi::with('kabupatens:id,nama')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kabupaten_ids' => 'required|array'
        ]);

        $di = DaerahIrigasi::create([
            'nama' => $request->nama,
        ]);

        $di->kabupatens()->sync($request->kabupaten_ids);

        return response()->json([
            'message' => 'Daerah Irigasi berhasil ditambahkan',
            'data' => $di->load('kabupatens')
        ], 201);
    }

    public function show(DaerahIrigasi $daerahIrigasi)
    {
        return $daerahIrigasi->load('kabupatens:id,nama');
    }

    public function update(Request $request, DaerahIrigasi $daerahIrigasi)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kabupaten_ids' => 'required|array'
        ]);

        $daerahIrigasi->update([
            'nama' => $request->nama,
        ]);

        $daerahIrigasi->kabupatens()->sync($request->kabupaten_ids);

        return response()->json([
            'message' => 'Daerah Irigasi berhasil diperbarui',
            'data' => $daerahIrigasi->load('kabupatens')
        ]);
    }

    public function destroy(DaerahIrigasi $daerahIrigasi)
    {
        $daerahIrigasi->delete();

        return response()->json(['message' => 'Daerah Irigasi berhasil dihapus']);
    }
}
