<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaerahIrigasi;
use App\Models\Komir;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KomirController extends Controller
{
    public function index()
    {
        $komir = Komir::with(['sesi:id,nama'])->get();
        return response()->json($komir);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:20',
            'sesi_id' => 'required|exists:sesis,id',
        ]);

        // generate kode unik
        do {
            $kode = strtoupper(Str::random(6));
        } while (Komir::where('kode', $kode)->exists());

        $komir = Komir::create([
            'sesi_id' => $request->sesi_id,
            'nama'    => $request->nama,
            'no_hp'   => $request->no_hp,
            'kode'    => $kode,
        ]);

        return response()->json($komir->load(['sesi']));
    }

    public function update(Request $request, Komir $komir)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:20',
            'sesi_id' => 'required|exists:sesis,id',
        ]);

        $komir->update([
            'sesi_id' => $request->sesi_id,
            'nama'    => $request->nama,
            'no_hp'   => $request->no_hp,
        ]);

        return response()->json($komir->load(['sesi']));
    }

    public function destroy(Komir $komir)
    {
        $komir->delete();
        return response()->json(['message' => 'Komir deleted successfully']);
    }

    public function validasiKode(Request $request)
    {


        $komir = Komir::where('kode', $request->kode)                   // 🔹 hanya yg aktif
            ->first();

        if (!$komir) {
            return response()->json(['message' => 'Kode salah atau petugas tidak aktif'], 401);
        }

        // if (!$petugas || !Hash::check($request->kode, $petugas->kode)) {
        //     return response()->json(['message' => 'Kode salah atau petugas tidak aktif'], 401);
        // }
        return response()->json([
            'message' => 'Kode benar',
            'komis' => $komir
        ]);
    }



    public function sendKode(Komir $komir)
    {
        // ambil kode yang sudah tersimpan
        $kode = $komir->kode;

        // kalau ingin selalu generate baru:
        // $kode = strtoupper(Str::random(6));
        // $petuga->update(['kode' => $kode]);

        // buat link wa.me
        $nomor = preg_replace('/^0/', '62', $komir->no_hp); // ganti 0 -> 62
        $pesan = "Halo {$komir->nama}, kode komir adalah: *{$kode}*. JANGAN MEMBERIKAN KODE INI KEPADA SIAPAPUN!";
        $link = "https://wa.me/{$nomor}?text=" . urlencode($pesan);

        return response()->json(['link' => $link]);
    }
}
