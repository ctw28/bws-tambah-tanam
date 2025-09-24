<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Petak;

class PetakController extends Controller
{
    public function index(Request $request)
    {
        $query = Petak::query();

        if ($request->has('bangunan_id')) {
            $query->where('bangunan_id', $request->bangunan_id);
        }

        return response()->json($query->get(['id', 'bangunan_id', 'nama', 'luas', 'gambar_skema']));
    }


    public function store(Request $request)
    {
        $request->validate([
            'bangunan_id' => 'required|exists:bangunans,id',
            'nama' => 'required|string|max:255',
            'luas' => 'required|numeric',
            'gambar_skema' => 'nullable|string',
        ]);

        $data = Petak::create($request->all());

        return response()->json($data, 201);
    }

    public function show(Petak $petak)
    {
        return response()->json($petak);
    }

    public function update(Request $request, Petak $petak)
    {
        $request->validate([
            'bangunan_id' => 'required|exists:bangunans,id',
            'nama' => 'required|string|max:255',
            'luas' => 'required|numeric',
            'gambar_skema' => 'nullable|string',
        ]);


        $petak->update($request->all());

        return response()->json($petak);
    }

    public function destroy(Petak $petak)
    {
        $petak->delete();

        return response()->json(null, 204);
    }
}
