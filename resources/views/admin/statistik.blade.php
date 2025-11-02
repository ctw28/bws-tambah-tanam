@extends('admin.template')

@section('content')
<div id="app" v-cloak class="p-4">
    <h2>📊 BASISDATA HASIL PEMANTAUAN LUAS TANAM</h2>
    <!-- Filter tanggal -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <!-- Pilih DI -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold">Daerah Irigasi</label>
                    <select class="form-select form-select" v-model="filterDI">
                        <option value="">-- Pilih DI --</option>
                        <option v-for="d in daerahIrigasis" :value="d.id">@{{ d.nama }}</option>
                    </select>
                </div>

                <!-- Tanggal awal -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold">Tanggal Awal</label>
                    <input type="date" v-model="filterTanggalAwal" @change="syncTanggal" class="form-control form-control" />
                </div>

                <!-- Tanggal akhir -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold">Tanggal Akhir</label>
                    <input type="date" v-model="filterTanggalAkhir" class="form-control form-control" />
                </div>

                <!-- Tombol -->
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn w-100" @click="applyFilter">
                        <span v-if="is_loading" class="spinner-border spinner-border me-1"></span>
                        <span v-else>Filter</span>
                    </button>
                    <button class="btn btn-secondary btn w-100" @click="resetFilter">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isFilter">
        <div class="card h-100">
            <div class="card-body">

                <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-8">
                    <div class="flex-grow-1 mt-2">
                        <div class="user-profile-info">
                            <h4 class="mb-2">Daerah Irigasi @{{selectedDI.nama}} - Kab. @{{selectedDI.kabupatens[0].nama}}</h4>
                            <!-- <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 mt-4">
                                <li class="list-inline-item"><i class="icon-base bx bx-palette me-2 align-top"></i><span class="fw-medium"> Luas Baku</span></li>
                                <li class="list-inline-item"><i class="icon-base bx bx-palette me-2 align-top"></i><span class="fw-medium">@{{ selectedDI.luas_fungsional }} ha Luas Fungsional</span></li>
                                <li class="list-inline-item"><i class="icon-base bx bx-palette me-2 align-top"></i><span class="fw-medium">@{{ selectedDI.luas_potensial }} ha Luas Potensial</span></li>
                            </ul> -->

                            <div class="row mt-4">
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-warning p-2"><i class="icon-base bx bx-git-branch icon-lg text-warning"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{rekap.total_saluran}}</h6>
                                        <small>Saluran</small>
                                    </div>
                                </div>
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-success p-2"><i class="icon-base bx bx-building icon-lg text-success"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{rekap.total_bangunan}}</h6>
                                        <small>Bangunan</small>
                                    </div>
                                </div>
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-info p-2"><i class="icon-base bx bx-traffic-cone icon-lg text-info"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{rekap.total_petak}}</h6>
                                        <small>Petak</small>
                                    </div>
                                </div>
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-secondary p-2"><i class="icon-base bx bx-bullseye icon-lg text-secondary"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{rekap.total_pengamat}}</h6>
                                        <small>Pengamat</small>
                                    </div>
                                </div>
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-dark p-2"><i class="icon-base bx bx-user icon-lg text-dark"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{rekap.total_juru}}</h6>
                                        <small>Juru</small>
                                    </div>
                                </div>
                            </div>
                            <h5 class="mt-4">Informasi Luas Daerah Irigasi</h5>
                            <div class="row mt-4">
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-warning p-2"><i class="icon-base bx bx-water icon-lg text-warning"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{ selectedDI.luas_baku }} ha</h6>
                                        <small>Luas Baku</small>
                                    </div>
                                </div>
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-success p-2"><i class="icon-base bx bx-water icon-lg text-success"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{ selectedDI.luas_fungsional }} ha</h6>
                                        <small>Luas Fungsional</small>
                                    </div>
                                </div>
                                <div class="col d-flex">
                                    <div class="me-3">
                                        <span class="badge rounded-2 bg-label-info p-2"><i class="icon-base bx bx-water icon-lg text-info"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">@{{ selectedDI.luas_potensial }} ha</h6>
                                        <small>Luas Potensial</small>
                                    </div>
                                </div>
                                <!-- <div class="col d-flex">
                                </div>
                                <div class="col d-flex">
                                </div> -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Ringkasan umum -->
        <div class="card h-100 mt-3">
            <div class="card-body">

                <div class="row g-3 mb-4">
                    <h4 class="mt-4">Basisdata Hasil Pemantauan</h4>

                    <!-- Total Laporan Juru -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bx-file icon-lg"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0">@{{ filteredItems.length }}</h4>
                                </div>
                                <p class="mb-0 text-muted fw-semibold">Laporan Juru Tervalidasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="bx bx-file icon-lg"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0">@{{ rekapLuasTotal.padi }} ha</h4>
                                </div>
                                <p class="mb-0 text-muted fw-semibold">Luas Tanam Padi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="bx bx-file icon-lg"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0">@{{ rekapLuasTotal.palawija }} ha</h4>
                                </div>
                                <p class="mb-0 text-muted fw-semibold">Luas Tanam Palawija</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-file icon-lg"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0">@{{ rekapLuasTotal.lainnya }} ha</h4>
                                </div>
                                <p class="mb-0 text-muted fw-semibold">Luas Tanam Lainnya</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-dark">
                                            <i class="bx bx-file icon-lg"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0">@{{ rekapLuasTotal.total }} ha</h4>
                                </div>
                                <p class="mb-0 text-muted fw-semibold">Luas Keseluruhan</p>
                            </div>
                        </div>
                    </div>


                </div>
                <h4>Rekap Pengisian Luas Tanam</h4>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Saluran</th>
                                <th>Luas Padi (ha)</th>
                                <th>Luas Palawija (ha)</th>
                                <th>Luas Lainnya (ha)</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(p, index) in rekapLuasTanam" :key="p.id">
                                <td>@{{ (pagination.current - 1) * perPage + index + 1 }}</td>
                                <td>
                                    @{{ p.saluran}} - @{{ p.bangunan }} - @{{ p.petak }}<br>
                                    <small>Terakhir update : @{{ p.tanggal_update!='-'? formatTanggalIndo(p.tanggal_update) : 'belum ada update'}}</small>
                                </td>
                                <td>@{{ p.padi }}</td>
                                <td>@{{ p.palawija }}</td>
                                <td>@{{ p.lainnya }}</td>
                                <td>@{{ p.total }}</td>
                            </tr>
                            <tr v-if="rekapLuasTanam.length === 0">
                                <td colspan="4" class="text-center text-muted">Belum ada permasalahan</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <!-- Pilih jumlah data per halaman -->
                        <div class="d-flex align-items-center gap-2">
                            <select v-model="perPage" @change="loadMaks(1)" class="form-select form-select" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span>per halaman</span>
                        </div>


                        <!-- Navigasi Pagination -->
                        <nav>
                            <ul class="pagination pagination mb-0">
                                <li class="page-item" :class="{ disabled: pagination.current === 1 }">
                                    <a class="page-link" href="#" @click.prevent="loadMaks(pagination.current - 1)">Prev</a>
                                </li>

                                <li v-for="page in pagination.last" :key="page" class="page-item" :class="{ active: page === pagination.current }">
                                    <a class="page-link" href="#" @click.prevent="loadMaks(page)">@{{ page }}</a>
                                </li>

                                <li class="page-item" :class="{ disabled: pagination.current === pagination.last }">
                                    <a class="page-link" href="#" @click.prevent="loadMaks(pagination.current + 1)">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <h4 class="mt-4">Permasalahan</h4>
                <div class="table-responsive">
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
                                <td>@{{ p.master_permasalahan?.id }}. @{{ p.master_permasalahan?.nama }} : p.keterangan</td>
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

                <h4 class="mt-5">📈 Informasi Grafis Jenis Tanaman</h4>
                <canvas id="chartItem" height="100"></canvas>
            </div>
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
                daerahIrigasis: [],
                isFilter: false,
                filterDI: '',
                is_loading: false,
                rekapLuasTanam: [],
                latestIssues: [],
                selectedDI: '',
                pagination: {
                    current: 1,
                    last: 1,
                    total: 0,
                },
                perPage: 10, // default
                rekapLuasTotal: [],

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
            async loadDI() {
                let res = await axios.get('/api/master/daerah-irigasi?page=all');
                console.log(res.data.data);
                this.daerahIrigasis = res.data.data;
            },
            syncTanggal() {
                this.filterTanggalAkhir = this.filterTanggalAwal;
            },
            applyFilter() {
                this.selectedDI = this.daerahIrigasis.find(d => d.id === this.filterDI) || null;
                this.loadData()
                this.isFilter = true

            },
            resetFilter() {
                // kosongkan filter tanggal
                this.filterDI = ''
                this.filterTanggalAwal = ''
                this.filterTanggalAkhir = ''
                this.isFilter = false
            },
            chartPerDI() {
                const labels = Object.keys(this.rekapPerDaerahIrigasi);
                const dataTotal = Object.values(this.rekapPerDaerahIrigasi).map(r => r.total);

                if (this.chartDI) this.chartDI.destroy();
                this.chartDI = new Chart(document.getElementById('chartDI'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Total Luas (ha)',
                            data: dataTotal
                        }]
                    }
                });
            },
            chartPerItem() {
                const rekap = this.rekapPerDaerahIrigasi; // fungsi yg sudah dibuat
                const labels = Object.keys(rekap);

                const dataPadi = Object.values(rekap).map(r => r.padi);
                const dataPalawija = Object.values(rekap).map(r => r.palawija);
                const dataLainnya = Object.values(rekap).map(r => r.lainnya);

                if (this.chartItem) this.chartItem.destroy();

                this.chartItem = new Chart(document.getElementById('chartItem'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                                label: 'Padi (ha)',
                                data: dataPadi,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)'
                            },
                            {
                                label: 'Palawija (ha)',
                                data: dataPalawija,
                                backgroundColor: 'rgba(255, 205, 86, 0.6)'
                            },
                            {
                                label: 'Lainnya (ha)',
                                data: dataLainnya,
                                backgroundColor: 'rgba(201, 90, 90, 0.6)'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Rekap Luas Per Daerah Irigasi'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Luas (ha)'
                                }
                            }
                        }
                    }
                });
            },
            async loadRekap() {
                let url = `/api/master/rekap-data?di_id=${this.filterDI}`
                axios.get(url).then(res => {
                    console.log(res);
                    this.rekap = res.data
                });

                axios.get(`/api/latest-issues?di_id=${this.filterDI}`).then(res => {
                    this.latestIssues = res.data
                    console.log(this.latestIssues);
                });
                this.loadMaks(1)

            },
            async loadMaks(page = 1) {
                // alert(page)
                let url = `/api/rekap-petak?di_id=${this.filterDI}&page=${page}&per_page=${this.perPage}`
                if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                axios.get(url).then(res => {
                    console.log(res.data);
                    this.rekapLuasTanam = res.data.data
                    this.pagination = {
                        current: res.data.current_page,
                        last: res.data.last_page,
                        total: res.data.total,
                    };
                });

                url = `/api/rekap-di?di_id=${this.filterDI}`
                if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                axios.get(url).then(res => {
                    console.log(res.data);
                    this.rekapLuasTotal = res.data.total_luas
                });
            },
            formatTanggalIndo(tanggal) {
                const options = {
                    timeZone: "Asia/Makassar",
                    day: "2-digit",
                    month: "long",
                    year: "numeric",
                    // hour: "2-digit",
                    // minute: "2-digit"
                };
                return new Date(tanggal).toLocaleString("id-ID", options);
            },
            async loadData() {
                let url = `/api/form-pengisian?page=all&di_id=${this.filterDI}`
                if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                let res = await axios.get(url);
                this.items = res.data.data;
                this.filteredItems = res.data;
                console.log(this.filteredItems);
                this.loadRekap()
                this.chartPerItem();
            }
        },
        mounted() {
            //ambil data dari API
            this.loadDI()
        }
    }).mount('#app');
</script>

@endpush