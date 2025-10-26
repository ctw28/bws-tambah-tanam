@extends('admin.template')

@section('content')
<div id="app" v-cloak class="p-4">
    <div class="card h-100">
        <div class="card-body">
            <h2>📊 Informasi Hasil Pemantauan Luas Tanam</h2>
            <!-- Filter tanggal -->
            <div class="mb-3">
                <div class="row g-2">
                    <!-- Input tanggal awal -->
                    <div class="col-6 col-md-3">
                        <input type="date" v-model="filterTanggalAwal" @change="syncTanggal" class="form-control" />
                    </div>
                    <!-- Input tanggal akhir -->
                    <div class="col-6 col-md-3">
                        <input type="date" v-model="filterTanggalAkhir" class="form-control" />
                    </div>
                    <!-- Tombol (hanya di layar md ke atas) -->
                    <div class="col-md-6 d-none d-md-flex gap-2">
                        <button class="btn btn-primary " @click="applyFilter">Filter</button>
                        <button class="btn btn-secondary" @click="resetFilter">Reset</button>
                    </div>
                </div>

                <!-- Tombol (khusus HP, tampil di bawah input tanggal) -->
                <div class="d-flex gap-2 mt-2 d-md-none">
                    <button class="btn btn-primary " @click="applyFilter">Filter</button>
                    <button class="btn btn-secondary btn-sm" @click="resetFilter">Reset</button>
                </div>
            </div>
            <!-- Ringkasan umum -->
            <div class="row text-center mb-3">
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Laporan Juru Tervalidasi</h6>
                        <h4>@{{ filteredItems.length }}</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Luas Tanam Padi</h6>
                        <h4>@{{ totalLuas.padi }} ha</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Luas Tanam Palawija</h6>
                        <h4>@{{ totalLuas.palawija }} ha</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Luas Tanam Lainnya</h6>
                        <h4>@{{ totalLuas.lainnya }} ha</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Luas Tanam</h6>
                        <h4>@{{ totalLuas.total }} ha</h4>
                    </div>
                </div>
            </div>
            <h4>Rekap Daerah Irigasi</h4>
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Daerah Irigasi</th>
                            <th>Padi (ha)</th>
                            <th>Palawija (ha)</th>
                            <th>Lainnya (ha)</th>
                            <th>Total Luas Tanam (ha)</th>
                            <th>Baku</th>
                            <th>Potensial</th>
                            <th>Fungsional</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, namaDI,index) in rekapPerDaerahIrigasi" :key="namaDI">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ namaDI }}</td>
                            <td>@{{ data.padi }}</td>
                            <td>@{{ data.palawija }}</td>
                            <td>@{{ data.lainnya }}</td>
                            <td>@{{ data.total }}</td>
                            <td>@{{ data.baku }}</td>
                            <td>@{{ data.potensial }}</td>
                            <td>@{{ data.fungsional }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Rekap per petugas -->
            <!-- <h4 class="mt-5">📌 Rekap per Petugas</h4>
            <div class="table-responsive">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Petugas</th>
                            <th>Debit Air</th>
                            <th>Luas Padi</th>
                            <th>Luas Palawija</th>
                            <th>Luas Lainnya</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(rekap, nama) in rekapPerPetugas" :key="nama">
                            <td>@{{ nama }}</td>
                            <td>@{{ rekap.debit_air.toFixed(2) }}</td>
                            <td>@{{ rekap.padi.toFixed(2) }}</td>
                            <td>@{{ rekap.palawija.toFixed(2) }}</td>
                            <td>@{{ rekap.lainnya.toFixed(2) }}</td>
                            <td>@{{ rekap.total.toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div> -->
            <!-- Chart -->
            <h4 class="mt-5">📈 Informasi Grafis Luas Tanam Daerah Irigasi : </h4>
            <canvas id="chartDI" height="100"></canvas>
            <h4 class="mt-5">📈 Infromasi Grafis Jenis Tanaman</h4>
            <canvas id="chartItem" height="100"></canvas>
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
                chartItem: null
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
            formatNumber(value) {
                const num = Number(value);


                return num.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },


            rekapPerDaerahIrigasi() {
                const rekap = {};
                const format = (n) =>
                    new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(Number(n) || 0);

                this.filteredItems.forEach(i => {
                    const namaDI = i.daerah_irigasi?.nama || 'Tidak Ada DI';

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

                    // Pastikan semua nilai numerik valid
                    const baku = Number(i.daerah_irigasi?.luas_baku) || 0;
                    const potensial = Number(i.daerah_irigasi?.luas_potensial) || 0;
                    const fungsional = Number(i.daerah_irigasi?.luas_fungsional) || 0;
                    const padi = Number(i.luas_padi) || 0;
                    const palawija = Number(i.luas_palawija) || 0;
                    const lainnya = Number(i.luas_lainnya) || 0;

                    rekap[namaDI].baku = baku;
                    rekap[namaDI].potensial = potensial;
                    rekap[namaDI].fungsional = fungsional;
                    rekap[namaDI].padi += padi;
                    rekap[namaDI].palawija += palawija;
                    rekap[namaDI].lainnya += lainnya;
                    rekap[namaDI].total += padi + palawija + lainnya;
                });

                // Format hasil akhir agar sudah siap ditampilkan
                Object.keys(rekap).forEach(namaDI => {
                    rekap[namaDI] = {
                        baku: format(rekap[namaDI].baku),
                        potensial: format(rekap[namaDI].potensial),
                        fungsional: format(rekap[namaDI].fungsional),
                        padi: format(rekap[namaDI].padi),
                        palawija: format(rekap[namaDI].palawija),
                        lainnya: format(rekap[namaDI].lainnya),
                        total: format(rekap[namaDI].total)
                    };
                });

                return rekap;
            },

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
            async loadData2() {
                axios.get('/api/form-pengisian?pengamat_valid=1&upi_valid=1').then(res => {
                    this.items = res.data;
                    this.filteredItems = res.data;
                    this.chartPerDI();
                    this.chartPerItem();
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
                this.chartPerDI();
                this.chartPerItem();
            }


        },
        mounted() {
            //ambil data dari API
            this.loadData()
        }
    }).mount('#app');
</script>

@endpush