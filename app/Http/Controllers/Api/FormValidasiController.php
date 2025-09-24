<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormValidasi;
use Illuminate\Http\Request;

class FormValidasiController extends Controller
{
    public function validateByPengamat(Request $request, $id)
    {
        $validasi = FormValidasi::where('form_pengisian_id', $id)->firstOrFail();

        $validasi->update([
            'pengamat_id' => $request->pengamat_id,
            'pengamat_valid' => true,
        ]);

        return response()->json($validasi);
    }


    public function validateByUpi($id)
    {
        $validasi = FormValidasi::findOrFail($id);
        $validasi->update([
            'upi_valid' => true,
        ]);

        return response()->json($validasi);
    }
}