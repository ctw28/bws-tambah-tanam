<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Petak;
use Illuminate\Support\Facades\Storage;

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
            'nama'        => 'required|string|max:255',
            'luas'        => 'required|numeric',
            'gambar_skema' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['bangunan_id', 'nama', 'luas']);

        if ($request->hasFile('gambar_skema')) {
            $path = $request->file('gambar_skema')->store('petak', 'public');
            $data['gambar_skema'] = $path;
        }

        $petak = Petak::create($data);

        return response()->json($petak, 201);
    }

    public function show(Petak $petak)
    {
        return response()->json($petak);
    }

    public function update(Request $request, Petak $petak)
    {
        $request->validate([
            'bangunan_id' => 'required|exists:bangunans,id',
            'nama'        => 'required|string|max:255',
            'luas'        => 'required|numeric',
            'gambar_skema' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['bangunan_id', 'nama', 'luas']);

        if ($request->hasFile('gambar_skema')) {
            // hapus gambar lama
            if ($petak->gambar_skema && Storage::disk('public')->exists($petak->gambar_skema)) {
                Storage::disk('public')->delete($petak->gambar_skema);
            }
            // simpan gambar baru
            $path = $request->file('gambar_skema')->store('petak', 'public');
            $data['gambar_skema'] = $path;
        }

        $petak->update($data);

        return response()->json($petak);
    }

    public function destroy(Petak $petak)
    {
        if ($petak->gambar_skema && Storage::disk('public')->exists($petak->gambar_skema)) {
            Storage::disk('public')->delete($petak->gambar_skema);
        }

        $petak->delete();

        return response()->json(null, 204);
    }
}