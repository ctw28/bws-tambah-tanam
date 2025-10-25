<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\P3a;

class P3aController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 25);

        $query = P3a::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        // ✅ jika per_page = "all", ambil semua data tanpa pagination
        if ($perPage === 'all' || $perPage == 0) {
            $data = $query->orderBy('id', 'desc')->get();
        } else {
            // pastikan perPage berupa integer
            $perPage = (int) $perPage;
            $data = $query->orderBy('id', 'desc')->paginate($perPage);
        }

        return response()->json($data);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
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
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $p3a->update($validated);
        return response()->json($p3a);
    }

    public function destroy(P3a $p3a)
    {
        $p3a->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
