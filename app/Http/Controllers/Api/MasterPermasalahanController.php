<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterPermasalahan;

class MasterPermasalahanController extends Controller
{
    public function index()
    {
        return response()->json(MasterPermasalahan::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);
        $data = MasterPermasalahan::create($request->all());

        return response()->json($data, 201);
    }

    public function show(MasterPermasalahan $data)
    {
        return response()->json($data);
    }

    public function update(Request $request, MasterPermasalahan $data)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $data->update($request->all());

        return response()->json($data);
    }

    public function destroy(MasterPermasalahan $data)
    {
        $data->delete();

        return response()->json(null, 204);
    }
}