@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label fw-bold">Daerah Irigasi</label>
                    <select v-model="filterDi" class="form-select">
                        <option value="">-- Pilih DI --</option>
                        <option v-for="di in daftarDI" :value="di.id">
                            @{{ di.nama }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Mulai</label>
                    <input type="date" v-model="tglMulai" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Selesai</label>
                    <input type="date" v-model="tglSelesai" class="form-control">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100" @click="loadData">
                        Tampilkan
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body table-responsive">

            <h5 class="fw-bold mb-3">Rekap Tertinggi per Masa Tanam</h5>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Masa Tanam</th>
                        <th>Tanggal Minggu</th>
                        <th>Total Luas</th>
                        <th>Padi</th>
                        <th>Palawija</th>
                        <th>Lainnya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, i) in topPerMt" :key="i" class="table-success fw-bold">
                        <td>@{{ i + 1 }}</td>
                        <td>@{{ row.masa_tanam }}</td>
                        <td>@{{ row.tanggal_minggu }}</td>
                        <td>@{{ formatAngka(row.total_luas) }}</td>
                        <td>@{{ formatAngka(row.padi) }}</td>
                        <td>@{{ formatAngka(row.palawija) }}</td>
                        <td>@{{ formatAngka(row.lainnya) }}</td>
                    </tr>

                    <tr v-if="topPerMt.length === 0">
                        <td colspan="7" class="text-center text-muted">
                            Belum ada data
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Minggu</th>
                        <th>Masa Tanam</th>

                        <th>Total Luas</th>
                        <th>Padi</th>
                        <th>Palawija</th>
                        <th>Lainnya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in items"
                        :key="index"
                        style="cursor:pointer"
                        @click="openDetail(row.tanggal_minggu)"
                        :class="row.total_luas == maxPerMt[row.masa_tanam] ? 'table-success fw-bold' : ''">


                        <td>@{{ index + 1 }}</td>
                        <td>@{{ row.tanggal_minggu }}</td>
                        <td>@{{ row.masa_tanam }}</td>

                        <td>
                            @{{ formatAngka(row.total_luas) }}
                            <span
                                v-if="row.total_luas == maxPerMt[row.masa_tanam]"
                                class="badge bg-success ms-1">
                                TERTINGGI MT
                            </span>
                        </td>

                        <td>@{{ formatAngka(row.padi) }}</td>
                        <td>@{{ formatAngka(row.palawija) }}</td>
                        <td>@{{ formatAngka(row.lainnya) }}</td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td colspan="6" class="text-center text-muted">
                            Belum ada data
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <!-- MODAL DETAIL -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Detail Pengisian - @{{ selectedTanggal }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body table-responsive">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Padi</th>
                                <th>Palawija</th>
                                <th>Lainnya</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, i) in detailItems" :key="row.id">
                                <td>@{{ i+1 }}</td>
                                <td>@{{ row.tanggal_pantau }}</td>
                                <td>@{{ row.luas_padi }}</td>
                                <td>@{{ row.luas_palawija }}</td>
                                <td>@{{ row.luas_lainnya }}</td>
                                <td>
                                    @{{ Number(row.luas_padi) + Number(row.luas_palawija) + Number(row.luas_lainnya) }}
                                </td>
                            </tr>
                            <!-- TOTAL KESELURUHAN -->
                            <tr v-if="detailItems.length > 0" class="fw-bold table-success">
                                <td colspan="2" class="text-end">TOTAL</td>
                                <td>@{{ formatAngka(totalDetail.padi) }}</td>
                                <td>@{{ formatAngka(totalDetail.palawija) }}</td>
                                <td>@{{ formatAngka(totalDetail.lainnya) }}</td>
                                <td>@{{ formatAngka(totalDetail.total) }}</td>
                            </tr>

                            <tr v-if="detailItems.length === 0">
                                <td colspan="6" class="text-center text-muted">
                                    Tidak ada data
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                daftarDI: [],
                filterDi: '',
                tglMulai: '',
                tglSelesai: '',
                items: [],
                detailItems: [],
                selectedTanggal: '',
                detailModal: null,
                totalDetail: {
                    padi: 0,
                    palawija: 0,
                    lainnya: 0,
                    total: 0
                },
                maxPerMt: {},
                topPerMt: []



            }
        },

        mounted() {
            this.loadDI();
        },

        methods: {
            async loadDI() {
                const res = await axios.get('/api/master/daerah-irigasi?page=all');
                this.daftarDI = res.data.data;
            },

            async loadData() {
                if (!this.filterDi || !this.tglMulai || !this.tglSelesai) {
                    alert('Lengkapi filter dulu');
                    return;
                }

                let url = `/api/rekap-mingguan?di_id=${this.filterDi}&tanggal_mulai=${this.tglMulai}&tanggal_selesai=${this.tglSelesai}`;
                const res = await axios.get(url);
                this.items = res.data;
                // Reset
                this.maxPerMt = {};
                this.topPerMt = [];

                // Hitung nilai tertinggi per MT
                this.items.forEach(row => {
                    const mt = row.masa_tanam;
                    const total = Number(row.total_luas);

                    if (!this.maxPerMt[mt]) {
                        this.maxPerMt[mt] = total;
                    } else {
                        if (total > this.maxPerMt[mt]) {
                            this.maxPerMt[mt] = total;
                        }
                    }
                });

                // Ambil hanya data tertinggi per MT
                Object.keys(this.maxPerMt).forEach(mt => {
                    const row = this.items.find(r =>
                        r.masa_tanam === mt &&
                        Number(r.total_luas) === this.maxPerMt[mt]
                    );

                    if (row) {
                        this.topPerMt.push(row);
                    }
                });

                // reset
                this.maxPerMt = {};

                // hitung max per masa tanam
                this.items.forEach(row => {
                    const mt = row.masa_tanam;

                    if (!this.maxPerMt[mt]) {
                        this.maxPerMt[mt] = Number(row.total_luas);
                    } else {
                        if (Number(row.total_luas) > this.maxPerMt[mt]) {
                            this.maxPerMt[mt] = Number(row.total_luas);
                        }
                    }
                });

            },


            formatAngka(val) {
                return Number(val).toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
            },

            async openDetail(tanggal) {
                this.selectedTanggal = tanggal;

                let url = `/api/rekap-mingguan-detail?di_id=${this.filterDi}&tanggal=${tanggal}`;
                const res = await axios.get(url);

                this.detailItems = res.data;

                // reset total
                let padi = 0;
                let palawija = 0;
                let lainnya = 0;

                // hitung total
                this.detailItems.forEach(row => {
                    padi += Number(row.luas_padi);
                    palawija += Number(row.luas_palawija);
                    lainnya += Number(row.luas_lainnya);
                });

                let total = padi + palawija + lainnya;

                this.totalDetail = {
                    padi: padi,
                    palawija: palawija,
                    lainnya: lainnya,
                    total: total
                };

                // buka modal
                const modalEl = document.getElementById('detailModal');
                this.detailModal = new bootstrap.Modal(modalEl);
                this.detailModal.show();
            },



        }
    }).mount('#app');
</script>
@endpush