@extends('admin.template')

@section('content')
<div id="app" v-cloak class="p-4">
    <div class="card h-100">
        <div class="card-body">
            <h2>📊 Dashboard Rekap Laporan</h2>

            <!-- Filter tanggal -->
            <div class="mb-3 d-flex flex-wrap gap-2 col-12 col-md-6">
                <input type="date" v-model="filterTanggalAwal" @change="syncTanggal" class="form-control flex-grow-1" />
                <input type="date" v-model="filterTanggalAkhir" class="form-control flex-grow-1" />
                <button class="btn btn-primary">Filter</button>
                <button class="btn btn-secondary">Reset</button>
            </div>

            <!-- Ringkasan umum -->
            <div class="row text-center mb-3">
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Laporan</h6>
                        <h4>@{{ filteredItems.length }}</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Luas Padi</h6>
                        <h4>@{{ totalLuas.padi.toFixed(2) }} ha</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Luas Palawija</h6>
                        <h4>@{{ totalLuas.palawija.toFixed(2) }} ha</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Luas Lainnya</h6>
                        <h4>@{{ totalLuas.lainnya.toFixed(2) }} ha</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm p-3">
                        <h6>Total Semua</h6>
                        <h4>@{{ totalLuas.total.toFixed(2) }} ha</h4>
                    </div>
                </div>
            </div>
            <h4>Rekap Per Daerah Irigasi</h4>
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Daerah Irigasi</th>
                            <th>Padi (ha)</th>
                            <th>Palawija (ha)</th>
                            <th>Lainnya (ha)</th>
                            <th>Total (ha)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, namaDI) in rekapPerDaerahIrigasi" :key="namaDI">
                            <td>@{{ namaDI }}</td>
                            <td>@{{ data.padi.toFixed(2) }}</td>
                            <td>@{{ data.palawija.toFixed(2) }}</td>
                            <td>@{{ data.lainnya.toFixed(2) }}</td>
                            <td>@{{ data.total.toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Rekap per petugas -->
            <h4 class="mt-5">📌 Rekap per Petugas</h4>
            <div class="table-responsive">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Petugas</th>
                            <th>Luas Padi</th>
                            <th>Luas Palawija</th>
                            <th>Luas Lainnya</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(rekap, nama) in rekapPerPetugas" :key="nama">
                            <td>@{{ nama }}</td>
                            <td>@{{ rekap.padi.toFixed(2) }}</td>
                            <td>@{{ rekap.palawija.toFixed(2) }}</td>
                            <td>@{{ rekap.lainnya.toFixed(2) }}</td>
                            <td>@{{ rekap.total.toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Chart -->
            <h4 class="mt-5">📈 Grafik Per DI</h4>
            <canvas id="chartDI" height="100"></canvas>
            <h4 class="mt-5">📈 Grafik Per Item</h4>
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
                    lainnya = 0;
                this.filteredItems.forEach(i => {
                    padi += parseFloat(i.luas_padi);
                    palawija += parseFloat(i.luas_palawija);
                    lainnya += parseFloat(i.luas_lainnya);
                });
                return {
                    padi,
                    palawija,
                    lainnya,
                    total: padi + palawija + lainnya
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
                            total: 0
                        };
                    }
                    rekap[nama].padi += parseFloat(i.luas_padi);
                    rekap[nama].palawija += parseFloat(i.luas_palawija);
                    rekap[nama].lainnya += parseFloat(i.luas_lainnya);
                    rekap[nama].total += parseFloat(i.luas_padi) + parseFloat(i.luas_palawija) + parseFloat(
                        i.luas_lainnya);
                });
                return rekap;
            },
            rekapPerDaerahIrigasi() {
                const rekap = {};
                this.filteredItems.forEach(i => {
                    const namaDI = i.daerah_irigasi?.nama || 'Tidak Ada DI';
                    if (!rekap[namaDI]) {
                        rekap[namaDI] = {
                            padi: 0,
                            palawija: 0,
                            lainnya: 0,
                            total: 0
                        };
                    }
                    rekap[namaDI].padi += parseFloat(i.luas_padi);
                    rekap[namaDI].palawija += parseFloat(i.luas_palawija);
                    rekap[namaDI].lainnya += parseFloat(i.luas_lainnya);
                    rekap[namaDI].total += parseFloat(i.luas_padi) + parseFloat(i.luas_palawija) +
                        parseFloat(i.luas_lainnya);
                });
                return rekap;
            }
        },
        methods: {
            syncTanggal() {
                // kalau user pilih tanggal awal, otomatis set tanggal akhir sama
                this.filterTanggalAkhir = this.filterTanggalAwal;
            },
            applyFilter() {
                if (!this.filterTanggalAwal || !this.filterTanggalAkhir) {
                    this.filteredItems = this.items;
                } else {
                    this.filteredItems = this.items.filter(i =>
                        i.tanggal_pantau >= this.filterTanggalAwal &&
                        i.tanggal_pantau <= this.filterTanggalAkhir
                    );
                }
                this.chartPerDI();
                this.chartPerItem();
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

                    let res = await axios.get(url);

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