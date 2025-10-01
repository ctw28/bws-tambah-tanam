<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaerahIrigasi;

class DaerahIrigasiController extends Controller
{
    public function index(Request $request)
    {
        $query = DaerahIrigasi::with(['kabupatens:id,nama', 'parent:id,nama', 'children']);

        if ($request->has('kabupaten_id')) {
            $query->whereHas('kabupatens', function ($q) use ($request) {
                $q->where('kabupatens.id', $request->kabupaten_id);
            });
        }
        // filter induk/anak
        if ($request->boolean('is_induk')) {
            $query->whereNull('parent_id');   // hanya induk
        } elseif ($request->boolean('is_child')) {
            $query->whereNotNull('parent_id'); // hanya anak
        }
        return response()->json($query->get());
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kabupaten_ids' => 'required|array',
            'parent_id' => 'nullable|exists:daerah_irigasis,id' // ✅ validasi parent
        ]);

        $di = DaerahIrigasi::create([
            'nama' => $request->nama,
            'parent_id' => $request->parent_id, // ✅ simpan parent
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
            'parent_id' => 'nullable|exists:daerah_irigasis,id'
        ]);

        $daerahIrigasi->update([
            'nama' => $request->nama,
            'parent_id' => $request->parent_id,
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
}