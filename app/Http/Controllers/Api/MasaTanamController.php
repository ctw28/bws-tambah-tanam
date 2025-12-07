<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasaTanam;
use Illuminate\Http\Request;

class MasaTanamController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $query = MasaTanam::query(); // ✅ buat query dulu

        // Filter tahun
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        // Sorting
        $data = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan_mulai')
            ->paginate($perPage);

        return response()->json($data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'nama' => 'required|string|max:100',
            'bulan_mulai' => 'required|integer|min:1|max:12',
            'bulan_selesai' => 'required|integer|min:1|max:12',
        ]);

        $data = MasaTanam::create([
            'tahun' => $request->tahun,
            'nama' => $request->nama,
            'bulan_mulai' => $request->bulan_mulai,
            'bulan_selesai' => $request->bulan_selesai,
        ]);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        return MasaTanam::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'nama' => 'required|string|max:100',
            'bulan_mulai' => 'required|integer|min:1|max:12',
            'bulan_selesai' => 'required|integer|min:1|max:12',
        ]);

        $data = MasaTanam::findOrFail($id);
        $data->update([
            'tahun' => $request->tahun,
            'nama' => $request->nama,
            'bulan_mulai' => $request->bulan_mulai,
            'bulan_selesai' => $request->bulan_selesai,
        ]);

        return response()->json($data);
    }

    public function destroy($id)
    {
        MasaTanam::destroy($id);

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
