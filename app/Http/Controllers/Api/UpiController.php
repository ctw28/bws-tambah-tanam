<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Upi;
use App\Models\DaerahIrigasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpiController extends Controller
{
    public function index()
    {
        $upis = Upi::with(['sesi:id,nama', 'daerahIrigasis:id,nama'])->get();
        return response()->json($upis);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:20',
            'sesi_id' => 'required|exists:sesis,id',
            'daerah_irigasi_ids' => 'required|array|min:1',
            'daerah_irigasi_ids.*' => [
                'integer',
                Rule::exists('daerah_irigasis', 'id'),
            ],
        ]);

        // generate kode unik
        do {
            $kode = strtoupper(Str::random(6));
        } while (Upi::where('kode', $kode)->exists());

        $upi = Upi::create([
            'sesi_id' => $request->sesi_id,
            'nama'    => $request->nama,
            'no_hp'   => $request->no_hp,
            'kode'    => $kode,
        ]);

        // simpan pivot (DI yang dipilih)
        $upi->daerahIrigasis()->sync($request->daerah_irigasi_ids);

        return response()->json($upi->load(['sesi', 'daerahIrigasis']));
    }

    public function update(Request $request, Upi $upi)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:20',
            'sesi_id' => 'required|exists:sesis,id',
            'daerah_irigasi_ids' => 'required|array|min:1',
            'daerah_irigasi_ids.*' => [
                'integer',
                Rule::exists('daerah_irigasis', 'id'),
            ],
        ]);

        $upi->update([
            'sesi_id' => $request->sesi_id,
            'nama'    => $request->nama,
            'no_hp'   => $request->no_hp,
        ]);

        $upi->daerahIrigasis()->sync($request->daerah_irigasi_ids);

        return response()->json($upi->load(['sesi', 'daerahIrigasis']));
    }

    public function destroy(Upi $upi)
    {
        $upi->delete();
        return response()->json(['message' => 'UPI deleted successfully']);
    }

    public function validasiKode(Request $request)
    {


        $upi = Upi::with(['daerahIrigasis.children'])
            ->where('kode', $request->kode)                   // 🔹 hanya yg aktif
            ->first();

        if (!$upi) {
            return response()->json(['message' => 'Kode salah atau petugas tidak aktif'], 401);
        }

        // if (!$petugas || !Hash::check($request->kode, $petugas->kode)) {
        //     return response()->json(['message' => 'Kode salah atau petugas tidak aktif'], 401);
        // }
        return response()->json([
            'message' => 'Kode benar',
            'upi' => $upi
        ]);
    }



    public function sendKode(Upi $upi)
    {
        // ambil kode yang sudah tersimpan
        $kode = $upi->kode;

        // kalau ingin selalu generate baru:
        // $kode = strtoupper(Str::random(6));
        // $petuga->update(['kode' => $kode]);

        // buat link wa.me
        $nomor = preg_replace('/^0/', '62', $upi->no_hp); // ganti 0 -> 62
        $pesan = "Halo {$upi->nama}, kode petugas Anda adalah: *{$kode}*. JANGAN MEMBERIKAN KODE INI KEPADA SIAPAPUN!";
        $link = "https://wa.me/{$nomor}?text=" . urlencode($pesan);

        return response()->json(['link' => $link]);
    }
}
