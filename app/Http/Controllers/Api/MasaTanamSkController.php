<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasaTanamSk;
use Illuminate\Http\Request;

class MasaTanamSkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // return $request->all();
        $diID     = $request->get('daerah_irigasi_id');
        $tahunSK  = $request->get('tahun_sk');

        $query = MasaTanamSk::query();

        if ($diID) {
            $query->where('daerah_irigasi_id', $diID);
        }

        if ($tahunSK) {
            $query->where('tahun_sk', $tahunSK);
        }

        return $query->first();
    }


    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'daerah_irigasi_id' => 'required|integer|exists:daerah_irigasis,id',
            'sk_dari'             => 'nullable|string',
            'no_sk'             => 'nullable|string',
            'tahun_sk'          => 'nullable|string|max:4',
            'tanggal_terbit_sk' => 'nullable|date',
        ]);

        $data = MasaTanamSk::create($validated);

        return response()->json([
            'message' => 'Data SK berhasil disimpan.',
            'data' => $data
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return MasaTanamSk::with('daerahIrigasi')->findOrFail($id);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'daerah_irigasi_id' => 'required|integer|exists:daerah_irigasis,id',
            'sk_dari'             => 'nullable|string',
            'no_sk'             => 'nullable|string',
            'tahun_sk'          => 'nullable|string|max:4',
            'tanggal_terbit_sk' => 'nullable|date',
        ]);

        $data = MasaTanamSk::findOrFail($id);
        $data->update($validated);

        return response()->json([
            'message' => 'Data SK berhasil diperbarui.',
            'data' => $data
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        $data = MasaTanamSk::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Data SK berhasil dihapus.'
        ]);
    }
}
