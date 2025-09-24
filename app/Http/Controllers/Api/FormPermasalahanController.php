<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormPermasalahan;

class FormPermasalahanController extends Controller
{
    public function index()
    {
        return response()->json(FormPermasalahan::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'form_pengisian_id' => 'required|exists:form_pengisians,id',
            'pemantauan_permasalahan_id' => 'required|exists:pemantauan_permasalahans,id',
            'status' => 'required',
            'keterangan' => 'nullable|string',
        ]);
        $data = FormPermasalahan::create($request->all());

        return response()->json($data, 201);
    }

    public function show(FormPermasalahan $data)
    {
        return response()->json($data);
    }

    public function update(Request $request, FormPermasalahan $data)
    {
        $request->validate([
            'form_pengisian_id' => 'required|exists:form_pengisians,id',
            'pemantauan_permasalahan_id' => 'required|exists:pemantauan_permasalahans,id',
            'status' => 'required',
            'keterangan' => 'nullable|string',
        ]);
        $data->update($request->all());

        return response()->json($data);
    }

    public function destroy(FormPermasalahan $data)
    {
        $data->delete();

        return response()->json(null, 204);
    }
}
