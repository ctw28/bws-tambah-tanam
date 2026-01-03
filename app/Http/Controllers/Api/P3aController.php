<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\P3a;

class P3aController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->query('search');
        $perPage  = $request->query('per_page', 25);
        $diId     = $request->query('daerah_irigasi_id');

        $query = P3a::query()->with('daerahIrigasi');

        if (!empty($search)) {
            $query->where('nama', 'like', "%{$search}%");
        }

        if (!empty($diId)) {
            $query->where('daerah_irigasi_id', $diId);
        }

        $query->orderBy('id', 'desc');

        // 👉 ambil semua (tanpa pagination)
        if ($perPage === 'all') {
            return response()->json([
                'data' => $query->get()
            ]);
        }

        // 👉 pagination normal
        $perPage = max((int) $perPage, 1);

        return response()->json(
            $query->paginate($perPage)
        );
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'daerah_irigasi_id' => 'required|exists:daerah_irigasis,id',
        ]);


        $p3a = P3a::create($validated);
        return response()->json($p3a);
    }

    public function show(P3a $p3a)
    {
        return response()->json($p3a);
    }

    public function update(Request $request, P3a $p3a)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ];

        // Jika data lama (DI masih kosong)
        if (is_null($p3a->daerah_irigasi_id)) {
            $rules['daerah_irigasi_id'] = 'required|exists:daerah_irigasis,id';
        }

        $validated = $request->validate($rules);

        // Kalau sudah ada DI → jangan diubah
        if (!is_null($p3a->daerah_irigasi_id)) {
            unset($validated['daerah_irigasi_id']);
        }

        $p3a->update($validated);

        return response()->json($p3a->load('daerahIrigasi'));
    }


    public function destroy(P3a $p3a)
    {
        $p3a->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
