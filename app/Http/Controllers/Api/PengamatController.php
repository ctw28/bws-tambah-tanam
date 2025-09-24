<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PengamatController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengamat::with(['daerahIrigasi', 'sesi']);
        if ($request->has('daerah_irigasi_id')) {
            $query->whereHas('daerahIrigasi', function ($q) use ($request) {
                $q->where('daerahIrigasi.id', $request->daerah_irigasi_id);
            });
        }

        return response()->json($query->get());
    }


    // Simpan pengamat baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20',
            'sesi_id' => 'required|exists:sesis,id',
            'daerah_irigasi_id' => [
                'required',
                Rule::exists('daerah_irigasis', 'id'),
                Rule::unique('pengamats')->where(
                    fn($q) =>
                    $q->where('sesi_id', $request->sesi_id)
                ),
            ],
        ]);

        // generate kode unik
        do {
            $plainKode = strtoupper(Str::random(6)); // contoh: AB12CD
        } while (Pengamat::where('kode', $plainKode)->exists());

        $pengamat = Pengamat::create([
            'nama' => $request->nama,
            'nomor_hp' => $request->nomor_hp,
            'sesi_id' => $request->sesi_id,
            'daerah_irigasi_id' => $request->daerah_irigasi_id,
            'kode' => $plainKode,
        ]);

        return response()->json($pengamat->load(['sesi', 'daerahIrigasi']));
    }


    // Tampilkan detail pengamat
    public function show($id)
    {
        $pengamat = Pengamat::with('daerahIrigasi:id,nama')->findOrFail($id);
        return response()->json($pengamat);
    }

    // Update pengamat
    public function update(Request $request, Pengamat $pengamat)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20',
            'sesi_id' => 'required|exists:sesis,id',
            'daerah_irigasi_id' => [
                'required',
                Rule::exists('daerah_irigasis', 'id'),
                Rule::unique('pengamats')
                    ->where(fn($q) => $q->where('sesi_id', $request->sesi_id))
                    ->ignore($pengamat->id),
            ],
        ]);

        // kalau ingin regenerasi kode setiap update:
        do {
            $plainKode = strtoupper(Str::random(6)); // contoh: AB12CD
        } while (Pengamat::where('kode', $plainKode)->exists());

        $data = $request->only(['nama', 'nomor_hp', 'sesi_id', 'daerah_irigasi_id']);
        $data['kode'] = $plainKode;

        $pengamat->update($data);

        return response()->json($pengamat->load(['sesi', 'daerahIrigasi']));
    }
    // Hapus pengamat
    public function destroy($id)
    {
        $pengamat = Pengamat::findOrFail($id);
        $pengamat->delete();

        return response()->json(['message' => 'Pengamat berhasil dihapus']);
    }
    public function validasiKode(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
        ]);

        $pengamat = Pengamat::with('daerahIrigasi')
            ->where('kode', $request->kode)
            ->whereNotNull('kode')
            ->first();

        // $found = null;
        // foreach ($pengamat as $p) {
        //     if (Hash::check($request->kode, $p->kode)) {
        //         $found = $p;
        //         break;
        //     }
        // }

        if (!$pengamat) {
            return response()->json(['message' => 'Kode salah'], 401);
        }

        return response()->json([
            'pengamat' => $pengamat,
        ]);
    }

    public function sendKode(Pengamat $pengamat)
    {
        // ambil kode yang sudah tersimpan
        $kode = $pengamat->kode;
        // buat link wa.me
        $nomor = preg_replace('/^0/', '62', $pengamat->hp); // ganti 0 -> 62
        $pesan = "Halo {$pengamat->nama}, kode petugas Anda adalah: {$kode}. JANGAN MEMBERIKAN KODE INI KEPADA SIAPAPUN!";
        $link = "https://wa.me/{$nomor}?text=" . urlencode($pesan);

        return response()->json(['link' => $link]);
    }
}
