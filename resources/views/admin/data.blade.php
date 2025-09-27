@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title">Data Pengisian Form Petugas</h5>
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
            <div class="table-responsive">

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pantau</th>
                            <th>Lokasi</th>
                            <!-- <th>Kec / Desa</th> -->
                            <th>Petugas</th>
                            <th>Saluran / Bangunan / Petak</th>
                            <th>Debit Air</th>
                            <th>Masa Tanam</th>
                            <th>Luas</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in filteredItems" :key="item.id">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ formatTanggal(item.tanggal_pantau) }}</td>
                            <td>@{{ item.kabupaten?.nama ?? '-' }}<br>
                                <span class="badge bg-success">@{{ item.daerah_irigasi?.nama ?? '-' }}</span>
                            </td>
                            <!-- <td>Kec : @{{ item.kecamatan }}<br>
                            Desa : @{{ item.desa }}</td> -->

                            <td>@{{ item.petugas?.nama ?? '-' }}</td>
                            <td>@{{ item.saluran.nama }} / @{{ item.bangunan.nama }} / @{{ item.petak.nama }}</td>
                            <td>@{{ item.debit_air }}</td>
                            <td>@{{ item.masa_tanam }}</td>
                            <td><span class="badge bg-info">Padi : @{{ item.luas_padi }}</span> <br>
                                <span class="badge bg-secondary">Palawija : @{{ item.luas_palawija }}</span><br>
                                <span class="badge bg-dark">Lainnya : @{{ item.luas_lainnya }}</span>
                            </td>
                            <td>
                                <img @click="showForm(item)" v-if="item.foto_pemantauan"
                                    :src="'/storage/' + item.foto_pemantauan" class="img-thumbnail"
                                    style="max-width: 80px;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formLTTModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Form Pemantauan LTT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Header -->
                    <h4 class="text-center mb-3">FORM PEMANTAUAN LUAS TAMBAH TANAM (LTT)</h4>

                    <!-- Identitas -->
                    <div class="mb-3">
                        <div class="form-line"><span>Nama Petugas OP</span>:
                            @{{ item.petugas ? item.petugas.nama : '-' }}</div>
                        <div class="form-line"><span>Tanggal Pemantauan</span>:
                            @{{ formatTanggal(item.tanggal_pantau) }}</div>
                        <div class="form-line"><span>Daerah Irigasi</span>: DI
                            @{{ item.daerah_irigasi ? item.daerah_irigasi.nama : '-' }}</div>
                        <div class="form-line"><span>Desa/Kelurahan</span>: @{{item.desa}}</div>
                        <div class="form-line"><span>Kecamatan</span>: @{{item.kecamatan}}</div>
                        <div class="form-line"><span>Kabupaten/Kota</span>:
                            @{{item.kabupaten ? item.kabupaten.nama : '-'}}
                        </div>
                        <div class="form-line"><span>Nama Saluran (Sekunder/Primer)</span>:
                            @{{item.saluran ? item.saluran.nama : '-'}}</div>
                        <div class="form-line"><span>Nama Bangunan Bagi/Sadap</span>:
                            @{{item.bangunan ? item.bangunan.nama : '-'}}</div>
                        <div class="form-line"><span>Kode/Nama Petak Layanan</span>:
                            @{{item.petak ? item.petak.nama : '-'}}
                        </div>
                        <div class="form-line"><span>Koordinat Bangunan Bagi/Sadap</span>: @{{item.koordinat}}
                        </div>
                    </div>

                    <!-- Tabel -->
                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Debit Air (lt/detik)</th>
                                    <th rowspan="2">Luas Petak Skema (Ha)</th>
                                    <th colspan="3">Pemantauan Luas Tambah Tanam (LTT)</th>
                                </tr>
                                <tr>
                                    <th>Padi (Ha)</th>
                                    <th>Palawija (Ha)</th>
                                    <th>Lainnya (Ha)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td height="50px">@{{item.debit_air}}</td>
                                    <td>@{{item.petak ? item.petak.luas : '-'}}</td>
                                    <td>@{{item.luas_padi}}</td>
                                    <td>@{{item.luas_palawija}}</td>
                                    <td>@{{item.luas_lainnya}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">

                        <!-- Tabel -->
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Pemantauan Permasalahan</th>
                                    <th>Ada/Tidak</th>
                                    <th>Ketarngan</th>
                                </tr>
                            </thead>
                            <tbody v-for="(p,index) in item.permasalahan" :key="p.id">
                                <tr>
                                    <td>@{{ index}}</td>
                                    <td>@{{ p.master_permasalahan.nama }}
                                    </td>
                                    <td class="text-center">
                                        <span v-if="p.status==1">Ada</span>
                                    </td>
                                    <td>@{{ p.keterangan}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h4>Foto Pemantauan</h4>
                    <img v-if="modalInstance" :src="`/storage/${item.foto_pemantauan}`" width="100%">

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
<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                items: [], // data asli
                item: {}, //detail
                filteredItems: [], // data hasil filter
                filterTanggalAwal: '',
                filterTanggalAkhir: ''
            }
        },
        mounted() {
            this.loadData();
        },
        methods: {
            async loadData() {
                let token = localStorage.getItem("token");

                let dis = await axios.get('/api/user-dis');
                console.log(dis.data);

                let items = [];
                let seen = new Set();

                for (let di of dis.data) {
                    let url = di.has_upi ?
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&upi_valid=1` :
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1`;
                    console.log(url);

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
            },
            showForm(form) {
                this.item = form;
                console.log(this.item);

                const modalEl = document.getElementById('formLTTModal');
                this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                this.modalInstance.show();
            },
            applyFilter() {
                const awal = this.filterTanggalAwal ? new Date(this.filterTanggalAwal) : null;
                const akhir = this.filterTanggalAkhir ? new Date(this.filterTanggalAkhir) : null;

                this.filteredItems = this.items.filter(item => {
                    const tgl = new Date(item.tanggal_pantau);
                    if (awal && tgl < awal) return false;
                    if (akhir && tgl > akhir) return false;
                    return true;
                });
            },
            syncTanggal() {
                // kalau user pilih tanggal awal, otomatis set tanggal akhir sama
                this.filterTanggalAkhir = this.filterTanggalAwal;
            },
            resetFilter() {
                this.filterTanggalAwal = '';
                this.filterTanggalAkhir = '';
                this.filteredItems = this.items;
            },
            formatTanggal(tgl) {
                if (!tgl) return '-';

                // Format ke 17 September 2025
                return new Date(tgl).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            },

        }
    }).mount('#app');
</script>
@endpush