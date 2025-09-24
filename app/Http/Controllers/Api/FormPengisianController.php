<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaerahIrigasi;
use Illuminate\Http\Request;
use App\Models\FormPengisian;
use App\Models\FormPermasalahan;
use App\Models\FormValidasi;
use Illuminate\Support\Facades\Auth;

class FormPengisianController extends Controller
{
    public function index(Request $request)
    {
        $data = FormPengisian::with([
            'kabupaten',
            'daerahIrigasi',
            'petugas',
            'saluran',
            'bangunan',
            'petak',
            'validasi',
            'permasalahan.masterPermasalahan'
        ])
            ->when($request->id, function ($q) use ($request) {
                $q->where('id', $request->id);
            })
            ->when($request->di_id, function ($q) use ($request) {
                $q->where('daerah_irigasi_id', $request->di_id);
            })
            ->when($request->pengamat_valid, function ($q) use ($request) {
                $q->whereHas('validasi', function ($qq) use ($request) {
                    $qq->where('pengamat_valid', (bool) $request->pengamat_valid);
                });
            })
            ->when($request->upi_valid, function ($q) use ($request) {
                $q->whereHas('validasi', function ($qq) use ($request) {
                    $qq->where('upi_valid', (bool) $request->upi_valid);
                });
            })
            ->latest()
            ->get();

        return response()->json($data);
    }


    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'tanggal_pantau' => 'required|date',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'daerah_irigasi_id' => 'required|exists:daerah_irigasis,id',
            'petugas_id' => 'required|exists:petugas,id',
            'saluran_id' => 'required|exists:salurans,id',
            'bangunan_id' => 'required|exists:bangunans,id',
            'petak_id' => 'required|exists:petaks,id',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'koordinat' => 'required|string|max:255',
            'debit_air' => 'required|numeric',
            'masa_tanam' => 'required|in:I,II,III',
            'luas_padi' => 'required|numeric',
            'luas_palawija' => 'required|numeric',
            'luas_lainnya' => 'required|numeric',
            'foto_pemantauan' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'permasalahan' => 'required',
            'permasalahan.*.pemantauan_permasalahan_id' => 'required|exists:pemantauan_permasalahans,id',
            'permasalahan.*.status' => 'required|boolean',
            'permasalahan.*.keterangan' => 'nullable|string',
        ]);

        // upload file
        $fotoPath = null;
        if ($request->hasFile('foto_pemantauan')) {
            $fotoPath = $request->file('foto_pemantauan')->store('foto_pemantauan', 'public');
        }

        // simpan data utama
        $formPengisian = FormPengisian::create([
            ...$request->except(['permasalahan', 'foto_pemantauan']),
            'foto_pemantauan' => $fotoPath,
        ]);

        // simpan permasalahan
        // Decode the JSON string before iterating
        $permasalahans = json_decode($request->permasalahan, true);

        if (is_array($permasalahans)) {
            $i = 1;
            foreach ($permasalahans as $p) {
                FormPermasalahan::create([
                    'form_pengisian_id' => $formPengisian->id,
                    'master_permasalahan_id' => $p['master_permasalahan_id'],
                    'status' => $p['status'] === 'ada', // Convert 'ada' to boolean true
                    'keterangan' => $p['keterangan'] ?? null,
                ]);
                $i++;
            }
        }
        //langsung insert juga di form validasi
        FormValidasi::create([
            'form_pengisian_id' => $formPengisian->id,
            'pengamat_id' => null,
            'pengamat_valid' => false,
            'upi_valid' => false,
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => $formPengisian->load('permasalahan')
        ], 201);
    }





    public function show(FormPengisian $data)
    {
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $formPengisian = FormPengisian::findOrFail($id);

        $request->validate([
            'tanggal_pantau' => 'required|date',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'daerah_irigasi_id' => 'required|exists:daerah_irigasis,id',
            'petugas_id' => 'required|exists:petugas,id',
            'saluran_id' => 'required|exists:salurans,id',
            'bangunan_id' => 'required|exists:bangunans,id',
            'petak_id' => 'required|exists:petaks,id',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'koordinat' => 'required|string|max:255',
            'debit_air' => 'required|numeric',
            'masa_tanam' => 'required|in:I,II,III',
            'luas_padi' => 'required|numeric',
            'luas_palawija' => 'required|numeric',
            'luas_lainnya' => 'required|numeric',
            'foto_pemantauan' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'permasalahan' => 'required',
            'permasalahan.*.pemantauan_permasalahan_id' => 'required|exists:pemantauan_permasalahans,id',
            'permasalahan.*.status' => 'required|boolean',
            'permasalahan.*.keterangan' => 'nullable|string',
        ]);

        // upload file baru jika ada
        $fotoPath = $formPengisian->foto_pemantauan;
        if ($request->hasFile('foto_pemantauan')) {
            // hapus file lama
            if ($fotoPath && \Storage::disk('public')->exists($fotoPath)) {
                \Storage::disk('public')->delete($fotoPath);
            }
            // simpan file baru
            $fotoPath = $request->file('foto_pemantauan')->store('foto_pemantauan', 'public');
        }

        // update data utama
        $formPengisian->update([
            ...$request->except(['permasalahan', 'foto_pemantauan']),
            'foto_pemantauan' => $fotoPath,
        ]);

        // update permasalahan → hapus lama dulu
        $formPengisian->permasalahan()->delete();

        $permasalahans = json_decode($request->permasalahan, true);

        if (is_array($permasalahans)) {
            foreach ($permasalahans as $p) {
                FormPermasalahan::create([
                    'form_pengisian_id' => $formPengisian->id,
                    'master_permasalahan_id' => $p['master_permasalahan_id'],
                    'status' => $p['status'] === 'ada', // sama seperti store
                    'keterangan' => $p['keterangan'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $formPengisian->load('permasalahan')
        ], 200);
    }


    public function destroy(FormPengisian $data)
    {
        $data->delete();

        return response()->json(null, 204);
    }

    public function getUserDis()
    {
        $user = Auth::user();
        // return $user;
        // Ambil semua kabupaten yg dimiliki user
        $kabupatens = $user->kabupatens()
            ->with(['daerahIrigasis' => function ($q) {
                $q->withCount('upis'); // hitung jumlah UPI per DI
            }])
            ->get();

        // Flatten supaya gabung semua DI dari berbagai kabupaten
        $dis = $kabupatens->flatMap(function ($kabupaten) {
            return $kabupaten->daerahIrigasis->map(function ($di) use ($kabupaten) {
                return [
                    'id'        => $di->id,
                    'nama'      => $di->nama,
                    'kabupaten' => $kabupaten->id, // lebih aman ambil dari parent, bukan pivot
                    'has_upi'   => $di->upis_count > 0,
                ];
            });
        });
        // return $dis;
        return response()->json($dis->values());
    }
}
