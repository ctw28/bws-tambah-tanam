<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaerahIrigasi;
use Illuminate\Http\Request;
use App\Models\FormPengisian;
use App\Models\FormPengisianP3a;
use App\Models\FormPermasalahan;
use App\Models\FormValidasi;
use App\Models\P3a;
use App\Models\Petak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormPengisianController extends Controller
{
    // public function index(Request $request)
    // {
    //     $data = FormPengisian::with([
    //         'kabupaten',
    //         'daerahIrigasi',
    //         'petugas',
    //         'saluran',
    //         'bangunan',
    //         'petak',
    //         'validasi',
    //         'permasalahan' => function ($q) {
    //             $q->where('status', 1)
    //                 ->whereNotNull('keterangan')
    //                 ->where('keterangan', '!=', '');
    //         },
    //         'permasalahan.masterPermasalahan',
    //     ])
    //         ->when($request->id, function ($q) use ($request) {
    //             $q->where('id', $request->id);
    //         })
    //         ->when($request->di_id, function ($q) use ($request) {
    //             $q->where('daerah_irigasi_id', $request->di_id);
    //         })
    //         ->when($request->pengamat_valid, function ($q) use ($request) {
    //             $q->whereHas('validasi', function ($qq) use ($request) {
    //                 $qq->where('pengamat_valid', (bool) $request->pengamat_valid);
    //             });
    //         })
    //         ->when($request->upi_valid, function ($q) use ($request) {
    //             $q->whereHas('validasi', function ($qq) use ($request) {
    //                 $qq->where('upi_valid', (bool) $request->upi_valid);
    //             });
    //         })
    //         ->when($request->has_permasalahan, function ($q) {
    //             $q->whereHas('permasalahan', function ($qq) {
    //                 $qq->where('status', 1)
    //                     ->whereNotNull('keterangan')
    //                     ->where('keterangan', '!=', '');
    //             });
    //         })
    //         ->latest()
    //         ->get();

    //     return response()->json($data);
    // }

    // public function index(Request $request)
    // {
    //     $data = FormPengisian::with([
    //         'kabupaten',
    //         'daerahIrigasi',
    //         'petugas',
    //         'saluran',
    //         'bangunan',
    //         'petak',
    //         'validasi',
    //         'permasalahan' => function ($q) {
    //             $q->where('status', 1)
    //                 ->whereNotNull('keterangan')
    //                 ->where('keterangan', '!=', '');
    //         },
    //         'permasalahan.masterPermasalahan',
    //     ])
    //         ->when($request->id, fn($q) => $q->where('id', $request->id))
    //         ->when($request->di_id, fn($q) => $q->where('daerah_irigasi_id', $request->di_id))
    //         ->when($request->pengamat_valid, function ($q) use ($request) {
    //             $q->whereHas('validasi', fn($qq) => $qq->where('pengamat_valid', (bool) $request->pengamat_valid));
    //         })
    //         ->when($request->upi_valid, function ($q) use ($request) {
    //             $q->whereHas('validasi', fn($qq) => $qq->where('upi_valid', (bool) $request->upi_valid));
    //         })
    //         ->when($request->has_permasalahan, function ($q) {
    //             $q->whereHas('permasalahan', function ($qq) {
    //                 $qq->where('status', 1)
    //                     ->whereNotNull('keterangan')
    //                     ->where('keterangan', '!=', '');
    //             });
    //         })
    //         ->when($request->tanggal_awal, function ($q) use ($request) {
    //             $q->whereDate('tanggal_pantau', '>=', $request->tanggal_awal);
    //         })
    //         ->when($request->tanggal_akhir, function ($q) use ($request) {
    //             $q->whereDate('tanggal_pantau', '<=', $request->tanggal_akhir);
    //         })
    //         ->latest()
    //         ->paginate($request->per_page ?? 25);

    //     return response()->json($data);
    // }
    // public function __construct()
    // {
    //     $this->middleware('auth:api')->only('index');
    // }


    public function index(Request $request)
    {
        // return $request->all();
        $user = Auth::guard('api')->user(); // ✅ bisa null kalau tidak login

        $query = FormPengisian::with([
            'kabupaten',
            'daerahIrigasi.pengamat',
            'daerahIrigasi.parent',
            'petugas',
            'saluran',
            'bangunan',
            'petak',
            'validasi',
            'permasalahan' => function ($q) {
                $q->where('status', 1)
                    ->whereNotNull('keterangan')
                    ->where('keterangan', '!=', '');
            },
            'permasalahan.masterPermasalahan',
            'formPengisianP3a.p3a'
        ])
            ->when($request->has_permasalahan, function ($q) {
                $q->whereHas('permasalahan', function ($qq) {
                    $qq->where('status', 1)
                        ->whereNotNull('keterangan')
                        ->where('keterangan', '!=', '');
                });
            })
            ->when($request->filled('pengamat_valid'), function ($q) use ($request) {
                $q->whereHas('validasi', function ($qq) use ($request) {
                    $qq->where('pengamat_valid', (int) $request->pengamat_valid);
                });
            })
            ->when($request->filled('upi_valid'), function ($q) use ($request) {
                $q->whereHas('validasi', function ($qq) use ($request) {
                    $qq->where('upi_valid', (int) $request->upi_valid);
                });
            });


        if ($user) {
            $kabupatens = $user->kabupatens()->with(['daerahIrigasis' => fn($q) => $q->withCount('upis')])->get();

            $userDis = $kabupatens->flatMap(
                fn($kab) =>
                $kab->daerahIrigasis->map(fn($di) => [
                    'id' => $di->id,
                    'has_upi' => $di->upis_count > 0,
                ])
            );

            $diIds = $userDis->pluck('id')->toArray();
            $query->whereIn('daerah_irigasi_id', $diIds);
        }

        // Tambahkan filter dari request
        $query->when($request->di_id, fn($q) => $q->where('daerah_irigasi_id', $request->di_id))
            ->when($request->saluran, fn($q) => $q->where('saluran_id', $request->saluran)) // 🔹 Filter saluran_id

            ->when($request->tanggal_awal, fn($q) => $q->whereDate('tanggal_pantau', '>=', $request->tanggal_awal))
            ->when($request->tanggal_akhir, fn($q) => $q->whereDate('tanggal_pantau', '<=', $request->tanggal_akhir));

        if ($request->has('per_page')) {
            $data = $query->latest()->paginate($request->per_page);
        } else {
            $data = $query->latest()->get();
        }

        return response()->json($data);
    }

    public function latestLaporan(Request $request)
    {
        $data = FormPengisian::with([
            'daerahIrigasi:id,nama',
            'kabupaten:id,nama',
            'petugas:id,nama',
            'validasi:id,form_pengisian_id,pengamat_valid',
            'validasi.pengamat'
        ])
            ->whereHas('validasi', function ($q) {
                $q->where('pengamat_valid', 1);
            })
            // 🔹 Filter daerah irigasi jika dikirim
            ->when($request->filled('di_id'), function ($q) use ($request) {
                $q->where('daerah_irigasi_id', $request->di_id);
            })
            // 🔹 Filter tanggal pantau (rentang)
            ->when($request->filled('tanggal_awal'), function ($q) use ($request) {
                $q->whereDate('tanggal_pantau', '>=', $request->tanggal_awal);
            })
            ->when($request->filled('tanggal_akhir'), function ($q) use ($request) {
                $q->whereDate('tanggal_pantau', '<=', $request->tanggal_akhir);
            })
            ->latest('tanggal_pantau')
            ->take(10)
            ->get([
                'id',
                'tanggal_pantau',
                'daerah_irigasi_id',
                'kabupaten_id',
                'petugas_id',
                'created_at'
            ]);

        return response()->json($data);
    }

    public function latestIssues(Request $request)
    {
        $issues = FormPermasalahan::with([
            'formPengisian.petugas',
            'formPengisian.daerahIrigasi',
            'formPengisian.saluran',
            'formPengisian.bangunan',
            'formPengisian.petak',
            'masterPermasalahan'
        ])
            ->where('status', 1)
            ->whereHas('formPengisian.validasi', function ($q) {
                $q->where('pengamat_valid', 1);
            })
            // 🔹 Filter daerah irigasi
            ->when($request->filled('di_id'), function ($q) use ($request) {
                $q->whereHas('formPengisian', function ($qq) use ($request) {
                    $qq->where('daerah_irigasi_id', $request->di_id);
                });
            })
            // 🔹 Filter tanggal pantau
            ->when($request->filled('tanggal_awal'), function ($q) use ($request) {
                $q->whereHas('formPengisian', function ($qq) use ($request) {
                    $qq->whereDate('tanggal_pantau', '>=', $request->tanggal_awal);
                });
            })
            ->when($request->filled('tanggal_akhir'), function ($q) use ($request) {
                $q->whereHas('formPengisian', function ($qq) use ($request) {
                    $qq->whereDate('tanggal_pantau', '<=', $request->tanggal_akhir);
                });
            })
            ->latest()
            ->take(10)
            ->get();

        // Format tanggal ke Indonesia
        $issues->transform(function ($item) {
            $item->tanggal_indo = \Carbon\Carbon::parse($item->created_at)
                ->locale('id')
                ->translatedFormat('d F Y H:i');
            return $item;
        });

        return response()->json($issues);
    }



    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
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
                'p3a' => 'nullable',
            ]);

            // --- 2️⃣ Upload foto pemantauan utama ---
            $fotoPath = null;
            if ($request->hasFile('foto_pemantauan')) {
                $fotoPath = $request->file('foto_pemantauan')->store('foto_pemantauan', 'public');
            }

            // --- 3️⃣ Simpan data utama ---
            $formPengisian = FormPengisian::create([
                ...$request->except(['permasalahan', 'foto_pemantauan']),
                'foto_pemantauan' => $fotoPath,
            ]);

            // --- 4️⃣ Decode data permasalahan (karena dikirim dari Vue sebagai JSON) ---
            $permasalahans = $request->permasalahan;

            if (is_array($permasalahans)) {
                foreach ($request->permasalahan as $index => $p) {
                    $fotoPermasalahanPath = null;

                    if (isset($p['foto_permasalahan']) && $p['foto_permasalahan'] instanceof \Illuminate\Http\UploadedFile) {
                        $fotoPermasalahanPath = $p['foto_permasalahan']->store('foto_permasalahan', 'public');
                    }

                    FormPermasalahan::create([
                        'form_pengisian_id' => $formPengisian->id,
                        'master_permasalahan_id' => $p['master_permasalahan_id'],
                        'status' => $p['status'] === 'ada',
                        'keterangan' => $p['keterangan'] ?? null,
                        'foto_permasalahan' => $fotoPermasalahanPath,
                    ]);
                }
            }
            $p3as = $request->p3a;

            if (is_array($p3as)) {
                foreach ($p3as as $index => $p) {
                    FormPengisianP3a::create([
                        'form_pengisian_id' => $formPengisian->id,
                        'p3a_id' => $p['p3a_id'] ?? $p['id'], // ✅ fallback ke 'id' kalau 'p3a_id' tidak ada
                    ]);
                }
            }

            // --- 5️⃣ Insert otomatis ke form_validasi ---
            FormValidasi::create([
                'form_pengisian_id' => $formPengisian->id,
                'pengamat_id' => null,
                'pengamat_valid' => false,
                'upi_valid' => false,
            ]);
            DB::commit();

            // --- 6️⃣ Response ---
            return response()->json([
                'message' => 'Data berhasil disimpan',
                'data' => $formPengisian->load('permasalahan')
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan data: ' . $th->getMessage(),
            ], 500);
        }
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


    public function destroy(FormPengisian $formPengisian)
    {
        $formPengisian->delete();

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

    public function rekapPetak(Request $request)
    {
        $diId = $request->get('di_id');
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $query = \App\Models\Petak::with([
            'bangunan.saluran',
            'formPengisian.petugas'
        ])->whereHas('bangunan.saluran', function ($q) use ($diId) {
            if ($diId) {
                $q->where('daerah_irigasi_id', $diId);
            }
        });

        // Pagination
        $petaks = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $petaks->getCollection()->map(function ($petak) use ($tanggalAwal, $tanggalAkhir) {
            // ambil form pengisian terakhir (latest)
            $formQuery = $petak->formPengisian();

            if ($tanggalAwal && $tanggalAkhir) {
                $formQuery->whereBetween('tanggal_pantau', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($tanggalAwal) {
                $formQuery->whereDate('tanggal_pantau', '>=', $tanggalAwal);
            } elseif ($tanggalAkhir) {
                $formQuery->whereDate('tanggal_pantau', '<=', $tanggalAkhir);
            }

            $last = $formQuery->latest('tanggal_pantau')->first();

            return [
                'saluran' => $petak->bangunan->saluran->nama ?? '-',
                'bangunan' => $petak->bangunan->nama ?? '-',
                'petak' => $petak->nama ?? '-',
                'padi' => (float) ($last->luas_padi ?? 0),
                'palawija' => (float) ($last->luas_palawija ?? 0),
                'lainnya' => (float) ($last->luas_lainnya ?? 0),
                'total' => (float) ($last->luas_padi ?? 0) + (float) ($last->luas_palawija ?? 0) + (float) ($last->luas_lainnya ?? 0),
                'tanggal_update' => $last->tanggal_pantau ?? '-',
                'pengisi_terakhir' => $last->petugas->nama ?? '-',
            ];
        });

        // Total dari semua petak pada halaman ini
        $totalPadi = $data->sum('padi');
        $totalPalawija = $data->sum('palawija');
        $totalLainnya = $data->sum('lainnya');

        return response()->json([
            'data' => $data,
            'current_page' => $petaks->currentPage(),
            'last_page' => $petaks->lastPage(),
            'total' => $petaks->total(),
            'per_page' => $petaks->perPage(),
            'total_luas' => [
                'padi' => $totalPadi,
                'palawija' => $totalPalawija,
                'lainnya' => $totalLainnya,
                'total' => $totalPadi + $totalPalawija + $totalLainnya,
            ],
        ]);
    }
}
