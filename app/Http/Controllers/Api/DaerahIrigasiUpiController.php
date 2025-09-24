<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class DaerahIrigasiUpiController extends Controller
{
    public function validasiKode(Request $request)
    {
        $upi = DaerahIrigasiUpi::where('kode', $request->kode)->first();
        if (!$upi) {
            return response()->json(['message' => 'Kode tidak valid'], 404);
        }
        return response()->json($upi);
    }
}
