<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Petugas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class PetugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Petugas::with(['salurans.daerahIrigasi']);

        if ($request->has('saluran_id')) {
            $query->where('saluran_id', $request->saluran_id);
        }

        return response()->json($query->get());
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'hp' => 'required|string|min:10', // validasi no HP
            'is_aktif' => 'boolean',
            'saluran_ids' => 'required|array',
            'saluran_ids.*' => 'exists:salurans,id',
        ]);

        // generate kode unik
        do {
            $plainKode = strtoupper(Str::random(6)); // misal: AB12CD
        } while (Petugas::where('kode', $plainKode)->exists());

        $petugas = Petugas::create([
            'sesi_id' => $request->sesi_id ?? 1,
            'nama' => $validated['nama'],
            'hp' => $validated['hp'],
            'kode' => $plainKode,
            'is_aktif' => $validated['is_aktif'] ?? true,
        ]);

        $petugas->salurans()->sync($validated['saluran_ids']);

        // TODO: kirim kode via WhatsApp API
        // WhatsAppService::send($petugas->hp, "Halo {$petugas->nama}, kode login Anda: {$plainKode}");

        return response()->json($petugas->load('salurans'), 201);
    }


    public function show(Petugas $data)
    {
        return response()->json($data);
    }

    public function update(Request $request, Petugas $petuga)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'is_aktif' => 'boolean',
            'hp' => 'required|string|min:10', // validasi no HP
            'saluran_ids' => 'array',
            'saluran_ids.*' => 'exists:salurans,id',
        ]);

        // update nama & status aktif
        $updateData = [
            'nama' => $validated['nama'],
            'hp' => $validated['hp'],

            'is_aktif' => $validated['is_aktif'] ?? $petuga->is_aktif,
        ];

        // update kode jika dikirim
        if (!empty($validated['kode'])) {
            $updateData['kode'] = Hash::make($validated['kode']);
        }

        $petuga->update($updateData);

        // update saluran jika dikirim
        if (isset($validated['saluran_ids'])) {
            $petuga->salurans()->sync($validated['saluran_ids']);
        }

        return response()->json($petuga->load('salurans'));
    }


    public function destroy(Petugas $data)
    {
        $data->delete();

        return response()->json(null, 204);
    }
    public function validasiKode(Request $request)
    {
        $request->validate([
            'saluran_id' => 'required|exists:salurans,id',   // pastikan saluran valid
            'petugas_id' => 'required|exists:petugas,id',
            'kode'       => 'required|string'
        ]);

        $petugas = Petugas::where('id', $request->petugas_id)
            ->where('is_aktif', true)                   // 🔹 hanya yg aktif
            ->where('kode', $request->kode)                   // 🔹 hanya yg aktif
            ->first();

        if (!$petugas) {
            return response()->json(['message' => 'Kode salah atau petugas tidak aktif'], 401);
        }

        // if (!$petugas || !Hash::check($request->kode, $petugas->kode)) {
        //     return response()->json(['message' => 'Kode salah atau petugas tidak aktif'], 401);
        // }
        return response()->json([
            'message' => 'Kode benar',
            'petugas' => $petugas->only(['id', 'nama', 'saluran_id'])
        ]);
    }



    public function sendKode(Petugas $petuga)
    {
        // ambil kode yang sudah tersimpan
        $kode = $petuga->kode;

        // kalau ingin selalu generate baru:
        // $kode = strtoupper(Str::random(6));
        // $petuga->update(['kode' => $kode]);

        // buat link wa.me
        $nomor = preg_replace('/^0/', '62', $petuga->hp); // ganti 0 -> 62
        $pesan = "Halo {$petuga->nama}, kode petugas Anda adalah: {$kode}. JANGAN MEMBERIKAN KODE INI KEPADA SIAPAPUN!";
        $link = "https://wa.me/{$nomor}?text=" . urlencode($pesan);

        return response()->json(['link' => $link]);
    }
}