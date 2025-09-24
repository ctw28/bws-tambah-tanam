<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kabupaten;

class KabupatenController extends Controller
{
    public function index()
    {
        return Kabupaten::select('id', 'nama')->get();
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $data = Kabupaten::create($request->all());

        return response()->json($data, 201);
    }

    public function show(Kabupaten $kabupaten)
    {
        return response()->json($kabupaten);
    }

    public function update(Request $request, Kabupaten $kabupaten)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $kabupaten->update($request->all());

        return response()->json($kabupaten);
    }

    public function destroy(Kabupaten $kabupaten)
    {
        $kabupaten->delete();

        return response()->json(null, 204);
    }
}
