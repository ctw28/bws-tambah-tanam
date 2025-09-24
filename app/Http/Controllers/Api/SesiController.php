<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    public function index()
    {
        return response()->json(Sesi::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'is_aktif' => 'boolean'
        ]);

        $sesi = Sesi::create($data);
        return response()->json($sesi, 201);
    }

    public function show(Sesi $sesi)
    {
        return response()->json($sesi);
    }

    public function update(Request $request, Sesi $sesi)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'is_aktif' => 'boolean'
        ]);

        $sesi->update($data);
        return response()->json($sesi);
    }

    public function destroy(Sesi $sesi)
    {
        $sesi->delete();
        return response()->json(['message' => 'Sesi deleted']);
    }
}
