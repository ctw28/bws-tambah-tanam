@extends('admin.template')

@section('content')
<div id="app" v-cloak class="p-4">
    <div class="card h-100">
        <div class="card-body">
            <h2>Data Summary</h2>

            <div class="row g-3 mb-4 text-center">

                <!-- Total Laporan Juru -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-primary">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-file icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_laporan_valid }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Laporan Juru Tervalidasi</p>
                        </div>
                    </div>
                </div>

                <!-- Total P3A -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-warning">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-community icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_p3a }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Total P3A</p>
                        </div>
                    </div>
                </div>
                <!-- Total Daerah Irigasi -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-info">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="bx bx-water icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_daerah_irigasi }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Total Daerah Irigasi</p>
                        </div>
                    </div>
                </div>

                <!-- Total Saluran -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-success">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="bx bx-git-branch icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_saluran }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Total Saluran</p>
                        </div>
                    </div>
                </div>

                <!-- Total Bangunan -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-warning">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="bx bx-building icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_bangunan }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Total Bangunan</p>
                        </div>
                    </div>
                </div>

                <!-- Total Luas Tanam -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-info">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="bx bx-traffic-cone icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_petak }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Total Petak</p>
                        </div>
                    </div>
                </div>

                <!-- Jumlah Juru -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-success">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-user icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_juru }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Jumlah Juru</p>
                        </div>
                    </div>
                </div>

                <!-- Jumlah Pengamat -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-bottom border-4 border-secondary">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-danger">
                                        <i class="bx bx-bullseye icon-lg"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">@{{ rekap.total_pengamat }}</h4>
                            </div>
                            <p class="mb-0 text-muted fw-semibold">Jumlah Pengamat</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-4">
                <!-- 10 Laporan Terakhir Masuk -->
                <div class="col-lg-3 col-md-12">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Laporan Terbaru</h6>
                            <!-- <button class="btn btn-sm btn-link text-muted p-0" @click="fetchLatestReports">
                                <i class="bx bx-refresh"></i>
                            </button> -->
                        </div>

                        <div class="card-body">
                            <ul class="p-0 m-0">
                                <li v-for="(laporan, index) in latestReports" :key="laporan.id"
                                    class="d-flex align-items-start mb-4 pb-2 border-bottom">

                                    <!-- Avatar kiri -->
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bx-file"></i>
                                        </span>
                                    </div>

                                    <!-- Isi laporan -->
                                    <div class="d-flex w-100 justify-content-between flex-wrap gap-2">
                                        <div>
                                            <h6 class="mb-1">
                                                DI @{{ laporan.daerah_irigasi?.nama || '-' }} - @{{ laporan.saluran?.nama }} - @{{ laporan.bangunan?.nama }} - @{{ laporan.petak?.nama }}
                                            </h6>
                                            <!-- <small class="text-body-secondary d-block">
                                                Pengamat: @{{ laporan.validasi?.pengamat?.nama || '-' }}
                                            </small> -->
                                            <small class="text-muted">
                                                @{{ formatTanggalIndo(laporan.created_at) }}
                                            </small>
                                        </div>

                                        <!-- <span class="badge bg-label-primary mt-auto">Laporan #@{{ index + 1 }}</span> -->
                                    </div>
                                </li>

                                <!-- Jika kosong -->
                                <li v-if="latestReports.length === 0" class="text-center text-muted py-3">
                                    Belum ada laporan terbaru
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <!-- 10 Permasalahan Terakhir -->
                <div class="col-lg-9 col-md-12 mt-4 mt-lg-0">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Permasalahan Terbaru</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Juru</th>
                                        <th>Permasalahan</th>
                                        <th>Foto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(p, index) in latestIssues" :key="p.id">
                                        <td>@{{ index + 1 }}</td>
                                        <td>
                                            @{{ p.form_pengisian?.petugas?.nama || '-' }} <br>
                                            <span class="badge bg-success">
                                                @{{ p.form_pengisian?.daerah_irigasi?.nama ?? '-' }}
                                            </span> <br>
                                            @{{ p.form_pengisian?.saluran?.nama || '-' }} <br> @{{ p.form_pengisian?.bangunan?.nama || '-' }} - @{{ p.form_pengisian?.petak?.nama || '-' }}<br>
                                            @{{ formatTanggalIndo(p.created_at) }}
                                        </td>
                                        <td>@{{ p.master_permasalahan?.id }}. @{{ p.master_permasalahan?.nama }} : <br>
                                            @{{p.keterangan || '-'}}</td>
                                        <!-- <td>@{{ p.created_at }}</td> -->
                                        <td> <img v-if="p.foto_permasalahan" :src="`/storage/${p.foto_permasalahan}`" width="100">
                                        </td>
                                    </tr>
                                    <tr v-if="latestIssues.length === 0">
                                        <td colspan="4" class="text-center text-muted">Belum ada permasalahan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="card mt-4 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">SIPemalutajir v1.0</h6>
                    <p class="text-muted mb-0">
                        Sistem Informasi Pemantauan Lumbung Tanaman dan Jaringan Irigasi<br>
                        <small>Dikembangkan tahun 2025 oleh Tim BBWS/BWS</small>
                    </p>
                </div>
            </div> -->

        </div>
    </div>
</div>



@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                items: [], // semua data laporan
                filteredItems: [], // data hasil filter
                filterTanggalAwal: '',
                filterTanggalAkhir: '',
                chartDI: null,
                chartItem: null,
                rekap: [],
                latestReports: [],
                latestIssues: []
            }
        },
        computed: {
            // total luas semua laporan
            totalLuas() {
                let padi = 0,
                    palawija = 0,
                    lainnya = 0,
                    debit_air = 0;

                this.filteredItems.forEach(i => {
                    padi += parseFloat(i.luas_padi) || 0;
                    palawija += parseFloat(i.luas_palawija) || 0;
                    lainnya += parseFloat(i.luas_lainnya) || 0;
                    debit_air += parseFloat(i.debit_air) || 0;
                });

                const format = (n) => new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 2
                }).format(n);

                return {
                    padi: format(padi),
                    palawija: format(palawija),
                    lainnya: format(lainnya),
                    debit_air: format(debit_air),
                    total: format(padi + palawija + lainnya)
                };
            },

            // rekap per petugas
            rekapPerPetugas() {
                const rekap = {};
                this.filteredItems.forEach(i => {
                    const nama = i.petugas.nama;
                    if (!rekap[nama]) {
                        rekap[nama] = {
                            padi: 0,
                            palawija: 0,
                            lainnya: 0,
                            debit_air: 0,
                            total: 0
                        };
                    }
                    rekap[nama].debit_air += parseFloat(i.debit_air);
                    rekap[nama].padi += parseFloat(i.luas_padi);
                    rekap[nama].palawija += parseFloat(i.luas_palawija);
                    rekap[nama].lainnya += parseFloat(i.luas_lainnya);
                    rekap[nama].total += parseFloat(i.luas_padi) + parseFloat(i.luas_palawija) + parseFloat(
                        i.luas_lainnya);
                });
                return rekap;
            },
            // rekapPerDaerahIrigasi() {
            //     const rekap = {};
            //     this.filteredItems.forEach(i => {
            //         const namaDI = i.daerah_irigasi?.nama || 'Tidak Ada DI';
            //         if (!rekap[namaDI]) {
            //             rekap[namaDI] = {
            //                 baku: 0,
            //                 potensial: 0,
            //                 fungsional: 0,
            //                 padi: 0,
            //                 palawija: 0,
            //                 lainnya: 0,
            //                 total: 0
            //             };
            //         }
            //         rekap[namaDI].baku = parseFloat(i.daerah_irigasi?.luas_baku ?? 0);
            //         rekap[namaDI].potensial = parseFloat(i.daerah_irigasi?.luas_potensial ?? 0);
            //         rekap[namaDI].fungsional = parseFloat(i.daerah_irigasi?.luas_fungsional ?? 0);
            //         rekap[namaDI].padi += parseFloat(i.luas_padi);
            //         rekap[namaDI].palawija += parseFloat(i.luas_palawija);
            //         rekap[namaDI].lainnya += parseFloat(i.luas_lainnya);
            //         rekap[namaDI].total += parseFloat(i.luas_padi) + parseFloat(i.luas_palawija) +
            //             parseFloat(i.luas_lainnya);
            //     });
            //     return rekap;
            // }
            rekapPerDaerahIrigasi() {
                const rekap = {};

                this.filteredItems.forEach(i => {
                    const di = i.daerah_irigasi;
                    if (!di) return;

                    // ambil nama DI induk jika ada, kalau tidak pakai nama sendiri
                    const namaDI = di.parent_id ?
                        (di.parent?.nama || 'Tidak Ada DI') :
                        (di.nama || 'Tidak Ada DI');

                    if (!rekap[namaDI]) {
                        rekap[namaDI] = {
                            baku: 0,
                            potensial: 0,
                            fungsional: 0,
                            padi: 0,
                            palawija: 0,
                            lainnya: 0,
                            total: 0
                        };
                    }

                    // gunakan data luas dari DI sekarang
                    rekap[namaDI].baku = parseFloat(di.luas_baku ?? 0);
                    rekap[namaDI].potensial = parseFloat(di.luas_potensial ?? 0);
                    rekap[namaDI].fungsional = parseFloat(di.luas_fungsional ?? 0);

                    rekap[namaDI].padi += parseFloat(i.luas_padi ?? 0);
                    rekap[namaDI].palawija += parseFloat(i.luas_palawija ?? 0);
                    rekap[namaDI].lainnya += parseFloat(i.luas_lainnya ?? 0);
                    rekap[namaDI].total +=
                        parseFloat(i.luas_padi ?? 0) +
                        parseFloat(i.luas_palawija ?? 0) +
                        parseFloat(i.luas_lainnya ?? 0);
                });

                return rekap;
            }



        },
        methods: {
            syncTanggal() {
                // kalau user pilih tanggal awal, otomatis set tanggal akhir sama
                this.filterTanggalAkhir = this.filterTanggalAwal;
            },
            // applyFilter() {
            //     if (!this.filterTanggalAwal || !this.filterTanggalAkhir) {
            //         this.filteredItems = this.items;
            //     } else {
            //         this.filteredItems = this.items.filter(i =>
            //             i.tanggal_pantau >= this.filterTanggalAwal &&
            //             i.tanggal_pantau <= this.filterTanggalAkhir
            //         );
            //     }
            //     this.chartPerDI();
            //     this.chartPerItem();
            // },
            applyFilter() {
                // this.is_filtered = true
                // this.is_loading = true;

                this.loadData();
            },
            resetFilter() {
                // kosongkan filter tanggal
                this.filterTanggalAwal = new Date().toISOString().slice(0, 10);
                this.filterTanggalAkhir = new Date().toISOString().slice(0, 10)
                this.loadData()
                // ambil ulang semua data tanpa filter

            },
            formatTanggalIndo(tanggal) {
                const options = {
                    timeZone: "Asia/Makassar",
                    day: "2-digit",
                    month: "long",
                    year: "numeric",
                    hour: "2-digit",
                    minute: "2-digit"
                };
                return new Date(tanggal).toLocaleString("id-ID", options);
            },

            async loadRekap() {
                axios.get('/api/master/rekap-data').then(res => {
                    console.log(res);
                    this.rekap = res.data
                });
                axios.get('/api/latest-laporan').then(res => {
                    console.log(res);
                    this.latestReports = res.data
                });
                axios.get('/api/latest-issues').then(res => {
                    console.log(res);
                    this.latestIssues = res.data
                });

            },
            async loadData() {
                let token = localStorage.getItem("token");

                let dis = await axios.get('/api/user-dis');

                let items = [];
                let seen = new Set();

                for (let di of dis.data) {
                    let url = di.has_upi ?
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&upi_valid=1` :
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1`;
                    if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                    if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                    let res = await axios.get(url);
                    console.log(res);

                    for (let d of res.data) {
                        if (!seen.has(d.id)) {
                            seen.add(d.id);
                            items.push(d);
                        }
                    }
                }

                console.log(items);

                this.items = items;
                this.filteredItems = items;
            }


        },
        mounted() {
            //ambil data dari API
            this.loadData()
            this.loadRekap()
        }
    }).mount('#app');
</script>

@endpush