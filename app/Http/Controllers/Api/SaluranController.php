<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Saluran;

class SaluranController extends Controller
{
    public function index(Request $request)
    {
        $query = Saluran::with(['daerahIrigasi', 'petugas']);

        if ($request->has('daerah_irigasi_id')) {
            $query->where('daerah_irigasi_id', $request->daerah_irigasi_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'daerah_irigasi_id' => 'required|exists:daerah_irigasis,id',
            'nama' => 'required|string|max:255',
        ]);

        $data = Saluran::create($request->all());

        return response()->json($data, 201);
    }

    public function show(Saluran $data)
    {
        return response()->json($data);
    }

    public function update(Request $request, Saluran $saluran)
    {
        $request->validate([
            'daerah_irigasi_id' => 'required|exists:daerah_irigasis,id',
            'nama' => 'required|string|max:255',
        ]);

        $saluran->update($request->all());

        return response()->json($saluran);
    }

    public function destroy(Saluran $saluran)
    {
        $saluran->delete();

        return response()->json(null, 204);
    }
}