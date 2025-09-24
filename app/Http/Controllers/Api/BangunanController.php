<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bangunan;

class BangunanController extends Controller
{
    public function index(Request $request)
    {
        $query = Bangunan::query();

        if ($request->has('saluran_id')) {
            $query->where('saluran_id', $request->saluran_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'saluran_id' => 'required|exists:salurans,id',
            'nama' => 'required|string|max:255',
        ]);

        $data = Bangunan::create($request->all());

        return response()->json($data, 201);
    }

    public function show(Bangunan $bangunan)
    {
        return response()->json($bangunan);
    }

    public function update(Request $request, Bangunan $bangunan)
    {
        $request->validate([
            'saluran_id' => 'required|exists:salurans,id',
            'nama' => 'required|string|max:255',
        ]);


        $bangunan->update($request->all());

        return response()->json($bangunan);
    }

    public function destroy(Bangunan $bangunan)
    {
        $bangunan->delete();

        return response()->json(null, 204);
    }
}
